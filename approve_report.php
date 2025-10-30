<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_POST['report_id'])) {
    $pdo = connectPDO();
    // You can choose what happens when approved (for example: mark as reviewed)
    $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->execute([$_POST['report_id']]);
}

header('Location: admin_reports.php');
exit;
?>
