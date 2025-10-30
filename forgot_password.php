<?php
require 'senku_db.php';
session_start();

$message = "";

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    if (!empty($email)) {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // You can add PHPMailer here to send a real reset link
            $message = "If your email exists, a reset link has been sent.";
        } else {
            $message = "If your email exists, a reset link has been sent.";
        }
    } else {
        $message = "Please enter your email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password | AYOKO NA</title>
  <link href="https://fonts.googleapis.com/css2?family=Karla:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Karla', sans-serif;
    }

    body {
      background-color: #d3d3d3;
    }

    header {
      background-color: #8a63cc;
      padding: 20px 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }

    header h1 {
      font-size: 28px;
      letter-spacing: 3px;
    }

    nav a {
      color: white;
      margin-left: 25px;
      text-decoration: none;
      letter-spacing: 1px;
      font-weight: 400;
      font-size: 15px;
    }

    nav a:hover {
      text-decoration: underline;
    }

    .forgot-box {
      width: 400px;
      background: #ececec;
      padding: 40px;
      margin: 100px auto;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      border-radius: 5px;
    }

    .forgot-box h2 {
      color: #8a63cc;
      font-size: 26px;
      letter-spacing: 2px;
      margin-bottom: 15px;
    }

    .forgot-box p {
      color: #7d5fa5;
      font-size: 13px;
      margin-bottom: 25px;
      line-height: 1.4;
    }

    .input-group {
      display: flex;
      align-items: center;
      border: 2px solid #8a63cc;
      padding: 8px;
      border-radius: 2px;
      background: white;
      margin-bottom: 25px;
    }

    .input-group i {
      background-color: #8a63cc;
      color: white;
      padding: 10px;
      font-size: 16px;
    }

    .input-group input {
      flex: 1;
      border: none;
      outline: none;
      padding: 10px;
      font-size: 14px;
      color: #333;
    }

    .btn {
      background-color: #8a63cc;
      color: white;
      border: none;
      padding: 12px 0;
      width: 100%;
      font-size: 13px;
      letter-spacing: 1px;
      cursor: pointer;
      border-radius: 2px;
    }

    .btn:hover {
      background-color: #7a53bc;
    }

    .back {
      display: block;
      margin-top: 15px;
      font-size: 12px;
      color: #8a63cc;
      text-decoration: none;
      letter-spacing: 1px;
    }

    .back:hover {
      text-decoration: underline;
    }

    .message {
      margin-top: 10px;
      font-size: 13px;
      color: #333;
    }

    @media (max-width: 480px) {
      .forgot-box {
        width: 90%;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>AYOKO NA</h1>
    <nav>
      <a href="index.php">HOME</a>
      <a href="register.php">REGISTER</a>
      <a href="about.php">ABOUT US</a>
    </nav>
  </header>

  <div class="forgot-box">
    <h2>FORGOT PASSWORD</h2>
    <p>Oops, nakalimot?<br>Drop your email and we’ll send you a reset link.<br>Balik ka na ulit sa paglalabas ng sama ng loob.</p>
    
    <form method="POST" action="">
      <div class="input-group">
        <i>✉️</i>
        <input type="email" name="email" placeholder="EMAIL" required>
      </div>
      <button type="submit" class="btn">REQUEST RESET LINK</button>
    </form>

    <a href="login.php" class="back">BACK TO LOGIN</a>

    <?php if (!empty($message)): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
