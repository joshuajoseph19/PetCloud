<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_schedule'])) {
    $meal = $_POST['meal_name'];
    $food = $_POST['food_desc'];
    $time = $_POST['time'];

    if ($meal && $food && $time) {
        $stmt = $pdo->prepare("INSERT INTO feeding_schedules (user_id, meal_name, food_description, feeding_time) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $meal, $food, $time])) {
            $success = "Schedule added successfully!";
        } else {
            $error = "Failed to add schedule.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM feeding_schedules WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: feeding-manager.php");
    exit();
}

// Fetch Schedules
$stmt = $pdo->prepare("SELECT * FROM feeding_schedules WHERE user_id = ? ORDER BY feeding_time ASC");
$stmt->execute([$user_id]);
$schedules = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Feeding Schedule - PetCloud</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            background: white;
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        h1 {
            margin-bottom: 2rem;
            color: #111827;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-family: inherit;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary {
            background: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
        }

        .list {
            margin-top: 2rem;
            border-top: 1px solid #f3f4f6;
            padding-top: 1rem;
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .delete-btn {
            color: #ef4444;
            border: none;
            background: none;
            cursor: pointer;
        }

        .back-link {
            display: block;
            margin-top: 1rem;
            text-align: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Feeding Routine</h1>

        <?php if ($success)
            echo "<p style='color:green; margin-bottom:1rem;'>$success</p>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Meal Name</label>
                <input type="text" name="meal_name" placeholder="e.g. Breakfast" required>
            </div>
            <div class="form-group">
                <label>Food Description</label>
                <input type="text" name="food_desc" placeholder="e.g. Dry Kibble & Vitamins" required>
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" name="time" required>
            </div>
            <button type="submit" name="add_schedule" class="btn btn-primary">Add Schedule</button>
        </form>

        <div class="list">
            <?php foreach ($schedules as $s): ?>
                <div class="item">
                    <div>
                        <strong>
                            <?php echo htmlspecialchars($s['meal_name']); ?>
                        </strong>
                        <div style="font-size: 0.85rem; color: #6b7280;">
                            <?php echo htmlspecialchars($s['food_description']); ?>
                        </div>
                    </div>
                    <div style="display:flex; gap:1rem; align-items:center;">
                        <span style="font-weight:600; color:#10b981;">
                            <?php echo date('g:i A', strtotime($s['feeding_time'])); ?>
                        </span>
                        <a href="?delete=<?php echo $s['id']; ?>" class="delete-btn"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($schedules))
                echo "<p style='text-align:center; color:#9ca3af; margin-top:1rem;'>No routines set.</p>"; ?>
        </div>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>

</html>