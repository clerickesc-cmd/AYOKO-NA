<?php
require 'database.php';
session_start();

// Must be logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        // Verify message ownership before deleting
        $check = $pdo->prepare("SELECT user_id FROM messages WHERE id = :id");
        $check->execute(['id' => $id]);
        $message = $check->fetch(PDO::FETCH_ASSOC);

        if ($message && $message['user_id'] == $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }

        // Redirect back safely
        $referer = $_SERVER['HTTP_REFERER'] ?? 'messages.php';
        header("Location: " . htmlspecialchars($referer));
        exit;
    } catch (PDOException $e) {
        echo "Error deleting message.";
    }
} else {
    header('Location: messages.php');
    exit;
}
