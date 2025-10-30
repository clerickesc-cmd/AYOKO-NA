<?php
// forgot_process.php
require 'database.php';
session_start();

$pdo = connectPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    if (!$email) {
        // invalid email
        echo "<script>alert('Please enter a valid email address.'); window.location.href='forgot_password.php';</script>";
        exit;
    }

    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            // To avoid user enumeration, show success message regardless
            echo "<script>alert('If that email exists in our system, a reset link has been sent.'); window.location.href='forgot_password.php';</script>";
            exit;
        }

        // Generate secure token
        $token = bin2hex(random_bytes(32)); // 64 hex chars
        $expiry = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

        // Save token and expiry
        $update = $pdo->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE id = :id");
        $update->execute([
            'token' => $token,
            'expiry' => $expiry,
            'id' => $user['id']
        ]);

        // Build reset link (adjust domain/path as needed)
        $resetLink = sprintf(
            "%s/reset_password.php?token=%s",
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'],
            $token
        );

        // Send email via PHPMailer
        // -- configure SMTP settings below --
        // Note: Composer/autoload recommended. See instructions after code.
        require 'vendor/autoload.php'; // if installed with composer

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // SMTP server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.example.com';      // e.g. smtp.gmail.com
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your-smtp-username';    // SMTP username
            $mail->Password   = 'your-smtp-password';    // SMTP password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // or 'ssl'
            $mail->Port       = 587; // 465 for ssl

            $mail->setFrom('no-reply@yourdomain.com', 'AYOKO NA');
            $mail->addAddress($email, $user['username']);

            $mail->isHTML(true);
            $mail->Subject = 'AYOKO NA — Password Reset Request';
            $mail->Body    = "
                <p>Hi " . htmlspecialchars($user['username']) . ",</p>
                <p>We received a request to reset your password. Click the link below to reset it (expires in 1 hour):</p>
                <p><a href='" . htmlspecialchars($resetLink) . "'>Reset your password</a></p>
                <p>If you didn't request this, ignore this message.</p>
            ";
            $mail->AltBody = "Reset your password: $resetLink";

            $mail->send();
        } catch (Exception $e) {
            // In production, log $e->getMessage()
            // still show the generic message to user
        }

        // Generic success message (prevents user enumeration)
        echo "<script>alert('If that email exists in our system, a reset link has been sent.'); window.location.href='forgot_password.php';</script>";
        exit;
    } catch (Exception $e) {
        // log error in production
        echo "<script>alert('An error occurred. Please try again later.'); window.location.href='forgot_password.php';</script>";
        exit;
    }
} else {
    header('Location: forgot_password.php');
    exit;
}
