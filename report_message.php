<?php
require 'database.php';
session_start();

$pdo = connectPDO();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message_content = isset($_GET['message']) ? $_GET['message'] : '';

$stmt = $pdo->prepare("SELECT content FROM messages WHERE id = ?");
$stmt->execute([$message_id]);
$message = $stmt->fetchColumn();

if (!$message) {
    $message = htmlspecialchars($message_content);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>REPORT</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap');

    body {
      margin: 0;
      font-family: 'Open Sans', sans-serif;
      background-color: #d9d9d9;
      color: #4a4a4a;
    }

    .header {
      background-color: #7b5fa6;
      color: white;
      padding: 20px 30px;
      display: flex;
      align-items: center;
      justify-content: flex-start;
    }

    .header h1 {
      font-size: 26px;
      letter-spacing: 3px;
      margin: 0;
      font-weight: 600;
    }

    .report-section {
      max-width: 1000px;
      margin: 40px auto;
      background: #d9d9d9;
      padding: 30px;
      border-radius: 10px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: flex-start;
    }

    .report-left {
      flex: 1;
      min-width: 320px;
    }

    .report-left h2 {
      color: #d32f2f;
      font-weight: 800;
      font-size: 22px;
      letter-spacing: 2px;
      margin-bottom: 20px;
      text-transform: uppercase;
    }

    .message-box {
      background: #eaeaea;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 25px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
      color: #222;
      font-size: 15px;
      line-height: 1.5;
    }

    .message-inner {
      background: #fff;
      border: 2px solid #c7b3e0;
      border-radius: 10px;
      padding: 15px 18px;
      color: #4c3d73;
      line-height: 1.6;
      word-wrap: break-word;
      max-height: 200px;
      overflow-y: auto;
    }

    .message-inner::-webkit-scrollbar {
      width: 6px;
    }
    .message-inner::-webkit-scrollbar-thumb {
      background: #b7a3d8;
      border-radius: 4px;
    }

    .instructions {
      color: #7b5fa6;
      font-size: 17px;
      font-weight: 500;
      letter-spacing: 1px;
      line-height: 1.7;
      margin-bottom: 40px;
    }

    .report-right {
      flex: 1;
      min-width: 300px;
      text-align: center;
    }

    .report-right h3 {
      color: #7b5fa6;
      font-weight: 700;
      margin-bottom: 25px;
      letter-spacing: 1px;
    }

    .report-options {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
    }

    .report-options button {
      width: 80%;
      background: #eaeaea;
      border: none;
      border-radius: 20px;
      padding: 12px;
      cursor: pointer;
      font-size: 15px;
      color: #333;
      box-shadow: 0 2px 3px rgba(0,0,0,0.15);
      transition: all 0.2s ease-in-out;
      letter-spacing: 1px;
    }

    .report-options button:hover {
      background: #c0c0c0;
    }

    .cancel-btn {
      background: #7b5fa6;
      color: white;
      border: none;
      border-radius: 20px;
      padding: 10px 25px;
      margin-top: 40px;
      cursor: pointer;
      font-size: 14px;
      letter-spacing: 1px;
    }

    .cancel-btn:hover {
      background: #654b90;
    }

    .popup {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .popup-content {
      background: #e9e9e9;
      border-radius: 15px;
      padding: 40px 60px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      width: 500px;
      max-width: 90%;
    }

    .popup h2 {
      color: #d32f2f;
      letter-spacing: 2px;
    }

    .popup h3 {
      color: #7b5fa6;
      letter-spacing: 1px;
    }

    .popup button {
      background: #7b5fa6;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px 25px;
      margin: 10px;
      cursor: pointer;
      font-size: 14px;
      letter-spacing: 1px;
    }

    .popup button:hover {
      background: #654b90;
    }

    .other-popup {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      justify-content: center;
      align-items: center;
      z-index: 1500;
    }

    .other-box {
      background: #f5f5f5;
      border-radius: 15px;
      padding: 50px 70px;
      text-align: center;
      width: 600px;
      max-width: 95%;
      box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    }

    .other-box h2 {
      color: #ff0000;
      font-size: 30px;
      font-weight: 800;
      letter-spacing: 2px;
      margin-bottom: 10px;
    }

    .other-box p {
      color: #7b5fa6;
      font-size: 15px;
      margin-bottom: 20px;
      letter-spacing: 0.5px;
    }

    textarea {
      width: 100%;
      height: 150px;
      border-radius: 10px;
      border: none;
      resize: none;
      background: #f9f9f9;
      padding: 15px;
      font-size: 14px;
      color: #555;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }

    .other-box .btns {
      display: flex;
      justify-content: center;
      margin-top: 20px;
      gap: 20px;
    }

    .other-box button {
      background: #7b5fa6;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px 25px;
      cursor: pointer;
      letter-spacing: 1px;
      font-size: 14px;
    }

    .other-box button:hover {
      background: #654b90;
    }

    /* ✅ Success Popup Style */
    .success-popup {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>AYOKO NA</h1>
  </div>

  <div class="report-section">
    <div class="report-left">
      <h2>REPORT “ANONYMOUS<?= rand(1000,9999); ?>”?</h2>

      <div class="message-box">
        <div class="message-inner">
          <?= nl2br(htmlspecialchars($message)); ?>
        </div>
      </div>

      <div class="instructions">
        Help us keep our community safe.<br>
        Tell us what’s wrong with this post or user, and<br>
        we’ll review it as soon as possible.
      </div>

      <button class="cancel-btn" onclick="window.location='messages.php'">CANCEL</button>
    </div>

    <div class="report-right">
      <h3>Select a problem to report.</h3>
      <div class="report-options">
        <button onclick="openConfirmation('Harassment')">Harassment</button>
        <button onclick="openConfirmation('Suicide or self-injury')">Suicide or self-injury</button>
        <button onclick="openConfirmation('Pretending to be someone else')">Pretending to be someone else</button>
        <button onclick="openConfirmation('Violence or dangerous organizations')">Violence or dangerous organizations</button>
        <button onclick="openConfirmation('Sexual activity')">Sexual activity</button>
        <button onclick="openConfirmation('Selling or promoting restricted items')">Selling or promoting restricted items</button>
        <button onclick="openConfirmation('Spam')">Spam</button>
        <button onclick="openOtherPopup()">Other</button>
      </div>
    </div>
  </div>

  <div class="popup" id="confirmationPopup">
    <div class="popup-content">
      <h2>REPORT “ANONYMOUS0214”?</h2>
      <h3>FOR “<span id="selectedReason"></span>”?</h3>
      <button onclick="closePopup()">CANCEL</button>
      <button onclick="submitReport()">SUBMIT</button>
      <input type="hidden" id="hiddenReason">
    </div>
  </div>

  <div class="other-popup" id="otherPopup">
    <div class="other-box">
      <h2>REPORT “ANONYMOUS0214”?</h2>
      <p>Provide additional context about your report. Please be as specific as possible to help us investigate the issue.</p>
      <textarea id="otherMessage" placeholder="Message"></textarea>
      <div class="btns">
        <button onclick="closeOtherPopup()">CANCEL</button>
        <button onclick="submitOtherReport()">SUBMIT</button>
      </div>
    </div>
  </div>

  <!-- ✅ Success Popup -->
  <div class="popup success-popup" id="successPopup">
    <div class="popup-content" style="background:#fff;border-radius:20px;padding:60px 40px;text-align:center;max-width:600px;">
      <h2 style="color:#32a852;font-size:36px;letter-spacing:3px;margin-bottom:15px;">REPORT SUBMITTED<br>SUCCESSFULLY</h2>
      <p style="color:#7b5fa6;font-size:16px;letter-spacing:0.5px;line-height:1.6;margin-bottom:35px;">
        Thank you for helping us maintain a safe and respectful community.<br>
        Our moderation team will review your report soon.
      </p>
      <button style="background:#7b5fa6;color:#fff;border:none;border-radius:10px;padding:12px 25px;cursor:pointer;letter-spacing:1px;"
        onclick="closeSuccessPopup()">RETURN TO MESSAGES</button>
    </div>
  </div>

  <script>
  function openConfirmation(reason) {
    document.getElementById("selectedReason").textContent = reason;
    document.getElementById("hiddenReason").value = reason;
    document.getElementById("confirmationPopup").style.display = "flex";
  }

  function closePopup() {
    document.getElementById("confirmationPopup").style.display = "none";
  }

  function openOtherPopup() {
    document.getElementById("otherPopup").style.display = "flex";
  }

  function closeOtherPopup() {
    document.getElementById("otherPopup").style.display = "none";
  }

  function showSuccessPopup() {
    document.getElementById("successPopup").style.display = "flex";
  }

  function closeSuccessPopup() {
    window.location = "messages.php";
  }

  function submitOtherReport() {
    const reason = "Other";
    const message = document.getElementById("otherMessage").value.trim();
    const messageId = <?= $message_id; ?>;

    if (message === "") {
      alert("Please enter a message before submitting.");
      return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "submit_report.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
      if (xhr.responseText.trim() === "success") {
        showSuccessPopup();
      } else {
        alert("Error submitting report.\n\nResponse: " + xhr.responseText);
      }
    };
    xhr.send("message_id=" + encodeURIComponent(messageId) + "&reason=" + encodeURIComponent(reason + ": " + message));
  }

  function submitReport() {
    const reason = document.getElementById("hiddenReason").value;
    const messageId = <?= $message_id; ?>;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "submit_report.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
      if (xhr.responseText.trim() === "success") {
        showSuccessPopup();
      } else {
        alert("Error submitting report. Please try again.\n\nResponse: " + xhr.responseText);
      }
    };
    xhr.send("message_id=" + encodeURIComponent(messageId) + "&reason=" + encodeURIComponent(reason));
  }
  </script>
</body>
</html>
