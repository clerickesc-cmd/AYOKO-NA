<?php
require 'database.php';
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = connectPDO();

// Existing stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRants = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$activeTodayStmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) FROM messages WHERE DATE(created_at) = CURDATE()");
$activeTodayStmt->execute();
$activeUsersToday = $activeTodayStmt->fetchColumn();

// 🆕 New stats
$totalBanned = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'banned'")->fetchColumn();
$totalReported = $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn();
$totalFeedback = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
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
    }

    .sidebar h2 {
      color: #6e4b9d;
      letter-spacing: 3px;
      font-weight: 700;
      font-size: 20px;
    }

    .sidebar a {
      display: block;
      background-color: #d6d6d6;
      border-radius: 15px;
      padding: 10px 0;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #3c3c3c;
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
    }

    .dashboard-content {
      flex-grow: 1;
      background-color: #e5e2e2;
      border: 2px solid #8a63b3;
      border-radius: 15px;
      padding: 50px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 25px;
      margin-top: 50px;
    }

    .stat-card {
      background-color: white;
      border: 2px solid #8a63b3;
      border-radius: 15px;
      padding: 30px 20px;
      text-align: center;
      box-shadow: 0 5px 10px rgba(0,0,0,0.15);
      transition: transform 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-card i {
      font-size: 35px;
      color: #8a63b3;
      margin-bottom: 10px;
    }

    .stat-card h3 {
      font-size: 14px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #000;
      margin: 10px 0 5px;
    }

    .stat-card p {
      font-size: 18px;
      font-weight: bold;
      color: #000;
    }

    @media (max-width: 768px) {
      .main-container {
        flex-direction: column;
        align-items: center;
      }
      .sidebar {
        width: 80%;
      }
      .dashboard-content {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>AYOKO NA</h1>
    <a href="logout.php">LOGOUT</a>
  </div>

  <div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>AYOKO NA</h2>
      <a href="admin_dashboard.php" class="active">Dashboard</a>
      <a href="admin_users.php">User Management</a>
      <a href="admin_rants.php">Rant Management</a>
      <a href="admin_reports.php">Reported Post</a>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa-solid fa-comment-dots"></i>
          <h3>Total Rants</h3>
          <p><?= $totalRants ?></p>
        </div>

        <div class="stat-card">
          <i class="fa-solid fa-user-clock"></i>
          <h3>Active Users</h3>
          <p><?= $activeUsersToday ?></p>
        </div>

        <div class="stat-card">
          <i class="fa-solid fa-users"></i>
          <h3>Total Users</h3>
          <p><?= $totalUsers ?></p>
        </div>

        <div class="stat-card">
          <i class="fa-solid fa-user-slash"></i>
          <h3>Total Banned</h3>
          <p><?= $totalBanned ?></p>
        </div>

        <div class="stat-card">
          <i class="fa-solid fa-flag"></i>
          <h3>Total Reported</h3>
          <p><?= $totalReported ?></p>
        </div>

        <div class="stat-card">
          <i class="fa-solid fa-message"></i>
          <h3>Total Feedback</h3>
          <p><?= $totalFeedback ?></p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
