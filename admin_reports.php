<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = connectPDO();

// ✅ Get all reports with user info, message, and restriction status
$stmt = $pdo->query("
    SELECT 
        r.id AS report_id, 
        u.id AS user_id,
        u.username, 
        u.restricted_until,
        m.id AS message_id,
        m.content AS message, 
        r.reason, 
        r.created_at
    FROM reports r
    JOIN messages m ON r.message_id = m.id
    JOIN users u ON m.user_id = u.id
    ORDER BY r.created_at DESC
");
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reported Posts</title>
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { margin: 0; background-color: #cfcfcf; }
        .header {
            background-color: #8a63b3; color: white;
            padding: 20px 40px; display: flex;
            justify-content: space-between; align-items: center;
            letter-spacing: 2px;
        }
        .logout-btn {
            color: white; background-color: #8a63b3;
            border: none; text-transform: uppercase;
            letter-spacing: 2px; font-size: 13px;
            padding: 5px 15px; cursor: pointer;
        }
        .main-container { display: flex; gap: 20px; padding: 30px; }
        .sidebar {
            background-color: #e0dfdf; border: 2px solid #8a63b3;
            border-radius: 15px; padding: 30px 20px; width: 220px;
            display: flex; flex-direction: column; gap: 20px; align-items: center;
        }
        .sidebar h2 {
            color: #6e4b9d; letter-spacing: 3px; font-size: 20px; font-weight: 700;
        }
        .sidebar a {
            display: block; width: 100%; background-color: #d3d3d3;
            color: #333; text-align: center; padding: 10px 0;
            border-radius: 10px; text-transform: uppercase;
            text-decoration: none; font-size: 12px; letter-spacing: 1px;
            transition: 0.3s; font-weight: 500;
        }
        .sidebar a:hover { background-color: #b89ed7; color: white; }
        .sidebar a.active { background-color: #8a63b3; color: white; }
        .dashboard-content {
            flex-grow: 1; background-color: #e0dfdf;
            border: 2px solid #8a63b3; border-radius: 15px; padding: 50px;
        }
        .dashboard-content h2 {
            text-align: center; letter-spacing: 3px;
            font-size: 30px; font-weight: 600; margin-bottom: 50px;
        }
        table {
            width: 100%; border-collapse: separate; border-spacing: 0;
            background-color: #f5f5f5; border-radius: 15px;
            overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th {
            background-color: #d6d6d6; padding: 12px;
            text-align: center; font-size: 13px; text-transform: uppercase;
            letter-spacing: 1px; border-bottom: 2px solid #bdbdbd;
        }
        td {
            padding: 12px; text-align: center;
            border-bottom: 1px solid #ccc; font-size: 14px;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }

        .action-container {
            display: flex; justify-content: center;
            align-items: center; gap: 10px;
        }
        .action-btn {
            border: none; border-radius: 8px;
            padding: 6px 14px; font-size: 13px;
            font-weight: 600; color: white; cursor: pointer;
            transition: all 0.2s ease-in-out;
            text-transform: uppercase; letter-spacing: 1px;
            min-width: 90px; text-align: center;
        }
        .delete-btn { background-color: #c74e4e; }
        .delete-btn:hover { background-color: #b13f3f; transform: scale(1.05); }
        .restrict-btn { background-color: #e6a93a; }
        .restrict-btn:hover { background-color: #d2931e; transform: scale(1.05); }

        .restricted {
            color: #c74e4e;
            font-weight: bold;
            font-size: 12px;
        }

        /* Popup styling */
        #restrict-popup {
            display: none;
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center; align-items: center;
        }
        .popup-content {
            background: white; padding: 30px; border-radius: 10px;
            text-align: center; width: 300px;
        }
        .popup-content h3 { margin-bottom: 15px; color: #8a63b3; }
        .popup-content select {
            width: 100%; padding: 8px;
            margin-bottom: 20px; border-radius: 6px; border: 1px solid #ccc;
        }
        .popup-btn {
            background-color: #8a63b3; color: white;
            border: none; padding: 8px 15px; border-radius: 8px;
            cursor: pointer; margin: 5px;
        }
        .popup-btn:hover { background-color: #6f4b99; }
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
        <a href="admin_rants.php">Rant Management</a>
        <a href="admin_reports.php" class="active">Reported Post</a>
    </div>

    <div class="dashboard-content">
        <h2>REPORTED POSTS</h2>

        <table>
            <tr>
                <th>USERNAME</th>
                <th>MESSAGE</th>
                <th>REASON</th>
                <th>DATE</th>
                <th>ACTION</th>
            </tr>

            <?php if (count($reports) > 0): ?>
                <?php foreach ($reports as $report): ?>
                <?php
                    $isRestricted = !empty($report['restricted_until']) && strtotime($report['restricted_until']) > time();
                ?>
                <tr>
                    <td>
                        @<?= htmlspecialchars($report['username']); ?><br>
                        <?php if ($isRestricted): ?>
                            <span class="restricted">
                                Restricted until <?= date('M d, Y h:i A', strtotime($report['restricted_until'])); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td><?= substr(htmlspecialchars($report['message']), 0, 25); ?>...</td>
                    <td><?= htmlspecialchars(strtoupper($report['reason'])); ?></td>
                    <td><?= date('m/d/y', strtotime($report['created_at'])); ?></td>
                    <td>
                        <div class="action-container">
                            <?php if (!$isRestricted): ?>
                                <button type="button" 
                                    class="action-btn restrict-btn"
                                    onclick="openRestrictPopup(<?= $report['user_id']; ?>)">
                                    Restrict
                                </button>
                            <?php endif; ?>

                            <form action="delete_report.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this message and report?');">
                                <input type="hidden" name="report_id" value="<?= $report['report_id']; ?>">
                                <input type="hidden" name="message_id" value="<?= $report['message_id']; ?>">
                                <button type="submit" class="action-btn delete-btn">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:20px;">No reported posts.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Restrict Popup -->
<div id="restrict-popup">
    <div class="popup-content">
        <h3>Restrict User</h3>
        <form id="restrict-form" action="restrict_user.php" method="POST">
            <input type="hidden" name="user_id" id="popup-user-id">
            <label for="duration">Select Restriction Duration:</label><br>
            <select name="hours" id="duration" required>
                <option value="1">1 Hour</option>
                <option value="6">6 Hours</option>
                <option value="12" selected>12 Hours</option>
                <option value="24">24 Hours</option>
            </select>
            <br>
            <button type="submit" class="popup-btn">Confirm</button>
            <button type="button" class="popup-btn" onclick="closeRestrictPopup()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openRestrictPopup(userId) {
    document.getElementById('popup-user-id').value = userId;
    document.getElementById('restrict-popup').style.display = 'flex';
}
function closeRestrictPopup() {
    document.getElementById('restrict-popup').style.display = 'none';
}
</script>

</body>
</html>
