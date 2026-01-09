<?php
require_once 'db_connect.php';

// Create health_reminders table
$pdo->exec("CREATE TABLE IF NOT EXISTS health_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pet_name VARCHAR(100),
    title VARCHAR(255),
    message TEXT,
    due_at TIMESTAMP,
    status ENUM('pending', 'completed', 'deferred') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Create daily_tasks table
$pdo->exec("CREATE TABLE IF NOT EXISTS daily_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    task_name VARCHAR(255),
    task_time VARCHAR(100),
    is_done BOOLEAN DEFAULT FALSE,
    task_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Seed a default reminder for demonstration if none exists
$user_id = 1; // Default for seeding, will be dynamic in app
$check = $pdo->query("SELECT COUNT(*) FROM health_reminders WHERE user_id = $user_id")->fetchColumn();
if ($check == 0) {
    $pdo->prepare("INSERT INTO health_reminders (user_id, pet_name, title, message, due_at) VALUES (?, ?, ?, ?, ?)")
        ->execute([1, 'Bella', 'Heartworm Medication', 'Needs her medication in 30 minutes', date('Y-m-d H:i:s', strtotime('+30 minutes'))]);
}

// Seed daily tasks if none exist
$checkTasks = $pdo->query("SELECT COUNT(*) FROM daily_tasks WHERE user_id = $user_id AND task_date = CURDATE()")->fetchColumn();
if ($checkTasks == 0) {
    $tasks = [
        ['Morning Feeding', '7:00 AM • 1 cup kibble', 1],
        ['Morning Walk', '7:30 AM • 30 mins', 1],
        ['Flea Prevention Meds', '12:00 PM • Monthly Dose', 1],
        ['Evening Walk', '6:00 PM • Park Route', 0],
        ['Dental Chews', 'Before Bed', 0],
    ];
    $stmt = $pdo->prepare("INSERT INTO daily_tasks (user_id, task_name, task_time, is_done, task_date) VALUES (?, ?, ?, ?, CURDATE())");
    foreach ($tasks as $t) {
        $stmt->execute([1, $t[0], $t[1], $t[2]]);
    }
}

echo "Functionality tables and seed data created.";
?>