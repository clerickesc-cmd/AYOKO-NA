<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = connectPDO();

// Fetch all rants, including deleted (assuming deleted posts have status='deleted')
$stmt = $pdo->query("
    SELECT m.id, m.content, m.created_at, u.username, m.status
    FROM messages m
    JOIN users u ON m.user_id = u.id
    ORDER BY m.id DESC
");
$rants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rant Management</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            margin: 0;
            background-color: #cfcfcf;
        }
        .header {
            background-color: #8a63b3;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            letter-spacing: 2px;
        }
        .logout-btn {
            color: white;
            background-color: #8a63b3;
            border: none;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 13px;
            padding: 5px 15px;
            cursor: pointer;
        }
        .main-container {
            display: flex;
            gap: 20px;
            padding: 30px;
        }
        .sidebar {
            background-color: #e0dfdf;
            border: 2px solid #8a63b3;
            border-radius: 15px;
            padding: 30px 20px;
            width: 220px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }
        .sidebar h2 {
            color: #6e4b9d;
            letter-spacing: 3px;
            font-size: 20px;
            font-weight: 700;
        }
        .sidebar a {
            display: block;
            width: 100%;
            background-color: #d3d3d3;
            color: #333;
            text-align: center;
            padding: 10px 0;
            border-radius: 10px;
            text-transform: uppercase;
            text-decoration: none;
            font-size: 12px;
            letter-spacing: 1px;
            transition: 0.3s;
            font-weight: 500;
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
            background-color: #e0dfdf;
            border: 2px solid #8a63b3;
            border-radius: 15px;
            padding: 50px;
        }
        .dashboard-content h2 {
            text-align: center;
            letter-spacing: 3px;
            font-size: 30px;
            font-weight: 600;
            margin-bottom: 50px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #f5f5f5;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th {
            background-color: #d6d6d6;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #bdbdbd;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            font-size: 14px;
        }
        tr:last-child td {
            border-bottom: none;
        }
        button {
            background-color: #8a63b3;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
        }
        button:hover {
            background-color: #734fa0;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>AYOKO NA</h1>
    <a href="logout.php" class="logout-btn">LOGOUT</a>
</div>

<div class="main-container">
    <div class="sidebar">
        <h2>AYOKO NA</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_users.php">User Management</a>
        <a href="admin_rants.php" class="active">Rant Management</a>
        <a href="admin_reports.php">Reported Post</a>
    </div>

    <div class="dashboard-content">
        <h2>RANT MANAGEMENT</h2>
        <table>
            <tr>
                <th>POST ID</th>
                <th>USER</th>
                <th>CONTENT PREVIEW</th>
                <th>DATE</th>
                <th>ACTION</th>
            </tr>
            <?php foreach ($rants as $rant): ?>
            <tr>
                <td><?= $rant['id']; ?></td>
                <td>@<?= htmlspecialchars($rant['username']); ?></td>
                <td><?= substr(htmlspecialchars($rant['content']), 0, 20); ?>....</td>
                <td><?= date('m/d/y', strtotime($rant['created_at'])); ?></td>
                <td>
                    <form action="delete_rants.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this rant?');" style="display:inline;">
    <input type="hidden" name="rant_id" value="<?= $rant['id']; ?>">
    <button type="submit">Delete</button>
</form>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

</body>
</html>
