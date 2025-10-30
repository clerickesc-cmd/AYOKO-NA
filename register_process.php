<?php
require 'database.php';
$pdo = connectPDO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if email or username already exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
        $check->execute(['email' => $email, 'username' => $username]);

        if ($check->rowCount() > 0) {
            echo "<script>alert('Username or Email already exists.'); window.history.back();</script>";
            exit();
        }

        // Insert new user
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        echo "<script>alert('Registration successful! You can now log in.'); window.location.href='login.php';</script>";

    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}
?>
