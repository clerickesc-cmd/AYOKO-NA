<?php
// reset_process.php
require 'database.php';
session_start();

$pdo = connectPDO();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

if (!$token || !$password || !$password_confirm) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit;
}

if ($password !== $password_confirm) {
    echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
    exit;
}

// Optionally: enforce password strength here
if (strlen($password) < 6) {
    echo "<script>alert('Password must be at least 6 characters.'); window.history.back();</script>";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, token_expiry FROM users WHERE reset_token = :token LIMIT 1");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<script>alert('Invalid or expired token.'); window.location.href='forgot_password.php';</script>";
        exit;
    }

    // Check expiry
    $expiry = new DateTime($user['token_expiry']);
    $now = new DateTime();
    if ($now > $expiry) {
        echo "<script>alert('This reset link has expired. Please request a new one.'); window.location.href='forgot_password.php';</script>";
        exit;
    }

    // Update password and clear token
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE users SET password = :pw, reset_token = NULL, token_expiry = NULL WHERE id = :id");
    $update->execute(['pw' => $newHash, 'id' => $user['id']]);

    echo "<script>alert('Password updated. You can now log in.'); window.location.href='login.php';</script>";
    exit;
} catch (Exception $e) {
    // log error in production
    echo "<script>alert('An error occurred. Please try again later.'); window.history.back();</script>";
    exit;
}
