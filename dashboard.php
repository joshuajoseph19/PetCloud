<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Pet Lover';
$user_pic = $_SESSION['profile_pic'] ?? 'images/default_user.png';

// Handle Actions (Mark as Done / Defer / Cancel Appointment)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $reminder_id = $_POST['reminder_id'];
        if ($_POST['action'] == 'complete') {
            $pdo->prepare("UPDATE health_reminders SET status = 'completed' WHERE id = ? AND user_id = ?")->execute([$reminder_id, $user_id]);
        } elseif ($_POST['action'] == 'defer') {
            $pdo->prepare("UPDATE health_reminders SET status = 'deferred', due_at = DATE_ADD(due_at, INTERVAL 1 HOUR) WHERE id = ? AND user_id = ?")->execute([$reminder_id, $user_id]);
        }
    } elseif (isset($_POST['cancel_appointment'])) {
        $appt_id = $_POST['appointment_id'];
        // Cancel appointment safely
        $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND user_id = ?")->execute([$appt_id, $user_id]);
        
        // Refresh to reflect changes
        header("Location: dashboard.php");
        exit();
    }
}

// Fetch Latest Pending Reminder (for Hero)
$reminderStmt = $pdo->prepare("SELECT * FROM health_reminders WHERE user_id = ? AND status = 'pending' ORDER BY due_at ASC LIMIT 1");
$reminderStmt->execute([$user_id]);
$currentReminder = $reminderStmt->fetch();

// Fetch Feeding Schedules
$feedStmt = $pdo->prepare("SELECT * FROM feeding_schedules WHERE user_id = ? ORDER BY feeding_time ASC");
$feedStmt->execute([$user_id]);
$feedingSchedules = $feedStmt->fetchAll();

// Fetch Top 3 Health Reminders (for Card)
$healthStmt = $pdo->prepare("SELECT * FROM health_reminders WHERE user_id = ? AND status = 'pending' ORDER BY due_at ASC LIMIT 3");
$healthStmt->execute([$user_id]);
$upcomingHealth = $healthStmt->fetchAll();

