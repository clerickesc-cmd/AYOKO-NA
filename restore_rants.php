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
        // Restore deleted rant
        $stmt = $pdo->prepare("UPDATE messages SET status = 'active' WHERE id = :id");
        $stmt->execute(['id' => $rant_id]);

        echo "<script>alert('Rant restored successfully.'); window.location.href='admin_rants.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Error restoring rant: " . $e->getMessage() . "'); window.location.href='admin_rants.php';</script>";
        exit;
    }
} else {
    header('Location: admin_rants.php');
    exit;
}
?>
