<?php
require 'database.php';
session_start();

// Redirect if not logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = connectPDO();

// Fetch only messages posted by the logged-in user
try {
    $stmt = $pdo->prepare("SELECT id, content, created_at FROM messages WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
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
  <title>My Messages | AYOKO NA</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    * {
      margin: 0; padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    body {
      background-color: #d3d3d3;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    header {
      width: 100%;
      background-color: #8f6cb3;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 22px 65px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .logo {
      color: #fff; font-size: 27px;
      font-weight: 500; letter-spacing: 5px;
    }
    nav ul {
      display: flex; gap: 40px; list-style: none;
    }
    nav ul li a {
      color: #e4daf3; text-decoration: none;
      font-size: 15px; letter-spacing: 1.6px;
      transition: color 0.3s ease; position: relative;
    }
    nav ul li a::after {
      content: ""; position: absolute;
      left: 0; bottom: -6px; width: 0%;
      height: 2px; background-color: #fff;
      transition: width 0.3s ease;
    }
    nav ul li a:hover { color: #fff; }
    nav ul li a:hover::after { width: 100%; }

    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 60px 20px;
    }

    h1 {
      font-size: 42px; color: #8f6cb3;
      letter-spacing: 6px; margin-bottom: 15px;
    }
    p.subtitle {
      color: #6a558d; font-size: 14px;
      margin-bottom: 40px; text-align: center;
    }

    /* ✅ Fixed Message Alignment */
    .message-container {
      width: 650px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      align-items: stretch; /* ensures all messages align */
    }

    .message {
      position: relative;
      background-color: #fff;
      padding: 18px 20px;
      border-radius: 10px;
      border-left: 5px solid #8f6cb3;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: box-shadow 0.2s ease;
      display: flex;
      flex-direction: column;
      align-items: flex-start; /* ensures consistent left alignment */
    }

    .message:hover {
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .message p {
      color: #5d4c80;
      font-size: 14px;
      margin-bottom: 8px;
      white-space: pre-wrap;
      text-align: left;
      word-wrap: break-word;
      width: 100%;
    }

    .message small {
      color: #7a6799;
      font-size: 11px;
      align-self: flex-end;
    }

    .content-wrapper {
      width: 100%;
      display: flex;
      flex-direction: column;
    }

    .delete-form {
      position: absolute;
      top: 8px;
      right: 10px;
    }

    .delete-btn, .edit-btn {
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      color: #8f6cb3;
      transition: color 0.3s ease;
    }

    .delete-btn:hover, .edit-btn:hover {
      color: #6b4f91;
    }

    .edit-btn { position: absolute; top: 8px; right: 46px; }

    textarea.edit-area {
      width: 100%;
      height: 100px;
      border: 2px solid #8f6cb3;
      border-radius: 5px;
      padding: 10px;
      resize: none;
      font-size: 14px;
      color: #5d4c80;
      background-color: #f6f4fa;
    }

    .edit-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 10px;
    }

    .save-btn, .cancel-btn {
      padding: 6px 16px;
      font-size: 13px;
      border-radius: 5px;
      border: none;
      cursor: pointer;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
    }

    .save-btn {
      background-color: #8f6cb3;
      color: #fff;
    }
    .save-btn:hover {
      background-color: #7b56a3;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .cancel-btn {
      background-color: #e7e3ee;
      color: #5d4c80;
    }
    .cancel-btn:hover {
      background-color: #d4cce0;
    }

    footer {
      text-align: center;
      font-size: 12px;
      color: #7a6799;
      padding: 20px 0;
    }
  </style>
</head>

<body>
  <header>
    <div class="logo">AYOKO NA</div>
    <nav>
      <ul>
        <li><a href="about.php">ABOUT US</a></li>
        <li><a href="messages.php" style="color:white;">MESSAGES</a></li>
        <li><a href="settings.php">SETTINGS</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <h1>MY MESSAGES</h1>
    <p class="subtitle">View, edit, or delete the messages you’ve posted.</p>

    <div class="message-container">
      <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $row): ?>
          <div class="message" id="message-<?= (int)$row['id']; ?>">
            <form action="delete_message.php" method="POST" class="delete-form">
              <input type="hidden" name="id" value="<?= (int)$row['id']; ?>">
              <button type="submit" class="delete-btn" title="Delete Message" onclick="return confirm('Are you sure you want to delete this message?')">🗑️</button>
            </form>

            <button class="edit-btn" onclick="enableEdit(<?= (int)$row['id']; ?>)">✏️</button>

            <div class="content-wrapper">
              <p id="content-<?= (int)$row['id']; ?>"><?= htmlspecialchars($row['content']); ?></p>
              <small>Posted <?= htmlspecialchars(date('F j, Y g:i A', strtotime($row['created_at']))); ?></small>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color:#7a6799; text-align:center;">You haven’t posted any messages yet.</p>
      <?php endif; ?>
    </div>
  </main>

  <footer>© 2025 AYOKO NA — All Rights Reserved</footer>

  <script>
    function enableEdit(id) {
      const messageBox = document.getElementById('message-' + id);
      const contentEl = document.getElementById('content-' + id);
      const oldContent = contentEl.textContent.trim();

      contentEl.style.display = 'none';
      const textarea = document.createElement('textarea');
      textarea.className = 'edit-area';
      textarea.value = oldContent;

      const actionDiv = document.createElement('div');
      actionDiv.className = 'edit-actions';

      const saveBtn = document.createElement('button');
      saveBtn.className = 'save-btn';
      saveBtn.textContent = 'Save';

      const cancelBtn = document.createElement('button');
      cancelBtn.className = 'cancel-btn';
      cancelBtn.textContent = '✖ Cancel';

      actionDiv.appendChild(saveBtn);
      actionDiv.appendChild(cancelBtn);

      contentEl.parentNode.insertBefore(textarea, contentEl.nextSibling);
      contentEl.parentNode.insertBefore(actionDiv, textarea.nextSibling);

      saveBtn.addEventListener('click', async () => {
        const newContent = textarea.value.trim();
        if (!newContent) {
          alert('Message cannot be empty.');
          return;
        }

        const formData = new FormData();
        formData.append('id', id);
        formData.append('content', newContent);

        try {
          const res = await fetch('edit.message.php', { method: 'POST', body: formData });
          const data = await res.json();

          if (data.success) {
            contentEl.textContent = data.content;
            textarea.remove();
            actionDiv.remove();
            contentEl.style.display = 'block';
          } else {
            alert(data.error || 'Failed to update message.');
          }
        } catch {
          alert('Request failed.');
        }
      });

      cancelBtn.addEventListener('click', () => {
        textarea.remove();
        actionDiv.remove();
        contentEl.style.display = 'block';
      });
    }
  </script>
</body>
</html>