// Default if no reminder
if (!$currentReminder) {
    $currentReminder = [
        'id' => 0,
        'pet_name' => 'Your pets',
        'message' => 'No active health alerts today. Keep up the great care!',
        'due_at' => null
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PetCloud</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="dashboard-page">

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-paw sidebar-logo-icon"></i>
                <div class="brand-text">
                    <span class="brand-name">PetCloud</span>
                    <span class="brand-sub">DASHBOARD</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <i class="fa-solid fa-table-cells-large"></i> Overview
                </a>
                <a href="adoption.php" class="nav-item">
                    <i class="fa-solid fa-heart"></i> Adoption
                </a>
                <a href="pet-rehoming.php" class="nav-item">
                    <i class="fa-solid fa-house-chimney-user"></i> Pet Rehoming
                </a>
                <a href="mypets.php" class="nav-item">
                    <i class="fa-solid fa-paw"></i> My Pets
                </a>
                <a href="schedule.php" class="nav-item">
                    <i class="fa-regular fa-calendar"></i> Schedule
                    <span class="nav-badge">2</span>
                </a>
                <a href="marketplace.php" class="nav-item">
                    <i class="fa-solid fa-bag-shopping"></i> Marketplace
                </a>
                <a href="health-records.php" class="nav-item">
                    <i class="fa-solid fa-notes-medical"></i> Health Records
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="nav-item">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
                <div class="user-mini-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random"
                        alt="Profile" class="mini-avatar">
                    <div class="mini-info">
                        <span class="mini-name">
                            <?php echo htmlspecialchars($user_name); ?>
                        </span>
                        <span class="mini-role">Premium Member</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search for pets, appointments, or tips...">
                </div>
                <div class="header-actions">
                    <button class="icon-btn">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notification-dot"></span>
                    </button>
                    <a href="mypets.php" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-plus"></i> Add Pet
                    </a>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Hero Section (Professional/Premium Design) -->
                <section class="dashboard-hero"
                    style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.6)), url('images/dashboard_hero.png'); background-size: cover; background-position: center 30%; color: white; padding: 4rem 3.5rem; border-radius: 2rem; margin-bottom: 3rem; position: relative; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);">

                    <div class="hero-overlay" style="position: relative; z-index: 2;">
                        <!-- Muted Dark Badge -->
                        <div class="health-alert-badge"
                            style="background: #1e293b; display: inline-flex; align-items: center; gap: 0.6rem; padding: 0.6rem 1.25rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 800; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 12px rgba(0,0,0,0.2); text-transform: uppercase; letter-spacing: 1.5px;">
                            <span class="alert-dot"
                                style="height: 10px; width: 10px; background: <?php echo $currentReminder['id'] > 0 ? '#ff4757' : '#2ed573'; ?>; border-radius: 50%; display: inline-block; box-shadow: 0 0 8px <?php echo $currentReminder['id'] > 0 ? '#ff4757' : '#2ed573'; ?>;"></span>
                            <?php echo $currentReminder['id'] > 0 ? 'HEALTH ALERT' : 'SYSTEM STATUS: CLEAR'; ?>
                        </div>

                        <!-- Premium Dark Navy Heading -->
                        <h1
                            style="font-size: 3.8rem; margin-bottom: 1.25rem; font-family: 'Outfit'; font-weight: 700; letter-spacing: -2px; line-height: 1.05; color: #0f172a; text-shadow: 0 2px 4px rgba(255,255,255,0.1);">
                            Good Morning, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>!
                        </h1>

                        <!-- Message with Bold Highlight -->
                        <p
                            style="opacity: 0.9; font-size: 1.4rem; font-weight: 500; margin-bottom: 2.5rem; color: #1e293b; max-width: 600px;">
                            <?php
                            $message = htmlspecialchars($currentReminder['pet_name'] . ' ' . $currentReminder['message']);
                            echo str_replace('30 minutes', '<strong style="font-weight:800; text-decoration: underline; text-decoration-color: #10b981;">30 minutes</strong>', $message);
                            ?>
                        </p>

                        <?php if ($currentReminder['id'] > 0): ?>
                            <div class="hero-actions" style="display: flex; gap: 1.25rem;">
                                <form method="POST">
                                    <input type="hidden" name="reminder_id" value="<?php echo $currentReminder['id']; ?>">
                                    <input type="hidden" name="action" value="complete">
                                    <button type="submit" class="btn action-btn"
                                        style="background: #10b981; color: white; border: none; padding: 1rem 2rem; border-radius: 1rem; cursor: pointer; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; font-size: 1rem; transition: 0.3s; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                                        <i class="fa-solid fa-circle-check"></i> Mark as Done
                                    </button>
                                </form>
                                <form method="POST">
                                    <input type="hidden" name="reminder_id" value="<?php echo $currentReminder['id']; ?>">
                                    <input type="hidden" name="action" value="defer">
                                    <button type="submit" class="btn action-btn"
                                        style="background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 255, 255, 0.3); padding: 1rem 2rem; border-radius: 1rem; cursor: pointer; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; font-size: 1rem; backdrop-filter: blur(12px); transition: 0.3s;">
                                        <i class="fa-regular fa-clock"></i> Defer 1 Hour
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Decorative Paw Print -->
                    <div
                        style="position: absolute; right: -30px; top: 50%; transform: translateY(-50%); opacity: 0.1; font-size: 15rem; pointer-events: none;">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                </section>

                <!-- My Family Section (New) -->
                <section class="my-family-section" style="margin-bottom: 2.5rem;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2 style="font-family: 'Outfit'; font-size: 1.5rem;">My Family</h2>
                        <a href="mypets.php"
                            style="color: #3b82f6; text-decoration: none; font-weight: 600; font-size: 0.9rem;">View
                            All</a>
                    </div>
                    <div
                        style="display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 1rem; scrollbar-width: none;">
                        <?php
                        $petsStmt = $pdo->prepare("SELECT * FROM user_pets WHERE user_id = ?");
                        $petsStmt->execute([$user_id]);
                        $myPets = $petsStmt->fetchAll();
                        foreach ($myPets as $pet): ?>
                            <div class="mini-pet-card"
                                style="min-width: 140px; background: white; padding: 1rem; border-radius: 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border: 1px solid #f3f4f6;">
                                <img src="<?php echo htmlspecialchars($pet['pet_image']); ?>"
                                    style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; margin-bottom: 0.75rem; border: 3px solid #f3f4f6;">
                                <h4 style="font-size: 0.9rem; margin-bottom: 0.15rem;">
                                    <?php echo htmlspecialchars($pet['pet_name']); ?>
                                </h4>
                                <p style="font-size: 0.7rem; color: #9ca3af;">
                                    <?php echo htmlspecialchars($pet['pet_breed']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                        <a href="mypets.php"
                            style="min-width: 140px; background: #f9fafb; border: 2px dashed #e5e7eb; border-radius: 1.25rem; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #9ca3af;">
                            <i class="fa-solid fa-plus" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                            <span style="font-size: 0.8rem; font-weight: 600;">Add Pet</span>
                        </a>
                    </div>
                </section>

                <div class="dashboard-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                    <!-- Column 1: Quick Actions & Schedule -->
                    <div class="grid-col-left">
                        <!-- Quick Actions Grid -->
                        <div class="quick-actions-grid"
                            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
                            <a href="adoption.php" class="quick-action-card"
                                style="background: white; padding: 1.5rem; border-radius: 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: block; border: 1px solid #f3f4f6; transition: transform 0.2s;">
                                <div class="action-icon"
                                    style="width: 44px; height: 44px; background: #d1fae5; color: #10b981; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                                    <i class="fa-solid fa-heart"></i>
                                </div>
                                <h3 style="font-size: 1rem; margin-bottom: 0.25rem; font-family: 'Outfit';">Adopt a
                                    Companion</h3>
                                <p style="font-size: 0.85rem; color: #6b7280;">Find your new best friend</p>
                            </a>

                            <a href="schedule.php" class="quick-action-card"
                                style="background: white; padding: 1.5rem; border-radius: 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: block; border: 1px solid #f3f4f6; transition: transform 0.2s;">
                                <div class="action-icon"
                                    style="width: 44px; height: 44px; background: #dbeafe; color: #3b82f6; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                                    <i class="fa-solid fa-calendar-plus"></i>
                                </div>
                                <h3 style="font-size: 1rem; margin-bottom: 0.25rem; font-family: 'Outfit';">Schedule
                                    Feeding</h3>
                                <p style="font-size: 0.85rem; color: #6b7280;">Set automated meal times</p>
                            </a>

                            <a href="adoption-list-pet.php" class="quick-action-card"
                                style="background: white; padding: 1.5rem; border-radius: 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-decoration: none; color: inherit; display: block; border: 1px solid #f3f4f6; transition: transform 0.2s;">
                                <div class="action-icon"
                                    style="width: 44px; height: 44px; background: #fef3c7; color: #d97706; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                                    <i class="fa-solid fa-house-chimney-user"></i>
                                </div>
                                <h3 style="font-size: 1rem; margin-bottom: 0.25rem; font-family: 'Outfit';">List Your
                                    Pet</h3>
                                <p style="font-size: 0.85rem; color: #6b7280;">Rehome your pet safely</p>
                            </a>
                        </div>

                        <div class="card feeding-schedule-card"
                            style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                            <div class="card-header"
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                <div class="icon-title" style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="icon-yellow"
                                        style="width: 40px; height: 40px; background: #fef3c7; color: #d97706; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-utensils"></i>
                                    </div>
                                    <div>
                                        <h4 style="font-size: 1rem;">Feeding Schedule</h4>
                                        <span class="text-xs text-muted"
                                            style="font-size: 0.75rem; color: #6b7280;">Today,
                                            <?php echo date('M d'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-list">
                                <?php if (empty($feedingSchedules)): ?>
                                    <div style="text-align: center; padding: 1rem; color: #9ca3af; font-size: 0.85rem;">
                                        No schedules set.
                                        <a href="feeding-manager.php"
                                            style="color: #3b82f6; display: block; margin-top: 0.5rem;">Add Routine</a>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($feedingSchedules as $schedule): ?>
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                                            <div>
                                                <span
                                                    style="font-size: 0.875rem; display: block; font-weight: 500;"><?php echo htmlspecialchars($schedule['meal_name']); ?></span>
                                                <span
                                                    style="font-size: 0.75rem; color: #6b7280;"><?php echo htmlspecialchars($schedule['food_description']); ?></span>
                                            </div>
                                            <span style="font-size: 0.875rem; color: #10b981; font-weight: 600;">
                                                <?php echo date('g:i A', strtotime($schedule['feeding_time'])); ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card orders-card"
                            style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <div class="card-header"
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                <div class="icon-title" style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="icon-blue"
                                        style="width: 40px; height: 40px; background: #e0f2fe; color: #0284c7; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                    <h4 style="font-size: 1rem;">Recent Orders</h4>
                                </div>
                                <a href="marketplace.php"
                                    style="font-size: 0.75rem; color: #3b82f6; text-decoration: none;">Shop More</a>
                            </div>

                            <?php
                            // --- AUTO-FIX: Create Table If Missing ---
                            try {
                                $pdo->query("SELECT 1 FROM orders LIMIT 1");
                            } catch (PDOException $e) {
                                include 'setup_orders_db.php';
                            }

                            // Fetch recent orders
                            $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
                            $orderStmt->execute([$user_id]);
                            $orders = $orderStmt->fetchAll();

                            if (empty($orders)): ?>
                                <div style="text-align: center; padding: 2rem 0; color: #9ca3af;">
                                    <i class="fa-solid fa-cart-shopping"
                                        style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                    <p style="font-size: 0.875rem;">No orders yet</p>
                                </div>
                            <?php else: ?>
                                <div class="orders-list">
                                    <?php foreach ($orders as $order): 
                                        // Fetch items for this order
                                        $itemsStmt = $pdo->prepare("
                                            SELECT p.name 
                                            FROM order_items oi 
                                            JOIN products p ON oi.product_id = p.id 
                                            WHERE oi.order_id = ? 
                                            LIMIT 2
                                        ");
                                        $itemsStmt->execute([$order['id']]);
                                        $items = $itemsStmt->fetchAll(PDO::FETCH_COLUMN);
                                        
                                        // Count total items to check if there are more
                                        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
                                        $countStmt->execute([$order['id']]);
                                        $totalItems = $countStmt->fetchColumn();
                                        
                                        $namesString = implode(', ', $items);
                                        if ($totalItems > 2) {
                                            $namesString .= ' +' . ($totalItems - 2) . ' more';
                                        }
                                        if (empty($namesString)) {
                                            $namesString = "Order items";
                                        }
                                    ?>
                                        <div class="order-summary-item"
                                            style="padding: 1rem; border: 1px solid #f3f4f6; border-radius: 0.75rem; margin-bottom: 0.75rem;">
                                            <div
                                                style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                                <div>
                                                    <span style="font-weight: 700; font-size: 0.95rem; display:block;">Order
                                                        #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                                    <span style="font-size: 0.85rem; color: #4b5563; display:block; margin-top:0.25rem;">
                                                        <?php echo htmlspecialchars($namesString); ?>
                                                    </span>
                                                </div>
                                                <span
                                                    style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 1rem; background: <?php echo $order['status'] == 'Processing' ? '#fef3c7' : '#dcfce7'; ?>; color: <?php echo $order['status'] == 'Processing' ? '#92400e' : '#166534'; ?>;">
                                                    <?php echo $order['status']; ?>
                                                </span>
                                            </div>
                                            <div
                                                style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #6b7280; margin-top:0.75rem; border-top:1px dashed #e5e7eb; padding-top:0.5rem;">
                                                <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                                <span
                                                    style="font-weight: 600; color: #111827;">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Upcoming Appointments Section -->
                         <div class="card appointments-card" 
                            style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 2rem;">
                            <div class="card-header"
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                <div class="icon-title" style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="icon-purple"
                                        style="width: 40px; height: 40px; background: #f3e8ff; color: #9333ea; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-stethoscope"></i>
                                    </div>
                                    <h4 style="font-size: 1rem;">Upcoming Visits</h4>
                                </div>
                                <a href="schedule.php"
                                    style="font-size: 0.75rem; color: #3b82f6; text-decoration: none;">New Booking</a>
                            </div>

                            <?php
                            // Fetch upcoming appointments
                            $apptStmt = $pdo->prepare("
                                SELECT a.*, h.name as hospital_name 
                                FROM appointments a 
                                LEFT JOIN hospitals h ON a.hospital_id = h.id 
                                WHERE a.user_id = ? AND a.status != 'cancelled' 
                                ORDER BY a.appointment_date ASC, a.appointment_time ASC 
                                LIMIT 3
                            ");
                            $apptStmt->execute([$user_id]);
                            $appointments = $apptStmt->fetchAll();

                            if (empty($appointments)): ?>
                                <div style="text-align: center; padding: 2rem 0; color: #9ca3af;">
                                    <i class="fa-regular fa-calendar-xmark"
                                        style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                    <p style="font-size: 0.875rem;">No upcoming appointments</p>
                                </div>
                            <?php else: ?>
                                <div class="appointments-list">
                                    <?php foreach ($appointments as $appt): 
                                        $apptDate = new DateTime($appt['appointment_date']);
                                        $formattedDate = $apptDate->format('M d');
                                        $formattedTime = date('g:i A', strtotime($appt['appointment_time']));
                                    ?>
                                        <div class="appt-item"
                                            style="display:flex; align-items:center; gap:1rem; padding: 1rem; border: 1px solid #f3f4f6; border-radius: 0.75rem; margin-bottom: 0.75rem;">
                                            <!-- Date Box -->
                                            <div style="background:#f8fafc; padding:0.5rem 0.75rem; border-radius:0.5rem; text-align:center; min-width:60px;">
                                                <div style="font-weight:700; color:#334155; font-size:1rem;"><?php echo $apptDate->format('d'); ?></div>
                                                <div style="font-size:0.7rem; color:#64748b; text-transform:uppercase;"><?php echo $apptDate->format('M'); ?></div>
                                            </div>
                                            
                                            <!-- Info -->
                                            <div style="flex:1;">
                                                <h5 style="margin:0; font-size:0.95rem; color:#1e293b;"><?php echo htmlspecialchars($appt['service_type']); ?> for <?php echo htmlspecialchars($appt['pet_name']); ?></h5>
                                                <div style="font-size:0.8rem; color:#64748b; margin-top:0.25rem;">
                                                    <i class="fa-solid fa-location-dot" style="color:#cbd5e1; margin-right:4px;"></i> 
                                                    <?php echo htmlspecialchars($appt['hospital_name'] ?? 'PetCloud Partner'); ?>
                                                </div>
                                            </div>

                                            <!-- Time -->
                                            <div style="font-size:0.85rem; font-weight:600; color:#9333ea;">
                                                <?php echo $formattedTime; ?>
                                            </div>

                                            <!-- Delete Action -->
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" style="margin-left:auto;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                                <input type="hidden" name="cancel_appointment" value="1">
                                                <button type="submit" 
                                                    style="background:white; border:1px solid #fee2e2; cursor:pointer; color:#ef4444; width:32px; height:32px; border-radius:0.5rem; display:flex; align-items:center; justify-content:center; transition:0.2s;"
                                                    onmouseover="this.style.background='#fee2e2'"
                                                    onmouseout="this.style.background='white'">
                                                    <i class="fa-solid fa-trash-can" style="font-size:0.9rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Column 2: Health Status -->
                    <div class="grid-col-right">
                        <!-- Pet Owner Profile Card -->
                        <div class="card profile-card"
                            style="background: white; padding: 1.5rem; border-radius: 1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 2rem; text-align: center; border: 1px solid #f3f4f6;">
                            <div style="position: relative; display: inline-block; margin-bottom: 1rem;">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=3b82f6&color=fff&size=100"
                                    style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid #f3f4f6;">
                                <div
                                    style="position: absolute; bottom: 5px; right: 5px; width: 20px; height: 20px; background: #10b981; border: 3px solid white; border-radius: 50%;">
                                </div>
                            </div>
                            <h3 style="font-family: 'Outfit'; font-size: 1.25rem; margin-bottom: 0.25rem;">
                                <?php echo htmlspecialchars($user_name); ?>
                            </h3>
                            <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 1rem;">Premium Member</p>
                            <div
                                style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: #9ca3af; font-size: 0.8rem;">
                                <i class="fa-solid fa-location-dot"></i> San Francisco, CA
                            </div>
                            <a href="profile.php" class="btn btn-outline"
                                style="width: 100%; margin-top: 1.5rem; padding: 0.6rem; border-radius: 0.75rem; font-size: 0.85rem; text-decoration: none; display: block; border: 1px solid #e5e7eb; color: #374151; transition: 0.2s;">Edit
                                Profile</a>
                        </div>

                        <div class="card health-status-card"
                            style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <div class="card-header centered-header" style="text-align: center; margin-bottom: 1.5rem;">
                                <div class="heart-icon-bg"
                                    style="width: 50px; height: 50px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="fa-solid fa-heart"></i>
                                </div>
                                <h4>Health Status</h4>
                            </div>

                            <div class="health-metrics">
                                <?php if (empty($upcomingHealth)): ?>
                                    <div style="text-align: center; color: #10b981; padding: 1rem;">
                                        <i class="fa-solid fa-check-circle"
                                            style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                        <p style="font-size: 0.9rem;">All clear! No pending health alerts.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($upcomingHealth as $h):
                                        $dueDate = new DateTime($h['due_at']);
                                        $today = new DateTime();
                                        $diff = $today->diff($dueDate);
                                        $daysLeft = $diff->days * ($diff->invert ? -1 : 1);

                                        $color = '#10b981'; // green
                                        $width = '100%';
                                        $dueText = "Due in $daysLeft days";

                                        if ($daysLeft < 0) {
                                            $color = '#ef4444'; // red (overdue)
                                            $dueText = "Overdue by " . abs($daysLeft) . " days";
                                            $width = '100%';
                                        } elseif ($daysLeft <= 3) {
                                            $color = '#f59e0b'; // orange (urgent)
                                            $width = '90%';
                                        } elseif ($daysLeft <= 7) {
                                            $color = '#3b82f6'; // blue
                                            $width = '75%';
                                        } else {
                                            $width = '50%';
                                        }
                                        ?>
                                        <div class="metric-item" style="margin-bottom: 1.5rem;">
                                            <div class="flex justify-between text-sm mb-1"
                                                style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                                <span><?php echo htmlspecialchars($h['pet_name'] . ' - ' . $h['title']); ?></span>
                                                <!-- Assuming title exists, or use message -->
                                                <span
                                                    style="color: <?php echo $color; ?>; font-weight: 700;"><?php echo $dueText; ?></span>
                                            </div>
                                            <div class="progress-bar-bg"
                                                style="background: #f3f4f6; height: 8px; border-radius: 4px;">
                                                <div class="progress-bar"
                                                    style="width: <?php echo $width; ?>; background: <?php echo $color; ?>; height: 100%; border-radius: 4px;">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>