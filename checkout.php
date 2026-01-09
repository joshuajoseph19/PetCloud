<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = false;

// --- AUTO-FIX: Create Tables If Missing ---
try {
    $pdo->query("SELECT 1 FROM orders LIMIT 1");
} catch (PDOException $e) {
    include 'setup_orders_db.php';
}

// --- Fetch Items for Summary ---
$sql = "SELECT c.quantity, p.* 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// --- Handle Order Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $zip = $_POST['zip_code'] ?? '';

    try {
        $pdo->beginTransaction();

        // 1. Insert into Orders
        $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, city, zip_code) VALUES (?, ?, ?, ?, ?)");
        $stmtOrder->execute([$user_id, $total, $address, $city, $zip]);
        $order_id = $pdo->lastInsertId();

        // 2. Insert into Order Items
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmtItem->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        // 3. Clear Cart
        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

        $pdo->commit();
        $success = true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Order failed: " . $e->getMessage());
    }
}

if (!$success && empty($cartItems)) {
    header("Location: marketplace.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - PetCloud</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .checkout-container {
            max-width: 1100px;
            margin: 3rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2.5rem;
        }

        .checkout-section {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }

        .form-group input {
            width: 100%;
            padding: 0.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.75rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            border-color: #3b82f6;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .place-order-btn {
            width: 100%;
            padding: 1.25rem;
            background: #111827;
            color: white;
            border: none;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 2rem;
            transition: transform 0.2s;
        }

        .place-order-btn:hover {
            transform: scale(1.02);
        }

        .success-card {
            text-align: center;
            max-width: 500px;
            margin: 10vh auto;
            padding: 4rem;
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .check-icon {
            width: 80px;
            height: 80px;
            background: #d1fae5;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2.5rem;
        }
    </style>
</head>

<body class="dashboard-page" style="background: #f9fafb;">

    <?php if ($success): ?>
        <div class="success-card">
            <div class="check-icon"><i class="fa-solid fa-check"></i></div>
            <h1 style="font-family: 'Outfit'; margin-bottom: 1rem;">Order Placed!</h1>
            <p style="color: #6b7280; margin-bottom: 2.5rem;">Thank you for your purchase. We've received your order and are
                getting it ready for your pet!</p>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="dashboard.php" class="btn btn-primary"
                    style="text-decoration: none; padding: 1rem 2rem; background: #111827; color: #fff;">Back to
                    Dashboard</a>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'shop_owner'): ?>
                    <a href="shop-orders.php" class="btn"
                        style="text-decoration: none; padding: 1rem 2rem; background: #10b981; color: #fff; border-radius: 1rem; font-weight: 700;">
                        <i class="fa-solid fa-shop"></i> View in Shop Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div style="max-width: 1100px; margin: 2rem auto; padding: 0 1rem;">
            <a href="cart.php" style="color: #6b7280; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Return
                to Cart</a>
        </div>

        <div class="checkout-container">
            <div class="checkout-section">
                <h2 style="font-family: 'Outfit'; margin-bottom: 2rem;">Shipping Information</h2>
                <form method="POST" id="checkoutForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="fname" required placeholder="John">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lname" required placeholder="Doe">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Shipping Address</label>
                        <input type="text" name="address" required placeholder="123 Pet Lane">
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" required placeholder="San Francisco">
                        </div>
                        <div class="form-group">
                            <label>Zip Code</label>
                            <input type="text" name="zip_code" required placeholder="94103">
                        </div>
                    </div>

                    <h2 style="font-family: 'Outfit'; margin: 3rem 0 1.5rem;">Payment Method</h2>
                    <div
                        style="background: #f8fafc; padding: 1.5rem; border-radius: 1rem; border: 2.5px solid #3b82f6; display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                        <i class="fa-solid fa-credit-card" style="font-size: 1.5rem; color: #3b82f6;"></i>
                        <div style="flex-grow: 1;">
                            <div style="font-weight: 700;">Credit / Debit Card</div>
                            <div style="font-size: 0.85rem; color: #6b7280;">Secure SSL Payment</div>
                        </div>
                        <i class="fa-solid fa-circle-check" style="color: #3b82f6;"></i>
                    </div>

                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" required placeholder="#### #### #### ####">
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" required placeholder="MM / YY">
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="password" required placeholder="***">
                        </div>
                    </div>

                    <input type="hidden" name="place_order" value="1">
                    <button type="submit" class="place-order-btn">Complete Purchase -
                        ₹<?php echo number_format($total, 2); ?></button>
                </form>
            </div>

            <div class="checkout-section" style="height: fit-content;">
                <h3 style="font-family: 'Outfit'; margin-bottom: 1.5rem;">Order Summary</h3>
                <?php foreach ($cartItems as $item): ?>
                    <div class="order-item">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                            <div>
                                <div style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($item['name']); ?>
                                </div>
                                <div style="font-size: 0.75rem; color: #6b7280;">Qty: <?php echo $item['quantity']; ?></div>
                            </div>
                        </div>
                        <div style="font-weight: 700;">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #f3f4f6;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Subtotal</span>
                        <span style="font-weight: 600;">₹<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Shipping</span>
                        <span style="color: #10b981; font-weight: 600;">FREE</span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-top: 1rem; font-size: 1.25rem; font-weight: 800;">
                        <span>Total</span>
                        <span style="color: #10b981;">₹<?php echo number_format($total, 2); ?></span>
                    </div>

                    <button type="button" onclick="document.getElementById('checkoutForm').submit();"
                        class="place-order-btn" style="margin-top: 2rem; background: #10b981;">
                        Place Order Fast
                    </button>
                    <p style="font-size: 0.75rem; color: #6b7280; text-align: center; margin-top: 1rem;">
                        <i class="fa-solid fa-shield-check"></i> 256-bit Secure Encryption
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

</body>

</html>