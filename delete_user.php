<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $pdo = connectPDO();
    $userId = intval($_POST['user_id']);

    // Prevent admin from deleting themselves
    if ($userId === $_SESSION['user_id']) {
        header("Location: admin_users.php?error=cannot_delete_self");
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    header("Location: admin_users.php?deleted=1");
    exit;
} else {
    header("Location: admin_users.php");
    exit;
}
?>
