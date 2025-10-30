<?php
require 'database.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = connectPDO();

// Fetch user info
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap');
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    background-color: #d6d6d6;
}
header {
    background-color: #8a63b3;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 60px;
}
.logo {
    font-size: 1.8em;
    letter-spacing: 3px;
    font-weight: 600;
}
nav a {
    color: white;
    text-decoration: none;
    margin-left: 25px;
    font-size: 0.9em;
    letter-spacing: 2px;
    text-transform: uppercase;
    transition: opacity 0.3s;
}
nav a:hover { opacity: 0.7; }
main {
    display: flex;
    justify-content: center;
    align-items: center;
    height: calc(100vh - 100px);
}
.settings-card {
    background: #e6e6e6;
    width: 420px;
    padding: 40px 50px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    text-align: center;
}
.settings-card h1 {
    font-size: 2.2em;
    color: #8a63b3;
    letter-spacing: 5px;
    margin-bottom: 20px;
}
.user-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #8a63b3;
    margin-bottom: 30px;
}
.user-info i { font-size: 1.2em; }
.settings-options {
    text-align: left;
    display: flex;
    flex-direction: column;
    gap: 18px;
    font-size: 0.8em;
    color: #444;
    letter-spacing: 1px;
}
.option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
}
.option i { width: 20px; margin-right: 10px; color: #222; }
.option span { flex: 1; font-size: 0.75em; }
.switch {
    position: relative;
    display: inline-block;
    width: 35px;
    height: 18px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0;
    right: 0; bottom: 0;
    background-color: #ccc;
    border-radius: 34px;
    transition: .4s;
}
.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}
input:checked + .slider { background-color: #8a63b3; }
input:checked + .slider:before { transform: translateX(17px); }
.logout {
    margin-top: 35px;
    font-size: 0.85em;
    color: #222;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: opacity 0.3s;
}
.logout:hover { opacity: 0.6; }

/* MODALS */
.modal {
    display: none;
    position: fixed;
    z-index: 10;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.4);
}
.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 24px 28px 28px;
    border-radius: 14px;
    width: 420px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    text-align: center;
    position: relative;
}
.modal-content h3 {
    color: #8a63b3;
    margin-bottom: 12px;
    font-size: 1.4em;
}
.password-field {
    position: relative;
    width: 100%;
}
.password-field input {
    display: block;
    width: 100%;
    padding: 12px 44px 12px 12px;
    border-radius: 8px;
    border: 1px solid #cfcfcf;
    font-family: inherit;
    font-size: 0.95em;
}
.password-field .eye-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #8a63b3;
    cursor: pointer;
}
.close {
    position: absolute;
    right: 12px;
    top: 10px;
    color: #aaa;
    font-size: 18px;
    cursor: pointer;
}
.modal-content button {
    width: 100%;
    background-color: #8a63b3;
    border: none;
    color: white;
    padding: 12px;
    border-radius: 8px;
    font-size: 0.95em;
    cursor: pointer;
}
.message {
    text-align: center;
    font-size: 0.9em;
    margin-bottom: 8px;
}
.message.success { color: green; }
.message.error { color: red; }

#successPopup {
    display: none;
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 10px;
    padding: 18px 32px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.18);
    text-align: center;
    z-index: 100;
}
#successPopup h4 { color: #8a63b3; margin: 0 0 6px; }
#successPopup p { margin: 0; color: #333; font-size: 0.95em; }
</style>
</head>
<body>
<header>
    <div class="logo">AYOKO NA</div>
    <nav>
        <a href="messages.php">MESSAGES</a>
        <a href="settings.php" class="active">SETTINGS</a>
    </nav>
</header>

<main>
    <div class="settings-card">
        <h1>SETTINGS</h1>
        <div class="user-info">
            <i class="fa-solid fa-user"></i>
            <span><?php echo htmlspecialchars($user['username']); ?></span>
        </div>

        <div class="settings-options">
            <div class="option">
                <i class="fa-solid fa-bell"></i>
                <span>NOTIFICATIONS</span>
                <label class="switch">
                    <input type="checkbox" id="notifToggle">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="option" onclick="window.location='your_message.php'">
                <i class="fa-solid fa-envelope"></i>
                <span>YOUR MESSAGES</span>
            </div>

            <div class="option" onclick="window.location='feedback.php'">
                <i class="fa-solid fa-comment-dots"></i>
                <span>FEEDBACK</span>
            </div>

            <div class="option" onclick="openModal('passwordModal')">
                <i class="fa-solid fa-lock"></i>
                <span>CHANGE PASSWORD</span>
            </div>

            <div class="option" onclick="openModal('deleteModal')">
                <i class="fa-solid fa-trash"></i>
                <span>DELETE ACCOUNT</span>
            </div>

            <div class="option" onclick="window.location='about.php'">
                <i class="fa-solid fa-users"></i>
                <span>ABOUT US</span>
            </div>
        </div>

        <!-- ✅ Logout confirmation added here -->
        <form action="logout.php" method="POST" onsubmit="return confirmLogout(event)">
            <button type="submit" class="logout" style="background:none;border:none;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>LOGOUT</span>
            </button>
        </form>
    </div>
