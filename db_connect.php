<?php
// Database configuration
$host = 'localhost';
$dbname = 'petcloud_db';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set default fetch mode
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log error
    error_log("Database connection failed: " . $e->getMessage());

    // Show user-friendly error
    die("Database connection error. Please check if MySQL is running and database 'petcloud_db' exists.");
}

// Helper function for admin logging
function logAdminActivity($pdo, $adminName, $action, $targetType = null, $targetId = null)
{
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_activity_logs (admin_name, action, target_type, target_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$adminName, $action, $targetType, $targetId]);
    } catch (PDOException $e) {
        error_log("Failed to log admin activity: " . $e->getMessage());
    }
}
?>