<?php
session_start();

// ✅ DATABASE CONNECTION
function connectPDO() {
    $host = 'localhost';
    $db   = 'senku_db'; // change to your database name
    $user = 'root';    // change to your MySQL username
    $pass = '';        // change to your MySQL password
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

$pdo = connectPDO();
$message = "";

// ✅ VERIFY TOKEN
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $resetData = $stmt->fetch();

    if ($resetData && $resetData['expires'] >= time()) {
        $email = $resetData['email'];
    } else {
        $message = "⚠️ Invalid or expired token.";
    }
} else {
    $message = "⚠️ No token provided.";
}

// ✅ HANDLE PASSWORD RESET
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_password'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $token = $_POST['token'];

    // Check token validity again
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $resetData = $stmt->fetch();

    if ($resetData && $resetData['expires'] >= time()) {
        $email = $resetData['email'];

        if ($newPassword === $confirmPassword && strlen($newPassword) >= 6) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password
            $update = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $update->execute(['password' => $hashed, 'email' => $email]);

            // Delete used token
            $pdo->prepare("DELETE FROM password_resets WHERE email = :email")->execute(['email' => $email]);

            $message = "✅ Password updated successfully! You can now <a href='login.php'>login</a>.";
        } else {
            $message = "⚠️ Passwords do not match or are too short.";
        }
    } else {
        $message = "⚠️ Invalid or expired reset link.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password | AYOKO NA</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Roboto', sans-serif;
}

body {
  background-color: #d9d9d9;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* HEADER */
header {
  background-color: #8a5cd9;
  color: white;
  padding: 20px 0;
}

header .container {
  width: 90%;
  max-width: 1000px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

header h1 {
  font-weight: 500;
  letter-spacing: 3px;
}

header nav a {
  color: white;
  margin-left: 25px;
  text-decoration: none;
  font-size: 15px;
  letter-spacing: 1px;
  transition: opacity 0.3s;
}

header nav a:hover {
  opacity: 0.8;
}

/* MAIN */
main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.reset-box {
  background: #f2f2f2;
  padding: 50px 60px;
  text-align: center;
  border-radius: 5px;
  box-shadow: 0px 3px 8px rgba(0,0,0,0.2);
  max-width: 420px;
  width: 100%;
}

.reset-box h2 {
  color: #8a5cd9;
  font-size: 25px;
  letter-spacing: 3px;
  margin-bottom: 10px;
}

.reset-box .subtitle {
  font-size: 12px;
  color: #7c6d8a;
  margin-bottom: 25px;
  line-height: 1.5;
}

.input-box {
  border: 2px solid #8a5cd9;
  margin-bottom: 15px;
}

.input-box input {
  width: 100%;
  padding: 10px;
  border: none;
  outline: none;
  background: none;
  font-size: 13px;
  color: #444;
}

button {
  background: #8a5cd9;
  color: white;
  border: none;
  padding: 10px 0;
  width: 100%;
  cursor: pointer;
  font-size: 12px;
  letter-spacing: 1px;
  transition: background 0.3s;
}

button:hover {
  background: #754fc4;
}

.message {
  color: #8a5cd9;
  font-size: 12px;
  margin-bottom: 15px;
}

.back {
  display: block;
  margin-top: 20px;
  font-size: 10px;
  text-decoration: none;
  color: #8a5cd9;
  letter-spacing: 1px;
}
</style>
</head>
<body>
<header>
  <div class="container">
    <h1>AYOKO NA</h1>
    <nav>
      <a href="index.php">HOME</a>
      <a href="register.php">REGISTER</a>
      <a href="about.php">ABOUT US</a>
    </nav>
  </div>
</header>

<main>
  <div class="reset-box">
    <h2>RESET PASSWORD</h2>
    <p class="subtitle">Enter your new password below.</p>

    <?php if (!empty($message)): ?>
      <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <?php if (isset($email) && empty($message)): ?>
    <form method="POST">
      <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
      <div class="input-box">
        <input type="password" name="new_password" placeholder="NEW PASSWORD" required minlength="6">
      </div>
      <div class="input-box">
        <input type="password" name="confirm_password" placeholder="CONFIRM PASSWORD" required minlength="6">
      </div>
      <button type="submit">UPDATE PASSWORD</button>
    </form>
    <?php endif; ?>

    <a href="login.php" class="back">BACK TO LOGIN</a>
  </div>
</main>
</body>
</html>
