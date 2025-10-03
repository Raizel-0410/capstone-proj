<?php
require 'auth_check.php';
require 'audit_log.php';
require 'db_connect.php';

$fullName = 'Unknown User';
$role = 'Unknown Role';

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

if (!empty($session['user_id'])) {
    $stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $session['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $fullName = htmlspecialchars($user['full_name'] ?? 'Unknown User', ENT_QUOTES, 'UTF-8');
        $role = htmlspecialchars($user['role'] ?? 'Unknown Role', ENT_QUOTES, 'UTF-8');
    } else {
        session_unset();
        session_destroy();
        header("Location: loginpage.php");
        exit;
    }
} else {
    session_unset();
    session_destroy();
    header("Location: loginpage.php");
    exit;
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
<link rel="stylesheet" href="./stylesheet/visitors.css">
<title>Visitors</title>
</head>
<body>

<div class="body">
  <!-- Sidebar -->
  <div class="left-panel">
    <div class="sidebar-panel">
      <h1 class="sidebar-header">iSecure</h1>
      <div class="nav-links">
        <ul>
          <h6>MENU</h6>
          <li><i class="fa-solid fa-gauge-high"></i><a href="maindashboard.php"> Main Dashboard</a></li>
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

  <!-- Main Panel -->
  <div class="right-panel">
    <div class="main-content">

      <!-- Header -->
      <div class="main-header">
        <div class="header-left">
          <i class="fa-solid fa-home"></i>
          <h6 class="path"> / Dashboard /</h6>
          <h6 class="current-loc">Visitors</h6>
        </div>
        <div class="header-right">
          <i class="fa-regular fa-bell me-3"></i>
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

      <!-- Confirm Modal -->
      <div id="confirmModal" class="modal">
        <div class="modal-content">
          <p id="confirmMessage"></p>
          <div class="modal-actions">
            <button id="confirmYes" class="btn btn-danger">Yes</button>
            <button id="confirmNo" class="btn btn-secondary">No</button>
          </div>
        </div>
      </div>

      <!-- Expected Visitors -->
      <div class="vehicles-container">
        <h5 class="table-title">Expected Visitors</h5>
        <div class="table-responsive">
          <table id="expectedVisitorsTable">
            <thead>
              <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Contact</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="6" class="text-center">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Inside Visitors -->
      <div class="vehicles-container">
        <h5 class="table-title">Inside Visitors</h5>
        <div class="table-responsive">
          <table id="insideVisitorsTable">
            <thead>
              <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Contact</th>
                <th>Key Card Number</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="8" class="text-center">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Exited Visitors -->
      <div class="vehicles-container">
        <h5 class="table-title">Exited Visitors</h5>
        <div class="table-responsive">
          <table id="exitedVisitorsTable">
            <thead>
              <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Contact</th>
                <th>Key Card Number</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Status</th>
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

<!-- Modals -->
<div class="modal fade" id="editTimeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Visitor Time</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editTimeForm">
          <input type="hidden" id="editVisitorId" name="visitor_id">
          <div class="mb-3">
            <label for="editTimeIn" class="form-label">Time In</label>
            <input type="datetime-local" id="editTimeIn" name="time_in" class="form-control">
          </div>
          <div class="mb-3">
            <label for="editTimeOut" class="form-label">Time Out</label>
            <input type="datetime-local" id="editTimeOut" name="time_out" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="visitorDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Visitor Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <ul class="nav nav-tabs" id="visitorTab" role="tablist" style="margin-bottom: 20px;">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">Details</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="verify-tab" data-bs-toggle="tab" data-bs-target="#verify" type="button" role="tab" aria-controls="verify" aria-selected="false">Verify</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="facial-tab" data-bs-toggle="tab" data-bs-target="#facial" type="button" role="tab" aria-controls="facial" aria-selected="false">Facial</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="vehicle-tab" data-bs-toggle="tab" data-bs-target="#vehicle" type="button" role="tab" aria-controls="vehicle" aria-selected="false">Vehicle</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="id-tab" data-bs-toggle="tab" data-bs-target="#id" type="button" role="tab" aria-controls="id" aria-selected="false">ID</button>
        </li>
      </ul>
      <div class="tab-content" id="visitorTabContent" style="margin-top: 10px;">
      <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
          <div class="visitor-info" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px 40px; align-items: start;">
            <p><strong>Full Name:</strong> <span id="visitorName"></span></p>
            <p><strong>Contact Number:</strong> <span id="visitorContact"></span></p>
            <p><strong>Email:</strong> <span id="visitorEmail"></span></p>
            <p><strong>Address:</strong> <span id="visitorAddress"></span></p>
            <p><strong>Reason for Visit:</strong> <span id="visitorReason"></span></p>
            <p><strong>Personnel Related:</strong> <span id="visitorPersonnel"></span></p>
            <div class="photos" style="display: flex; gap: 20px; margin-top: 20px;">
              <div>
                <strong>ID Photo:</strong><br>
                <img id="visitorIDPhoto" src="" alt="ID Photo" style="max-width:200px; max-height:200px;">
              </div>
              <div>
                <strong>Selfie:</strong><br>
                <img id="visitorSelfie" src="" alt="Selfie" style="max-width:200px; max-height:200px;">
              </div>
            </div>
            <div id="vehicleInfo" style="display:none; margin-top: 20px;">
              <h5>Vehicle Information</h5>
              <p><strong>Owner:</strong> <span id="vehicleOwner"></span></p>
              <p><strong>Brand:</strong> <span id="vehicleBrand"></span></p>
              <p><strong>Model:</strong> <span id="vehicleModel"></span></p>
              <p><strong>Color:</strong> <span id="vehicleColor"></span></p>
              <p><strong>Plate Number:</strong> <span id="plateNumber"></span></p>
              <div style="margin-top: 10px;">
                <strong>Vehicle Photo:</strong><br>
                <img id="vehiclePhoto" src="" alt="Vehicle Photo" style="max-width:200px; max-height:200px;">
              </div>
            </div>
            <div id="driverInfo" style="display:none; margin-top: 20px;">
              <h5>Driver Information</h5>
              <p><strong>Name:</strong> <span id="driverName"></span></p>
              <div style="margin-top: 10px;">
                <strong>ID Photo:</strong><br>
                <img id="driverIdPhoto" src="" alt="Driver ID" style="max-width:200px; max-height:200px;">
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="verify" role="tabpanel" aria-labelledby="verify-tab">
          <div>
            <button id="nextToFacial" class="btn btn-primary float-end">Next</button>
          </div>
        </div>
        <div class="tab-pane fade" id="facial" role="tabpanel" aria-labelledby="facial-tab">
          <div>
            <div id="facialRecognitionContainer" style="min-height: 200px; border: 1px solid #ccc; border-radius: 8px; margin-bottom: 15px;">
              <!-- Facial recognition feature under development -->
            </div>
            <button id="nextToVehicle" class="btn btn-primary float-end">Next</button>
          </div>
        </div>
        <div class="tab-pane fade" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">
          <div>
            <div id="vehicleRecognitionContainer" style="min-height: 200px; border: 1px solid #ccc; border-radius: 8px; margin-bottom: 15px;">
              <!-- Vehicle license plate recognition feature under development -->
            </div>
            <button id="skipVehicle" class="btn btn-secondary float-start">Skip</button>
            <button id="nextToId" class="btn btn-primary float-end">Next</button>
          </div>
        </div>
        <div class="tab-pane fade" id="id" role="tabpanel" aria-labelledby="id-tab">
          <div>
            <div id="idRecognitionContainer" style="min-height: 200px; border: 1px solid #ccc; border-radius: 8px; margin-bottom: 15px;">
              <!-- ID recognition feature under development -->
            </div>
            <button id="markEntryBtn" class="btn btn-success float-end">Mark Entry</button>
          </div>
        </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="./scripts/visitors_new.js"></script>
<script src="./scripts/session_check.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
