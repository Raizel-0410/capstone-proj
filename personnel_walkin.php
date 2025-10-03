<?php
require 'auth_check.php';
require 'audit_log.php';

// Default fallbacks
$fullName = 'Unknown User';
$role = 'Unknown Role';

// Check session
if (!isset($_SESSION['token'])) {
    header("Location: loginpage.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM personnel_sessions WHERE token = :token AND expires_at > NOW()");
$stmt->execute([':token' => $_SESSION['token']]);
$session = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    session_unset();
    session_destroy();
    header("Location: loginpage.php");
    exit;
}

// Fetch user info
if (!empty($session['user_id'])) {
    $stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $session['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $fullName = htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8');
        $role = htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8');
    }
}

// Check if role is Personnel (assuming 'User' is personnel)
if ($role !== 'User') {
    echo "<script>alert('Access denied. Personnel only.'); window.location.href='loginpage.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Personnel Walk-in Visit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="./stylesheet/personnel_dashboard.css">
</head>
<body>

<div class="body">
  <div class="left-panel">
    <div class="sidebar-panel">
      <h1 class="sidebar-header">iSecure</h1>
      <div class="nav-links">
        <ul>
          <h6>MENU</h6>
          <li><i class="fa-solid fa-gauge-high"></i><a href="personnel_dashboard.php"> Dashboard</a></li>
          <li><i class="fa-solid fa-user-plus"></i><a href="personnel_walkin.php"> Walk-in Visit</a></li>
          <h6>NOTIFICATIONS</h6>
          <li><i class="fa-solid fa-bell"></i><a href="personnel_notifications.php"> View Notifications</a></li>
          <h6>DATA MANAGEMENT</h6>
          <li><i class="fa-solid fa-users"></i><a href="personnel_visitors.php"> Visitors Inside</a></li>
          <li><i class="fa-solid fa-id-badge"></i><a href="personnel_key_cards.php"> Key Cards</a></li>
          <li><i class="fa-solid fa-list"></i><a href="personnel_key_card_list.php"> Key Cards List</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="right-panel">
    <div class="main-content">
      <div class="main-header">
        <div class="header-left">
          <i class="fa-solid fa-home"></i>
          <h6 class="path"> / Dashboard /</h6>
          <h6 class="current-loc">Walk-in Visit</h6>
        </div>
        <div class="header-right">
          <div class="notification-dropdown">
            <i class="fa-regular fa-bell me-3" id="notification-bell"></i>
            <div class="notification-menu" id="notification-menu">
              <div class="notification-header">Notifications</div>
              <div id="notification-list">
                <!-- Notifications will be loaded here -->
              </div>
            </div>
          </div>
          <i class="fa-regular fa-message me-3"></i>
          <div class="user-info">
            <i class="fa-solid fa-user-circle fa-lg me-2"></i>
            <div class="user-text">
              <span class="username"><?php echo $fullName; ?></span>
              <a id="logout-link" class="logout-link" href="logout.php">Logout</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Walk-in Visit Form -->
      <div class="walkin-form-section">
        <h4>Walk-in Visit Request</h4>
        <form class="visitation-request-section" action="walkin_submit.php" method="POST" enctype="multipart/form-data">
          <div class="container">
            <div class="row">
              <div class="col-md-6">
                <!-- Visitor Info -->
                <h3>Visitor Information: </h3>
                <div class="visitor-information-section">
                  <div class="visitor-info-column">
                    <label>First Name:
                      <input type="text" name="first_name" placeholder="Enter first name" required>
                    </label>
                    <label>Last Name:
                      <input type="text" name="last_name" placeholder="Enter last name" required>
                    </label>
                    <label>Home Address:
                      <input type="text" name="home_address" placeholder="Enter home address" required>
                    </label>
                    <label>Valid ID:</label>
                    <div class="file-input-wrapper" onclick="document.getElementById('valid_id').click();">
                      Choose File
                    </div>
                    <input type="file" id="valid_id" name="valid_id" accept="image/*" required style="position:absolute; left:-9999px;">
                  </div>

                  <div class="visitor-info-column">
                    <label>Contact Number:
                      <input type="text" name="contact_number" placeholder="e.g. 09xxxxxxxxx" required>
                    </label>
                    <label>Email Address:
                      <input type="email" name="email" placeholder="example@email.com" required>
                    </label>
                    <label>Selfie Photo:</label>
                    <div class="file-input-wrapper" onclick="document.getElementById('selfie_photo').click();">
                      Choose File
                    </div>
                    <input type="file" id="selfie_photo" name="selfie_photo" accept="image/*" required style="position:absolute; left:-9999px;">
                  </div>
                </div>

                <!-- Visit Details -->
                <h3>Visit Details: </h3>
                <div class="schedule-request-section">
                  <div class="schedule-req-div">
                    <label>Visitation:
                      <input type="text" name="reason" placeholder="Enter reason" required>
                    </label>
                    <label>Personnel to Visit:
                      <input type="text" name="personnel_related" placeholder="Personnel Related To" required>
                    </label>
                  </div>

                  <label id="datetime">Date:
                    <input type="date" name="visit_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                  </label>

                  <button type="submit" class="submit">Submit Walk-in Request</button>
                </div>
              </div>

              <div class="col-md-6">
                <!-- Vehicle Info -->
                <h3>Vehicle Information: </h3>
                <div class="vehicle-information-section">
                  <div class="vehicle-info-column">
                    <label>Vehicle Owner:
                      <input type="text" name="vehicle_owner" placeholder="Owner name" readonly>
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
                    <label>Vehicle Type:
                      <input type="text" name="vehicle_type" placeholder="e.g. SUV, Sedan">
                    </label>
                    <label>Vehicle Photo:</label>
                    <div class="file-input-wrapper" onclick="document.getElementById('vehicle_photo').click();">
                      Choose File
                    </div>
                    <input type="file" id="vehicle_photo" name="vehicle_photo" accept="image/*" style="position:absolute; left:-9999px;">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<script src="./scripts/personnel_dashboard.js"></script>
<script src="./scripts/session_check.js"></script>
<script>
document.querySelector('input[name="first_name"]').addEventListener('input', updateOwner);
document.querySelector('input[name="last_name"]').addEventListener('input', updateOwner);

function updateOwner() {
  const first = document.querySelector('input[name="first_name"]').value;
  const last = document.querySelector('input[name="last_name"]').value;
  document.querySelector('input[name="vehicle_owner"]').value = first + ' ' + last;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
