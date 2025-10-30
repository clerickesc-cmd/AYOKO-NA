<?php
require 'database.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$pdo = connectPDO();
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$confirmPassword = $_POST['confirm_password'] ?? '';

if (!$user || !password_verify($confirmPassword, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit;
}

// Delete user data
$delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
$delete->execute([$_SESSION['user_id']]);

session_unset();
session_destroy();

echo json_encode(['success' => true, 'message' => 'Account deleted successfully!']);
?>
