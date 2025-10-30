<?php
require 'database.php';
session_start();

$pdo = connectPDO();

// Redirect if not logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 🔹 Check restriction (but don’t die)
$stmt = $pdo->prepare("SELECT restricted_until FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$isRestricted = false;
$restrictionMessage = "";

if ($user && $user['restricted_until'] && strtotime($user['restricted_until']) > time()) {
    $remaining = strtotime($user['restricted_until']) - time();
    $hours = ceil($remaining / 3600);
    $isRestricted = true;
    $restrictionMessage = "⛔ You are restricted from posting for another {$hours} hour(s).";
}

// ✅ Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// ✅ Handle POST form submission (insert message)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isRestricted && !empty($_POST['message'])) {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (user_id, content, created_at) VALUES (:user_id, :content, NOW())");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':content' => $message
            ]);
            header("Location: messages.php");
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('Error saving message.');</script>";
        }
    } else {
        echo "<script>alert('Message cannot be empty.');</script>";
    }
}

// ✅ Fetch messages
try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT id, content, created_at FROM messages WHERE content LIKE :search ORDER BY id DESC");
        $stmt->execute([':search' => "%$search%"]);
    } else {
        $stmt = $pdo->prepare("SELECT id, content, created_at FROM messages ORDER BY id DESC");
        $stmt->execute();
    }
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Messages | AYOKO NA</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
   /* ===== BASE RESET ===== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

body {
  background-color: #d3d3d3;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* ===== HEADER ===== */
header {
  width: 100%;
  background-color: #8f6cb3;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 22px 65px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo {
  color: #fff;
  font-size: 27px;
  font-weight: 500;
  letter-spacing: 5px;
}

nav ul {
  display: flex;
  gap: 40px;
  list-style: none;
  align-items: center;
}

nav ul li a {
  position: relative;
  color: #e4daf3;
  text-decoration: none;
  font-size: 15px;
  letter-spacing: 1.6px;
  transition: color 0.3s ease;
}

nav ul li a::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: -6px;
  width: 0%;
  height: 2px;
  background-color: #fff;
  transition: width 0.3s ease;
}

nav ul li a:hover {
  color: #ffffff;
}

nav ul li a:hover::after {
  width: 100%;
}

/* ===== MAIN ===== */
main {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 60px 20px;
}

h1 {
  font-size: 42px;
  color: #8f6cb3;
  letter-spacing: 6px;
  margin-bottom: 15px;
}

p.subtitle {
  color: #6a558d;
  font-size: 14px;
  margin-bottom: 20px;
  text-align: center;
}

/* ===== SEARCH BAR ===== */
form.search-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 300px;
  background-color: #e7e5eb;
  padding: 6px 12px;
  border-radius: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 40px;
}

.search-bar input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-size: 10px;
  color: #5d4c80;
}

.search-bar input::placeholder {
  color: #8f6cb3;
  font-size: 10px;
}

.search-bar button {
  background: none;
  border: none;
  color: #8f6cb3;
  font-size: 13px;
  cursor: pointer;
  transition: color 0.3s;
}

.search-bar button:hover {
  color: #7a59a0;
}

.clear-btn {
  background: none;
  border: none;
  color: #8f6cb3;
  font-size: 16px;
  cursor: pointer;
  transition: color 0.3s;
}

.clear-btn:hover {
  color: #7a59a0;
}

/* ===== MESSAGE FORM ===== */
form.post-form {
  background-color: #e7e5eb;
  width: 550px;
  padding: 35px 40px;
  border-radius: 3px;
  box-shadow: 0 5px 10px rgba(0,0,0,0.1);
  margin-bottom: 50px;
}

textarea {
  width: 100%;
  height: 120px;
  resize: none;
  padding: 12px;
  border: 2px solid #8f6cb3;
  outline: none;
  background-color: #f4f4f4;
  font-size: 13px;
  color: #5d4c80;
  border-radius: 2px;
}

textarea::placeholder {
  color: #8f6cb3;
  text-transform: uppercase;
  font-size: 11px;
  letter-spacing: 1px;
}

button {
  background-color: #8f6cb3;
  color: white;
  border: none;
  width: 100%;
  padding: 10px 0;
  margin-top: 15px;
  font-size: 13px;
  letter-spacing: 1px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  border-radius: 2px;
}

button:hover {
  background-color: #7a59a0;
}

/* ===== MESSAGE CARDS ===== */
.message-container {
  width: 90%;
  max-width: 1200px;
  margin: 0 auto 60px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 25px;
  justify-content: center;
  align-items: start;
}

