<?php
session_start();
require 'db_connect.php'; 
require 'audit_log.php'; 

function generateRandomToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

$token = $_SESSION['user_token'] ?? null;

if (!$token) {
    $token = generateRandomToken(64);
    $_SESSION['user_token'] = $token;

    $expiry = date("Y-m-d H:i:s", strtotime("+45 minutes"));
    $stmt = $pdo->prepare("INSERT INTO visitor_sessions (user_token, created_at, expires_at) VALUES (?, CURRENT_TIMESTAMP(), ?)");
    $stmt->execute([$token, $expiry]);
}

// Only log if $token is valid
if ($token) {
    log_landing_action($pdo, $token, "Visited landing page");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="icon" type="image/png" href=".\images\logo\5thFighterWing-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./stylesheet/visitationrequestform.css">
    <title>Visitation Request</title>
</head>
<body>
     

    <div id="top-bar" class="top-bar"></div>

    <!-- Header -->

  <div class="header">
  <div class="left-group">
    <div class="basa-logo"><img src=".\images\logo\5thFighterWing-logo.png" alt="BASA Logo"></div>
    <h1>5TH FIGHTER WING</h1>
  </div>

  <div class="right-group">
    <div class="transparency-logo"><img src=".\images\logo\Transparency Seal - img.png" alt="Transparency Logo"></div>
    <div class="phaf-logo"><img src=".\images\logo\Philippine Airforce - Logo.png" alt="PHAF Logo"></div>
    <div class="bagongpilipinas-logo"><img src=".\images\logo\Bagong Pilipinas - Logo.png" alt="Bagong Pilipinas Logo"></div>
  </div>
</div>

    <h1 id="ScheduleVisit" class="visith1">VISITATION REQUEST FORM</h1>

<form class="visitation-request-section" action="visitation_submit.php" method="POST" enctype="multipart/form-data">

  <!-- Visitor Info -->
  <h3>Visitor Information: </h3>
  <div class="visitor-information-section">
    <div class="visitor-info-column">
      <label>Visitor Name:
        <input type="text" name="visitor_name" placeholder="Enter full name" required>
      </label>
      <label>Home Address:
        <input type="text" name="home_address" placeholder="Enter home address" required>
      </label>
      <label>Valid ID:
        <input type="file" name="valid_id" accept="image/*" required>
      </label>
    </div>

    <div class="visitor-info-column">
      <label>Contact Number:
        <input type="text" name="contact_number" placeholder="e.g. 09xxxxxxxxx" required>
      </label>
      <label>Email Address:
        <input type="email" name="email" placeholder="example@email.com" required>
      </label>
      <label>Selfie Photo:
        <input type="file" name="selfie_photo" accept="image/*" required>
      </label>
    </div>
  </div>

  <!-- Vehicle Info -->
  <h3>Vehicle Information: </h3>
  <div class="vehicle-information-section">
    <div class="vehicle-info-column">
      <label>Vehicle Owner:
        <input type="text" name="vehicle_owner" placeholder="Owner name">
      </label>
      <label>Vehicle Brand:
        <input type="text" name="vehicle_brand" placeholder="e.g. Toyota, Honda">
      </label>
      <label>Plate Number:
        <input type="text" name="plate_number" placeholder="ABC-1234">
      </label>
    </div>

    <div class="vehicle-info-column">
      <label>Vehicle Color:
        <input type="text" name="vehicle_color" placeholder="e.g. Red, Black">
      </label>
      <label>Vehicle Model:
        <input type="text" name="vehicle_model" placeholder="e.g. Vios, Civic">
      </label>
      <label>Vehicle Photo:
        <input type="file" name="vehicle_photo" accept="image/*">
      </label>
    </div>
  </div>

  <!-- Schedule -->
  <h3>Schedule Request: </h3>
  <div class="schedule-request-section">
    <div class="schedule-req-div">
      <label>Reason for Visitation:
        <input type="text" name="reason" placeholder="Enter reason" required>
      </label>
      <label>Personnel Related to:
        <input type="text" name="personnel_related" placeholder="Who will be visited">
      </label>
    </div>

    <label id="datetime">Date & Time:
      <input type="date" name="visit_date" required>
      <input type="time" name="visit_time" required>
    </label>

    <button type="submit" class="submit">Submit</button>
  </div>

</form>


  <div class="footer-columns">
    <div class="col">
      <div class="col-img">
         <img src=".\images\logo\5thFighterWing-logo.png" alt="Basa Logo">
         <img src=".\images\logo\Bagong Pilipinas - Logo.png" alt="Bagong Pilipinas Logo">
         <img src=".\images\logo\Philippine Airforce - Logo.png" alt="Philippine Air Force Logo">
      </div>
      <br>
      <h5>Copyright © Basa Air Base 5th Fighter Wing. All Rights Reserved</h5>
    </div>
    <div class="col">
      <h4 class="middle-panel-text">Follow our Socials:</h4>
      <i class="fa-brands fa-facebook"></i>
      <i class="fa-brands fa-instagram"></i>
      <i class="fa-brands fa-twitter"></i>
      <i class="fa-brands fa-youtube"></i>
    </div>

    <div id="last-col" class="col">
      <h4 class="right-panel-text">Developed By:</h4>

      <div id="left-col-img" class="col-img">
        <img src=".\images\logo\pamsu - logo.png" alt="Pampanga State Univertisty Logo">
        <img src=".\images\logo\ccs - log.png" alt="College of Computing Studies">
        <h4 class="right-panel-text2">CCS Students of Pampanga State University</h4>
      </div>
     
    </div>
  </div>
     <script src="./scripts/landingpage.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
     </body>
</html>