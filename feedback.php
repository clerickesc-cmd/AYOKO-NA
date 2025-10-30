<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = connectPDO();

// Handle feedback submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $feedback = trim($_POST['feedback']);
    if ($feedback !== "") {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, message, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$_SESSION['user_id'], $feedback]);
        $success = "Feedback sent successfully!";
    } else {
        $error = "Please enter your feedback.";
    }
}

// Fetch all feedback entries
$stmt = $pdo->query("
    SELECT users.username, feedback.message 
    FROM feedback 
    JOIN users ON feedback.user_id = users.id 
    ORDER BY feedback.created_at DESC
");
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

body {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    background-color: #d6d6d6;
}

/* Header */
.header {
    background-color: #8a63b3;
    padding: 20px 50px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
}

.header h1 {
    margin: 0;
    font-weight: 700;
    font-size: 24px;
    letter-spacing: 3px;
}

.nav-links {
    display: flex;
    gap: 30px;
}

.nav-links a {
    text-decoration: none;
    color: white;
    font-size: 14px;
    letter-spacing: 2px;
    transition: opacity 0.2s ease;
}

.nav-links a:hover {
    opacity: 0.8;
}

/* Feedback section */
.container {
    max-width: 800px;
    margin: 60px auto;
    background: #e6e6e6;
    border-radius: 15px;
    padding: 40px 60px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    text-align: center;
}

.container h2 {
    color: #7b5fa6;
    font-size: 36px;
    letter-spacing: 5px;
    margin-bottom: 40px;
}

/* Feedback cards */
.feedback-card {
    background: #f2f2f2;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    text-align: center;
}

.feedback-card strong {
    font-size: 15px;
    letter-spacing: 1px;
}

.feedback-card p {
    font-size: 13px;
    color: #555;
    margin-top: 6px;
}

/* Give Feedback Button */
.give-feedback {
    margin-top: 30px;
}

.give-feedback button {
    background: none;
    border: none;
    color: #7b5fa6;
    font-size: 14px;
    letter-spacing: 1px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity 0.2s;
}

.give-feedback button:hover {
    opacity: 0.7;
}

.give-feedback img {
    width: 20px;
}

/* Popup */
.popup {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.popup-content {
    background: #f9f9f9;
    border-radius: 15px;
    padding: 40px 60px;
    text-align: center;
    width: 400px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.popup-content h3 {
    color: #7b5fa6;
    letter-spacing: 2px;
    margin-bottom: 15px;
}

textarea {
    width: 100%;
    height: 120px;
    border-radius: 10px;
    border: 1px solid #ccc;
    padding: 10px;
    resize: none;
    font-family: inherit;
    font-size: 14px;
}

.popup-content button {
    margin-top: 20px;
    background: #7b5fa6;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 25px;
    cursor: pointer;
    font-size: 14px;
}

.popup-content button:hover {
    background: #654b90;
}

.message {
    margin-bottom: 10px;
    text-align: center;
}
</style>
</head>
<body>
<div class="header">
  <h1>AYOKO NA</h1>
  <div class="nav-links">
    <a href="your_messages.php">YOUR MESSAGES</a>
    <a href="messages.php">MESSAGES</a>
    <a href="settings.php">SETTINGS</a>
  </div>
</div>

<div class="container">
  <h2>FEEDBACK</h2>

  <?php foreach ($feedbacks as $f): ?>
  <div class="feedback-card">
    <strong><?= htmlspecialchars(strtoupper($f['username'])) ?></strong>
    <p><?= htmlspecialchars($f['message']) ?></p>
  </div>
  <?php endforeach; ?>

  <div class="give-feedback">
    <button onclick="openPopup()">
      <img src="https://cdn-icons-png.flaticon.com/512/3095/3095583.png" alt="icon">
      GIVE FEEDBACK
    </button>
  </div>
</div>

<!-- Feedback popup -->
<div class="popup" id="feedbackPopup">
  <div class="popup-content">
    <h3>GIVE FEEDBACK</h3>
    <div class="message">
        <?php if(!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </div>
    <form method="POST">
      <textarea name="feedback" placeholder="Write your feedback here..."></textarea>
      <button type="submit">Submit</button>
    </form>
  </div>
</div>

<script>
function openPopup() {
  document.getElementById("feedbackPopup").style.display = "flex";
}

window.onclick = function(event) {
  const popup = document.getElementById("feedbackPopup");
  if (event.target === popup) {
    popup.style.display = "none";
  }
}
</script>
</body>
</html>
