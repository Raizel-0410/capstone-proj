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

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
      <!-- Visitors Table -->
      <div class="card mt-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="visitorsTable">
              <thead class="table-light">
                <tr>
                  <th>Full Name</th>
                  <th>Contact</th>
                  <th>Date</th>
                  <th>Time In</th>
                  <th>Time Out</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
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

<!-- ==== Expected Visitors Table ==== -->
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
        <tr>
          <td colspan="6" class="text-center">Loading...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ==== Inside Visitors Table ==== -->
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

<!-- ==== Exited Visitors Table ==== -->
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
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes

    </div>
  </div>
</div>

<!-- Edit Time Out Modal -->
<div class="modal fade" id="editTimeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Edit Time Out</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editTimeForm">
          <input type="hidden" id="editVisitorId">
          <div class="mb-3">
            <label for="editTimeOut" class="form-label">Time Out</label>
            <input type="time" class="form-control" id="editTimeOut" required>
          </div>
          <div class="mb-3">
            <label for="editValidityStart" class="form-label">Validity Start</label>
            <input type="datetime-local" class="form-control" id="editValidityStart" required>
          </div>
          <div class="mb-3">
            <label for="editValidityEnd" class="form-label">Validity End</label>
            <input type="datetime-local" class="form-control" id="editValidityEnd" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="saveTimeBtn" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<!-- Visitor Details Modal -->
<div class="modal fade" id="visitorDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Visitor Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="visitorTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Details</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="verify-tab" data-bs-toggle="tab" data-bs-target="#verify" type="button" role="tab">Verify</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="facial-tab" data-bs-toggle="tab" data-bs-target="#facial" type="button" role="tab">Facial Verification</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="vehicle-tab" data-bs-toggle="tab" data-bs-target="#vehicle" type="button" role="tab">Vehicle Verification</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="id-tab" data-bs-toggle="tab" data-bs-target="#id" type="button" role="tab">ID Verification</button>
          </li>
        </ul>

        <div class="tab-content mt-3">
          <!-- Visitor Details -->
          <div class="tab-pane fade show active" id="details" role="tabpanel">
            <div class="details-container">
              <div class="details-text">
                <p><strong>Full Name:</strong> <span id="visitorName"></span></p>
                <p><strong>Contact:</strong> <span id="visitorContact"></span></p>
                <p><strong>Email:</strong> <span id="visitorEmail"></span></p>
                <p><strong>Address:</strong> <span id="visitorAddress"></span></p>
                <p><strong>Reason:</strong> <span id="visitorReason"></span></p>
                <p><strong>Personnel to Visit:</strong> <span id="visitorPersonnel"></span></p>
                <p><strong>Personnel Office:</strong> <span id="visitorPersonnelOffice"></span></p>
                <p><strong>Office to Visit:</strong> <span id="visitorOfficeToVisit"></span></p>
                <div id="vehicleInfo" style="display: none;">
                  <h6>Vehicle Information:</h6>
                  <p><strong>Vehicle Owner:</strong> <span id="vehicleOwner"></span></p>
                  <p><strong>Vehicle Brand:</strong> <span id="vehicleBrand"></span></p>
                  <p><strong>Vehicle Model:</strong> <span id="vehicleModel"></span></p>
                  <p><strong>Vehicle Color:</strong> <span id="vehicleColor"></span></p>
                  <p><strong>Plate Number:</strong> <span id="plateNumber"></span></p>
                </div>
                <div id="driverInfo" style="display: none;">
                  <h6>Driver Information:</h6>
                  <p><strong>Driver Name:</strong> <span id="driverName"></span></p>
                </div>
              </div>
              <div class="details-images">
                <div class="image-container">
                  <h6>ID Photo</h6>
                  <img id="visitorIDPhoto" src="" alt="ID Photo" class="img-fluid rounded shadow">
                </div>
                <div class="image-container">
                  <h6>Selfie Photo</h6>
                  <img id="visitorSelfie" src="" alt="Selfie Photo" class="img-fluid rounded shadow">
                </div>
                <div class="image-container" id="vehiclePhotoContainer" style="display:none;">
                  <h6>Vehicle Photo</h6>
                  <img id="vehiclePhoto" src="" alt="Vehicle Photo" class="img-fluid rounded shadow" style="max-width: 200px;">
                </div>
                <div class="image-container" id="driverIdPhotoContainer" style="display:none;">
                  <h6>Driver ID Photo</h6>
                  <img id="driverIdPhoto" src="" alt="Driver ID Photo" class="img-fluid rounded shadow" style="max-width: 200px;">
                </div>
              </div>
            </div>
            <button id="nextToVerify" class="btn btn-primary mt-3">Next</button>
          </div>

          <!-- Verify Tab -->
          <div class="tab-pane fade" id="verify" role="tabpanel">
            <h6>Verification Checklist</h6>
            <ul>
              <li>Facial Verification</li>
              <li>Vehicle Verification</li>
              <li>ID Verification</li>
            </ul>
            <button id="nextToFacial" class="btn btn-primary mt-3">Next</button>
          </div>

          <!-- Facial Verification Tab -->
          <div class="tab-pane fade" id="facial" role="tabpanel">
            <h6>Facial Verification</h6>
            <div id="facialContainer">Container for facial recognition feature</div>
            <button id="nextToVehicle" class="btn btn-primary mt-3">Next</button>
          </div>

          <!-- Vehicle Verification Tab -->
          <div class="tab-pane fade" id="vehicle" role="tabpanel">
            <h6>Vehicle Verification</h6>
            <div id="vehicleContainer">Container for vehicle verification feature</div>
            <button id="nextToId" class="btn btn-primary mt-3">Next</button>
            <button id="skipVehicle" class="btn btn-secondary mt-3 ms-2">Skip</button>
          </div>

          <!-- ID Verification Tab -->
          <div class="tab-pane fade" id="id" role="tabpanel">
            <h6>ID Verification</h6>
            <div id="idContainer">Container for ID verification feature</div>
            <button id="markEntryBtn" class="btn btn-success mt-3">Mark Entry</button>
            <button id="rejectBtn" class="btn btn-danger mt-3">Reject</button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="./scripts/visitors_new.js"></script>
<script src="./scripts/session_check.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
