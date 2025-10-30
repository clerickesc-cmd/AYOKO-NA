<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home | AYOKO NA</title>

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
      align-items: center;
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

    /* underline hover animation */
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

    /* ===== MAIN CONTENT ===== */
    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 80px 20px;
    }

    h1 {
      font-size: 48px;
      color: #8f6cb3;
      letter-spacing: 10px;
      margin-bottom: 25px;
    }

    p {
      max-width: 700px;
      font-size: 13px;
      color: #6a558d;
      line-height: 1.8;
      margin-bottom: 40px;
    }

    .btn-group {
      display: flex;
      gap: 20px;
    }

    .btn {
      background-color: #8f6cb3;
      color: #fff;
      border: none;
      padding: 12px 32px;
      font-size: 13px;
      letter-spacing: 1px;
      border-radius: 2px;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #7a59a0;
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
        <li><a href="register.php">REGISTER</a></li>
        <li><a href="about.php">ABOUT US</a></li>
      </ul>
    </nav>
  </header>

  <!-- MAIN CONTENT -->
  <main>
    <h1>WELCOME TO AYOKO NA</h1>
    <p>
      Ayoko Na is a safe space for anyone in the academic grind — students, teachers, staff, researchers — lahat ng pagod at sawa. 
      Rant mo na 'yan: tungkol sa deadlines, toxic workload, unfair systems, o kahit sa Zoom na ayaw mag-connect. 
      No judgment, no need to explain. Just real talk from people who get it.
    </p>

    <div class="btn-group">
      <a href="register.php" class="btn">GET STARTED</a>
      <a href="login.php" class="btn">LOGIN</a>
    </div>
  </main>

  <footer>
    © 2025 AYOKO NA — ALL RIGHTS RESERVED
  </footer>
</body>
</html>
