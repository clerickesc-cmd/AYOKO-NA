<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = connectPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rant_id'])) {
    $rant_id = $_POST['rant_id'];

    try {
        // Hard delete: completely remove from database
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
        $stmt->execute(['id' => $rant_id]);

        echo "<script>alert('Rant permanently deleted.'); window.location.href='admin_rants.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting rant: " . $e->getMessage() . "'); window.location.href='admin_rants.php';</script>";
        exit;
    }
} else {
    header('Location: admin_rants.php');
    exit;
}
?>
