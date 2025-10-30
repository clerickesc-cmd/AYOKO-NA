<?php
require 'database.php';
session_start();

header('Content-Type: application/json');

// Make sure the user is logged in
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$pdo = connectPDO();

// Get POST data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

if ($id <= 0 || $content === '') {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

try {
    // Only allow the logged-in user to edit their own message
    $stmt = $pdo->prepare("UPDATE messages SET content = :content WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':content' => $content,
        ':id' => $id,
        ':user_id' => $_SESSION['user_id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'content' => htmlspecialchars($content)]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Message not found or no changes made.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>
