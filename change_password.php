<?php
require 'database.php';
session_start();

$pdo = connectPDO();

// Redirect if not logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_id = $_SESSION['user_id'];

    if ($new_password !== $confirm_password) {
        $message = "❌ New passwords do not match.";
    } else {
        // Fetch current password hash from database
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['password'])) {
            // Hash and update new password
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$new_hashed, $user_id]);

            if ($update->rowCount()) {
                $message = "✅ Password updated successfully!";
            } else {
                $message = "⚠️ Something went wrong while updating.";
            }
        } else {
            $message = "❌ Incorrect current password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: rgba(0,0,0,0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.modal {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
    text-align: center;
    width: 380px;
}
.modal h2 {
    color: #6A0DAD;
    margin-bottom: 20px;
}
.input-group {
    position: relative;
    margin-bottom: 15px;
}
input[type="password"] {
    width: 100%;
    padding: 10px 40px 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}
.toggle-visibility {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}
button {
    width: 100%;
    background-color: #8E44AD;
    color: white;
    border: none;
    padding: 10px;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
}
button:hover {
    background-color: #732d91;
}
.message {
    margin-top: 10px;
    color: #333;
    font-size: 14px;
}
.success { color: green; }
.error { color: red; }
</style>
</head>
<body>

<div class="modal">
    <h2>Change Password</h2>
    <form method="POST">
        <div class="input-group">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <span class="toggle-visibility">👁️</span>
        </div>
        <div class="input-group">
            <input type="password" name="new_password" placeholder="New Password" required>
            <span class="toggle-visibility">👁️</span>
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <span class="toggle-visibility">👁️</span>
        </div>
        <button type="submit">Update Password</button>
    </form>
    <p class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </p>
</div>

<script>
// Toggle password visibility
document.querySelectorAll('.toggle-visibility').forEach(icon => {
    icon.addEventListener('click', () => {
        const input = icon.previousElementSibling;
        input.type = input.type === 'password' ? 'text' : 'password';
    });
});
</script>

</body>
</html>
