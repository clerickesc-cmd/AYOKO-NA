<?php
require 'database.php';
session_start();

$pdo = connectPDO();

if (empty($_SESSION['user_id'])) {
    echo "unauthorized";
    exit;
}

$message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$reported_by = $_SESSION['user_id'];

if ($message_id && $reason) {
    try {
        // Make sure the column names match your DB schema
        $stmt = $pdo->prepare("INSERT INTO reports (reported_by, message_id, reason) VALUES (?, ?, ?)");
        $stmt->execute([$reported_by, $message_id, $reason]);

        echo "success";
    } catch (PDOException $e) {
        // Show the exact error for debugging
        echo "DB ERROR: " . $e->getMessage();
    }
} else {
    echo "error";
}
?>
