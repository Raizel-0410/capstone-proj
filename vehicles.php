<?php
require 'auth_check.php';

// Default fallbacks
$fullName = 'Unknown User';
$role = 'Unknown Role';

// Check if session token exists
if (!isset($_SESSION['token'])) {
    header("Location: loginpage.php");
    exit;
}

// Validate token in DB
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="icon" type="image/png" href="./images/logo/5thFighterWing-logo.png">
  <link rel="stylesheet" href="./stylesheet/vehicles.css">
  <title>Vehicles</title>
</head>
<body>
<div class="body">
  <div class="left-panel">
    <div class="sidebar-panel">
      <h1 class="sidebar-header">iSecure</h1>
      <div class="nav-links">
        <ul>
          <h6>MENU</h6>
          <li><i class="fa-solid fa-gauge-high"></i><a href="maindashboard.php"> Main Dashboard</a></li>
          <li><i class="fa-solid fa-video"></i><a href="cameraview.php"> Camera View</a></li>
          <li><i class="fa-solid fa-circle-dot"></i><a href="livefeed.php"> Live Feed</a></li>
          <li><i class="fa-solid fa-id-card-clip"></i><a href="personinformation.php"> Person Information</a></li>
          <li><i class="fa-solid fa-user"></i><a href="visitors.php"> Visitors</a></li>
          <li><i class="fa-solid fa-car-side"></i><a href="vehicles.php"> Vehicles</a></li>
          <li><i class="fa-solid fa-user-gear"></i><a href="personnels.php"> Personnels</a></li>
          <li><i class="fa-solid fa-clock-rotate-left"></i><a href="pendings.php"> Pendings</a></li>
          <h6>DATA MANAGEMENT</h6>
          <li><i class="fa-solid fa-image-portrait"></i><a href="personnelaccounts.php"> Personnel Accounts</a></li>
          <li><i class="fa-solid fa-id-badge"></i><a href="key_cards.php"> Key Cards</a></li>
          <li><i class="fa-solid fa-list"></i><a href="key_card_list.php"> Key Cards List</a></li>
          <h6>CUSTOMIZATION</h6>
          <li><i class="fa-solid fa-newspaper"></i><a href="customizelanding.php"> Landing Page</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="right-panel">
    <div class="main-content">
      <div class="main-header d-flex justify-content-between align-items-center">
        <div class="header-left d-flex align-items-center">
          <i class="fa-solid fa-home me-2"></i>
          <h6 class="path mb-0"> / Dashboard /</h6>
          <h6 class="current-loc mb-0 ms-1">Vehicles</h6>
        </div>
        <div class="header-right d-flex align-items-center">
          <i class="fa-regular fa-bell me-3"></i>
          <i class="fa-regular fa-message me-3"></i>
          <div class="user-info d-flex align-items-center">
            <i class="fa-solid fa-user-circle fa-lg me-2"></i>
            <div class="user-text">
              <span class="username"><?php echo $fullName; ?></span>
              <a id="logout-link" class="logout-link" href="logout.php">Logout</a>
              <div id="confirmModal" class="modal">
                <div class="modal-content">
                  <p id="confirmMessage"></p>
                  <div class="modal-actions">
                    <button id="confirmYes" class="btn btn-danger">Yes</button>
                    <button id="confirmNo" class="btn btn-secondary">No</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

<!-- ==== Expected Vehicles Table ==== -->
<<<<<<< Updated upstream
<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">Expected Vehicles</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="expectedVehiclesTable">
        <thead class="table-light">
          <tr>
            <th>Owner</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Color</th>
            <th>Plate No.</th>
            <th>Status</th> <!-- ✅ Updated header -->
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="7" class="text-center">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
=======
<div class="vehicles-container">
  <h5 class="table-title">Expected Vehicles</h5>
  <div class="table-responsive">
    <table id="expectedVehiclesTable">
      <thead>
        <tr>
          <th>Owner</th>
          <th>Brand</th>
          <th>Model</th>
          <th>Color</th>
          <th>Plate No.</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="6" class="text-center">Loading...</td>
        </tr>
      </tbody>
    </table>
>>>>>>> Stashed changes
  </div>
</div>



<!-- ==== Inside Vehicles Table ==== -->
<div class="card mb-4">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0">Inside Vehicles</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="insideVehiclesTable">
        <thead class="table-light">
          <tr>
            <th>Owner</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Color</th>
            <th>Plate No.</th>
            <th>Entry Time</th>
            <th>Exit Time</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="8" class="text-center">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>


    </div>
  </div>
</div>

<script src="./scripts/vehicles.js"></script>
<script src="./scripts/session_check.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
