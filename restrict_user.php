<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_POST['user_id']) && isset($_POST['hours'])) {
    $user_id = $_POST['user_id'];
    $hours = intval($_POST['hours']);
    $pdo = connectPDO();

    $restrict_until = date('Y-m-d H:i:s', strtotime("+$hours hours"));

    $stmt = $pdo->prepare("UPDATE users SET restricted_until = ? WHERE id = ?");
    $stmt->execute([$restrict_until, $user_id]);

    echo "<script>alert('User restricted for $hours hour(s).'); window.location.href='admin_reports.php';</script>";
    exit;
} else {
    echo "<script>alert('Invalid request.'); window.location.href='admin_reports.php';</script>";
    exit;
}
?>
