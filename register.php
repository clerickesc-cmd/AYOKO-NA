<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | AYOKO NA</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
    }

    /* ===== HEADER BAR ===== */
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

    /* ===== MAIN CONTAINER ===== */
    main {
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      height: calc(100vh - 120px);
    }

    /* ===== REGISTER BOX ===== */
    .register-box {
      background-color: #e7e5eb;
      width: 460px;
      padding: 70px 80px;
      text-align: center;
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
      border-radius: 3px;
    }

    .register-box h1 {
      font-size: 42px;
      color: #8f6cb3;
      letter-spacing: 8px;
      margin-bottom: 12px;
    }

    .register-box p {
      font-size: 12px;
      color: #7a6799;
      line-height: 1.7;
      letter-spacing: 0.6px;
      margin-bottom: 45px;
    }

    /* ===== INPUT FIELDS ===== */
    .input-group {
      display: flex;
      align-items: center;
      border: 2px solid #8f6cb3;
      margin-bottom: 22px;
      background-color: #f4f4f4;
      height: 45px;
      position: relative;
    }

    .input-group .icon {
      width: 50px;
      text-align: center;
      font-size: 18px;
      color: #6f5b91;
      border-right: 2px solid #8f6cb3;
    }

    .input-group input {
      flex: 1;
      border: none;
      outline: none;
      background: none;
      font-size: 12px;
      padding: 11px 10px;
      color: #5d4c80;
    }

    .input-group input::placeholder {
      text-transform: uppercase;
      font-size: 11px;
      color: #8f6cb3;
      letter-spacing: 1px;
    }

    /* ===== SHOW PASSWORD ICON ===== */
    .show-password {
      position: absolute;
      right: 12px;
      color: #8f6cb3;
      cursor: pointer;
      font-size: 16px;
      transition: color 0.2s;
    }

    .show-password:hover {
      color: #6f5b91;
    }

    /* ===== BUTTON ===== */
    button.register-btn {
      background-color: #8f6cb3;
      color: #fff;
      border: none;
      width: 100%;
      padding: 10px 0;
      font-size: 12px;
      letter-spacing: 1px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 10px;
    }

    button.register-btn:hover {
      background-color: #7a59a0;
    }

    /* ===== LOGIN LINK ===== */
    .login-link {
      display: block;
      margin-top: 22px;
      font-size: 10.5px;
      color: #8f6cb3;
      text-decoration: none;
      letter-spacing: 0.8px;
    }

    .login-link:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <header>
    <div class="logo">AYOKO NA</div>
    <nav>
      <ul>
        <li><a href="index.php">HOME</a></li>
        <li><a href="login.php">LOG IN</a></li>
        <li><a href="about.php">ABOUT US</a></li>
      </ul>
    </nav>
  </header>

  <!-- REGISTER FORM -->
  <main>
    <div class="register-box">
      <h1>REGISTER</h1>
      <p>Create your anonymous account.<br>
         Start expressing your thoughts safely and freely.<br>
         We’ll never show your identity.</p>

      <form action="register_process.php" method="POST">
        <div class="input-group">
          <div class="icon"><i class="fa-solid fa-user"></i></div>
          <input type="text" 
          name="username" 
          placeholder="USERNAME" 
          
          required>
        </div>

        <div class="input-group">
          <div class="icon"><i class="fa-solid fa-envelope"></i></div>
          <input type="email"
          name="email" 
          placeholder="EMAIL" 
          mimnlength="6"
          required>
        </div>

        <!-- PASSWORD FIELD WITH TOGGLE ICON -->
        <div class="input-group">
          <div class="icon"><i class="fa-solid fa-lock"></i></div>
          <input type="password" 
          id="password" 
          name="password" 
          placeholder="PASSWORD" 
          minlength="6"
          maxlength="20"
          required>
          <i class="fa-regular fa-eye show-password" id="togglePassword"></i>
        </div>

        <button type="submit" class="register-btn">REGISTER</button>
      </form>

      <a href="login.php" class="login-link">ALREADY HAVE AN ACCOUNT?</a>
    </div>
  </main>

  <!-- ===== JavaScript for Show/Hide Password ===== -->
  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      togglePassword.classList.toggle('fa-eye');
      togglePassword.classList.toggle('fa-eye-slash');
    });
  </script>

</body>
</html>
