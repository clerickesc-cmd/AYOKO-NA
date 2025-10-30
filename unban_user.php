<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = connectPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = :id AND role = 'banned'");
        $stmt->execute(['id' => $user_id]);

        echo "<script>alert('User has been unbanned successfully.'); window.location.href='admin_users.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Error unbanning user: " . $e->getMessage() . "'); window.location.href='admin_users.php';</script>";
        exit;
    }
} else {
    header('Location: admin_users.php');
    exit;
}
?>
