<?php
require 'database.php';
session_start();

// Only admins can view
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = connectPDO();

// Fetch all users
$stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #d6d6d6;
      overflow-x: hidden;
    }

    .header {
      background-color: #8a63b3;
      color: white;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 18px;
      letter-spacing: 2px;
    }

    .header h1 {
      font-weight: 700;
    }

    .header a {
      color: white;
      text-decoration: none;
      font-size: 13px;
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    .main-container {
      display: flex;
      padding: 30px;
      gap: 20px;
    }

    .sidebar {
      background-color: #e5e2e2;
      border: 2px solid #8a63b3;
      border-radius: 15px;
      padding: 40px 20px;
      width: 220px;
      text-align: center;
      display: flex;
      flex-direction: column;
      gap: 25px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .sidebar h2 {
      color: #6e4b9d;
      letter-spacing: 3px;
      font-weight: 700;
      font-size: 20px;
      margin-bottom: 20px;
    }

    .sidebar a {
      display: block;
      background-color: #d6d6d6;
      border: none;
      border-radius: 15px;
      padding: 10px 0;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #3c3c3c;
      box-shadow: 2px 3px 4px rgba(0,0,0,0.2);
      text-decoration: none;
      transition: 0.3s;
    }

    .sidebar a:hover {
      background-color: #b89ed7;
      color: white;
    }

    .sidebar a.active {
      background-color: #8a63b3;
      color: white;
      box-shadow: inset 0 2px 5px rgba(0,0,0,0.2);
    }

    .content {
      flex-grow: 1;
      background-color: #e5e2e2;
      border: 2px solid #8a63b3;
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .content h2 {
      text-align: center;
      font-size: 30px;
      letter-spacing: 3px;
      color: #333;
      margin-bottom: 40px;
    }

    table {
      width: 90%;
      margin: 0 auto;
      border-collapse: collapse;
      background: #f3f0f0;
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.15);
      overflow: hidden;
    }

    th, td {
      padding: 12px 10px;
      text-align: center;
      font-size: 13px;
      border-bottom: 1px solid #bbb;
    }

    th {
      background-color: #d8d3da;
      font-weight: 600;
      letter-spacing: 1px;
      color: #333;
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover {
      background-color: #e7e3eb;
    }

    .action-btn {
      border: none;
      border-radius: 8px;
      color: #fff;
      padding: 5px 10px;
      cursor: pointer;
      transition: 0.3s;
    }

    .ban-btn {
      background: #8a63b3;
    }

    .ban-btn:hover {
      background: #6c4c9d;
    }

    .unban-btn {
      background: #4caf50;
      margin-left: 5px;
    }

    .unban-btn:hover {
      background: #3e9142;
    }

    .delete-btn {
      background: #b35e63;
      margin-left: 5px;
    }

    .delete-btn:hover {
      background: #9d4c50;
    }

    .admin-tag {
      color: #888;
      font-style: italic;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>AYOKO NA</h1>
    <a href="logout.php">LOGOUT</a>
  </div>

  <div class="main-container">
    <div class="sidebar">
      <h2>AYOKO NA</h2>
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="admin_users.php" class="active">User Management</a>
      <a href="admin_rants.php">Rant Management</a>
      <a href="admin_reports.php">Reported Post</a>
    </div>

    <div class="content">
      <h2>USER MANAGEMENT</h2>
      <table>
        <tr>
          <th>USER ID</th>
          <th>USERNAME</th>
          <th>EMAIL</th>
          <th>CREATED AT</th>
          <th>ROLE</th>
          <th>ACTION</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['id']); ?></td>
          <td>@<?= htmlspecialchars($user['username']); ?></td>
          <td><?= htmlspecialchars($user['email']); ?></td>
          <td><?= date('m/d/y', strtotime($user['created_at'])); ?></td>
          <td><?= htmlspecialchars($user['role']); ?></td>
          <td>
            <?php if ($user['role'] !== 'admin'): ?>
              <?php if ($user['role'] === 'banned'): ?>
                <span style="color:red; font-weight:bold;">Banned</span>
                <form action="unban_user.php" method="POST" style="display:inline;" onsubmit="return confirm('Unban this user?');">
                  <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                  <button type="submit" class="action-btn unban-btn">Unban</button>
                </form>
              <?php else: ?>
                <form action="ban_user.php" method="POST" style="display:inline;" onsubmit="return confirm('Ban this user?');">
                  <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                  <button type="submit" class="action-btn ban-btn">Ban</button>
                </form>

                <form action="unban_user.php" method="POST" style="display:inline;" onsubmit="return confirm('Unban this user?');">
                  <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                  <button type="submit" class="action-btn unban-btn">Unban</button>
                </form>
              <?php endif; ?>

              <form action="delete_user.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this user permanently?');">
                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                <button type="submit" class="action-btn delete-btn">Delete</button>
              </form>
            <?php else: ?>
              <span class="admin-tag">you</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>
</html>
