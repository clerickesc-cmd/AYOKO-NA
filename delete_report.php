<?php
session_start();
require 'database.php';

// ✅ Only admins can delete reports
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = connectPDO();

if (isset($_POST['report_id'], $_POST['message_id'])) {
    $report_id = $_POST['report_id'];
    $message_id = $_POST['message_id'];

    // ✅ Delete the report first
    $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->execute([$report_id]);

    // ✅ Delete the related message
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$message_id]);

    // ✅ Redirect back to reports page
    header("Location: admin_reports.php?deleted=success");
    exit;
} else {
    header("Location: admin_reports.php?error=invalid");
    exit;
}
?>
