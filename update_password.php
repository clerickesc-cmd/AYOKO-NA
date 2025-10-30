<?php
require 'database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

$pdo = connectPDO();
$user_id = $_SESSION['user_id'];

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(["success" => false, "message" => "Passwords do not match."]);
    exit;
}

// Get current hashed password
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !password_verify($current_password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
    exit;
}

// Update password
$new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$new_hashed, $user_id]);

echo json_encode(["success" => true, "message" => "Password updated successfully!"]);
