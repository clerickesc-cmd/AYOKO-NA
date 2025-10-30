<?php
session_start();
require 'database.php';
$pdo = connectPDO();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us | AYOKO NA</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    /* ===== RESET & BASE ===== */
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

    /* ===== HEADER BAR ===== */
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

    /* ===== ABOUT SECTION ===== */
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
    }

    .about-box {
      background-color: #e7e5eb;
      padding: 70px 80px;
      border-radius: 5px;
      max-width: 700px;
      text-align: center;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .about-box h1 {
      font-size: 42px;
      color: #8f6cb3;
      letter-spacing: 8px;
      margin-bottom: 15px;
    }

    .about-box p {
      color: #5d4c80;
      font-size: 14px;
      line-height: 1.8;
      margin-bottom: 20px;
      letter-spacing: 0.4px;
    }

    .about-box strong {
      color: #7a59a0;
    }

    .about-box em {
      color: #8f6cb3;
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
  <!-- HEADER -->
  <header>
    <div class="logo">AYOKO NA</div>
    <nav>
      <ul>
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li><a href="messages.php">MESSAGES</a></li>
          <li><a href="settings.php">SETTINGS</a></li>
        <?php else: ?>
          <li><a href="login.php">LOGIN</a></li>
          <li><a href="register.php">REGISTER</a></li>
        <?php endif; ?>
        <li><a href="about.php">ABOUT US</a></li>
      </ul>
    </nav>
  </header>

  <!-- ABOUT SECTION -->
  <main>
    <div class="about-box">
      <h1>ABOUT US</h1>
      <p><strong>AYOKO NA</strong> is your safe and anonymous online space — a place where you can share your emotions freely without fear or judgment.</p>
      <p>We believe that expressing how you feel, even anonymously, can bring peace and healing. Whether you're happy, sad, tired, or frustrated — your voice matters here.</p>
      <p>Every message, every rant, every emotion is valid. Our mission is to provide comfort through words and remind everyone that <strong>you are not alone</strong>.</p>
      <p>💜 <em>Speak freely. Heal quietly. You are safe here.</em></p>
    </div>
  </main>

  <footer>© 2025 AYOKO NA — All Rights Reserved</footer>
</body>
</html>