</main>

<!-- Password Modal -->
<div id="passwordModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('passwordModal')">&times;</span>
    <h3>Change Password</h3>
    <div id="passwordMessage" class="message"></div>
    <form id="passwordForm">
        <div class="password-field">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <i class="fa-solid fa-eye-slash eye-icon" onclick="togglePassword(this)"></i>
        </div>
        <div class="password-field">
            <input type="password" name="new_password" placeholder="New Password" required>
            <i class="fa-solid fa-eye-slash eye-icon" onclick="togglePassword(this)"></i>
        </div>
        <div class="password-field">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <i class="fa-solid fa-eye-slash eye-icon" onclick="togglePassword(this)"></i>
        </div>
        <button type="submit">Update Password</button>
    </form>
  </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('deleteModal')">&times;</span>
    <h3>Delete Account</h3>
    <div id="deleteMessage" class="message"></div>
    <p style="font-size:0.9em; text-align:center; margin-bottom:12px;">
      Are you sure you want to delete your account?<br>
      This action cannot be undone.
    </p>
    <form id="deleteForm">
        <div class="password-field">
            <input type="password" name="confirm_password" placeholder="Enter your password to confirm" required>
            <i class="fa-solid fa-eye-slash eye-icon" onclick="togglePassword(this)"></i>
        </div>
        <button type="submit">Confirm Delete</button>
    </form>
  </div>
</div>

<!-- Success Popup -->
<div id="successPopup">
  <h4>Success!</h4>
  <p>Password updated successfully.</p>
</div>

<script>
function openModal(id) { document.getElementById(id).style.display = 'block'; }
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    const pm = document.getElementById('passwordMessage');
    const dm = document.getElementById('deleteMessage');
    if (pm) pm.innerHTML = '';
    if (dm) dm.innerHTML = '';
}
window.onclick = function(e) {
    document.querySelectorAll('.modal').forEach(modal => {
        if (e.target == modal) modal.style.display = 'none';
    });
}

function togglePassword(icon) {
    const input = icon.previousElementSibling;
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    }
}

// ✅ CONFIRM LOGOUT FUNCTION
function confirmLogout(event) {
    const confirmAction = confirm("Are you sure you want to log out?");
    if (!confirmAction) {
        event.preventDefault();
        return false;
    }
    return true;
}

// PASSWORD UPDATE
document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await fetch('update_password.php', { method: 'POST', body: new FormData(e.target) });
    const data = await res.json();
    const msg = document.getElementById('passwordMessage');
    msg.innerHTML = data.message || '';
    msg.className = 'message ' + (data.success ? 'success' : 'error');
    if (data.success) {
        e.target.reset();
        showSuccessPopup();
    }
});

// DELETE ACCOUNT
document.getElementById('deleteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!confirm("This will permanently delete your account. Continue?")) return;
    const res = await fetch('delete_account.php', { method: 'POST', body: new FormData(e.target) });
    const data = await res.json();
    const msg = document.getElementById('deleteMessage');
    msg.innerHTML = data.message || '';
    msg.className = 'message ' + (data.success ? 'success' : 'error');
    if (data.success) {
        setTimeout(() => window.location = 'login.php', 1500);
    }
});

// NOTIFICATION TOGGLE
const notifToggle = document.getElementById('notifToggle');
if (notifToggle) {
    notifToggle.addEventListener('change', () => {
        alert(notifToggle.checked ? "Notifications Enabled" : "Notifications Disabled");
    });
}

// SUCCESS POPUP
function showSuccessPopup() {
    const popup = document.getElementById('successPopup');
    popup.style.display = 'block';
    setTimeout(() => { popup.style.display = 'none'; closeModal('passwordModal'); }, 1500);
}
</script>
</body>
</html>