.message {
  background: #fff;
  border: 2px solid #8f6cb3;
  border-radius: 10px;
  padding: 15px 15px 12px 15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  cursor: pointer;
  transition: transform 0.2s ease;
  min-height: 180px;
  max-height: 200px;
  overflow: hidden; /* ensures message stays neat */
  position: relative;
}

.from-anon {
  font-weight: 600;
  font-size: 12px;
  color: #8f6cb3;
  margin-bottom: 5px; /* small clean gap */
  text-align: left;
  letter-spacing: 0.5px;
  flex-shrink: 0; /* ensures it never collapses */
}

.message-content {
  flex: 1;
  color: #5d4c80;
  font-size: 13px;
  line-height: 1.5;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 10px;
  word-wrap: break-word;
}

.message-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}


/* ===== REPORT BUTTON ===== */
.report-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 3px;
  background: #f2f2f2;
  border: 1px solid #e74c3c;
  color: #7a7a7a;
  font-size: 9px;
  font-weight: 500;
  text-transform: uppercase;
  padding: 1px 4px;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s ease;
  width: auto;
  height: 16px;
  line-height: 1;
}

.report-btn i {
  color: #e74c3c;
  font-size: 9px;
  margin-top: -1px;
}

.report-btn:hover {
  background: #e74c3c;
  color: #fff;
}

.report-btn:hover i {
  color: #fff;
}

/* ===== REPORT POPUP ===== */
.report-popup {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  animation: fadeIn 0.2s ease;
}

.report-box {
  background: #fff;
  border-radius: 10px;
  width: 350px;
  padding: 25px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  animation: zoomIn 0.25s ease;
}

.report-box p {
  font-size: 14px;
  color: #5d4c80;
  margin-bottom: 15px;
}

.report-box button {
  margin: 8px;
  padding: 8px 15px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
}

.report-confirm {
  background: #e74c3c;
  color: white;
}

.report-cancel {
  background: #ccc;
  color: #333;
}

/* 🔹 Overlay / zoom fixated box with scroll */
.message-zoom-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
  animation: fadeIn 0.3s ease;
  overflow: hidden; /* Prevent the whole screen from scrolling */
}

.message-zoom-box {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  width: 80%;
  max-width: 500px;
  height: 400px;               /* fixed height */
  display: flex;
  flex-direction: column;
  justify-content: space-between; /* Push date to bottom */
  overflow-y: auto;
  overflow-x: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  text-align: left;
  word-wrap: break-word;
  white-space: normal;
}

.message-zoom-box p {
  flex: 1;
  overflow-y: auto;             /* scrolls only text if long */
  margin-bottom: 10px;
}

.message-zoom-box small {
  display: block;
  text-align: right;
  color: #8f6cb3;
  font-size: 12px;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-top: auto;             /* pushes it to bottom */
  padding-top: 10px;
  border-top: 1px solid #ddd;
}
@keyframes fadeIn {
  from { opacity: 0; } to { opacity: 1; }
}

@keyframes zoomIn {
  from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; }
}

/* ===== FOOTER ===== */
footer {
  text-align: center;
  font-size: 12px;
  color: #7a6799;
  padding: 20px 0;
}

.from-anon {
  font-weight: 600;
  font-size: 11px;
  color: #8f6cb3;
  margin-bottom: 8px;
  text-align: left;
  letter-spacing: 0.5px;
}

/* 🔹 Overlay / zoom fixated box with scroll */
.message-zoom-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
  animation: fadeIn 0.3s ease;
  overflow: hidden;
}

/* Main popup box */
.message-zoom-box {
  background: #fff;
  border-radius: 12px;
  width: 80%;
  max-width: 450px;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  overflow: hidden;
  padding: 18px 20px;
}

/* Header (From: Anonymous) */
.message-zoom-header {
  position: sticky;
  top: 0;
  background: white;
  padding: 0;
  margin: 0;
  font-weight: 600;
  color: #8f6cb3;
  font-size: 13px;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #eee;
  z-index: 5;
  line-height: 1.2;
}

/* Scrollable message content */
.message-zoom-content {
  flex: 1;
  overflow-y: auto;
  padding-top: 6px;       /* minimal space after header */
  padding-right: 6px;
  color: #5d4c80;
  font-size: 14px;
  line-height: 1.6;
  white-space: pre-wrap;
}

/* Remove top gap from default <p> */
.message-zoom-content p {
  margin-top: 0 !important;
  margin-bottom: 10px;
}

/* Bottom section (date + click hint) */
.message-zoom-bottom {
  border-top: 1px solid #ddd;
  margin-top: 10px;
  padding-top: 8px;
  text-align: right;
}

.message-zoom-bottom small {
  color: #8f6cb3;
  font-size: 12px;
  letter-spacing: 1px;
  display: block;
  margin-bottom: 5px;
  text-transform: uppercase;
}

.message-zoom-bottom p {
  text-align: center;
  font-size: 11px;
  color: #aaa;
  margin: 0;
}


  </style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.message');

    messages.forEach(msg => {
        msg.addEventListener('click', function(e) {
            // Prevent overlay when clicking REPORT button
            if (e.target.closest('.report-btn')) return;

            const content = this.querySelector('p:nth-of-type(2)') ? this.querySelector('p:nth-of-type(2)').textContent : '';
            const anon = this.querySelector('.from-anon') ? this.querySelector('.from-anon').textContent : 'Anonymous';
            const dateEl = this.querySelector('small');
            const date = dateEl ? dateEl.textContent : '';

            // 🔹 Create overlay
            const overlay = document.createElement('div');
            overlay.classList.add('message-zoom-overlay');

            // 🔹 Create main box
            const box = document.createElement('div');
            box.classList.add('message-zoom-box');

            // 🔹 Header (sticky top: From Anonymous)
            const header = document.createElement('div');
            header.classList.add('message-zoom-header');
            header.textContent = anon;

            // 🔹 Message content (scrollable)
            const contentWrapper = document.createElement('div');
            contentWrapper.classList.add('message-zoom-content');

            const textEl = document.createElement('p');
            textEl.textContent = content;
            contentWrapper.appendChild(textEl);

            // 🔹 Bottom section (date + hint)
            const bottomSection = document.createElement('div');
            bottomSection.classList.add('message-zoom-bottom');

            const dateSmall = document.createElement('small');
            dateSmall.textContent = date;

            const hint = document.createElement('p');
            hint.textContent = 'Click anywhere to close';

            bottomSection.appendChild(dateSmall);
            bottomSection.appendChild(hint);

            // Append to box
            box.appendChild(header);
            box.appendChild(contentWrapper);
            box.appendChild(bottomSection);
            overlay.appendChild(box);
            document.body.appendChild(overlay);

            // Click overlay to close
            overlay.addEventListener('click', function() {
                overlay.classList.add('fade-out');
                setTimeout(() => overlay.remove(), 250);
            });
        });
    });
});
</script>


</head>

<body>
  <header>
    <div class="logo">AYOKO NA</div>
    <nav>
      <ul>
        <li><a href="about.php">ABOUT US</a></li>
        <li><a href="your_message.php">MY MESSAGES</a></li>
        <li><a href="settings.php">SETTINGS</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <h1>MESSAGES</h1>
    <p class="subtitle">Let it out anonymously. Say what you feel — no filters, no judgment.</p>

    <form class="search-bar" method="GET" action="">
      <input type="text" name="search" placeholder="Search messages..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit"><i class="fa fa-search"></i></button>
      <?php if (!empty($search)): ?>
        <button type="button" class="clear-btn" onclick="window.location='messages.php'"><i class="fa fa-times"></i></button>
      <?php endif; ?>
    </form>
        <?php if ($isRestricted): ?>
  <div style="background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:15px; border-radius:5px; width:550px; text-align:center; margin-bottom:30px;">
    <?= htmlspecialchars($restrictionMessage); ?>
  </div>
<?php else: ?>
    <form class="post-form" method="POST" action="">
      <textarea name="message" placeholder="TYPE YOUR MESSAGE HERE..." required></textarea>
      <button type="submit">POST MESSAGE</button>
    </form>
    <?php endif; ?>

    <div class="message-container">
      <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $row): ?>
          
          <div class="message">
  <?php 
    $anon_id = 1000 + ($row['id'] % 9000);
  ?>
  <p class="from-anon">From: Anonymous#<?= $anon_id; ?></p>
  <p class="message-content"><?= htmlspecialchars($row['content']); ?></p>

  <div class="message-footer">
    <button class="report-btn"
      onclick="window.location.href='report_message.php?id=<?= $row['id']; ?>&user=Anonymous#<?= $anon_id; ?>&message=<?= urlencode($row['content']); ?>'">
      <i class='fa fa-pen'></i> REPORT
    </button>
    <small><?= htmlspecialchars(date('F j, Y g:i A', strtotime($row['created_at']))); ?></small>
  </div>
</div>


        <?php endforeach; ?>
      <?php else: ?>
        <p style="color:#7a6799; text-align:center;">No messages found.</p>
      <?php endif; ?>
    </div>
  </main>

  <footer>© 2025 AYOKO NA — All Rights Reserved</footer>
</body>
</html>
