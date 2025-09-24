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
          <li><i class="fa-solid fa-video"></i><a href="cameraview.php"> Camera View</a></li>
          <li class="camera-view-drop-down"><i class="fa-solid fa-circle-dot"></i><a href="livefeed.php"> Live Feed</a></li>
          <li class="camera-view-drop-down"><i class="fa-solid fa-id-card-clip"></i><a href="personinformation.php"> Person Information</a></li>
          <li><i class="fa-solid fa-user"></i><a href="visitors.php"> Visitors</a></li>
          <li><i class="fa-solid fa-car-side"></i><a href="vehicles.php"> Vehicles</a></li>
          <li><i class="fa-solid fa-user-gear"></i><a href="personnels.php"> Personnels</a></li>
          <li><i class="fa-solid fa-clock-rotate-left"></i><a href="pendings.php"> Pendings</a></li>
          <h6>DATA MANAGEMENT</h6>
          <li><i class="fa-solid fa-image-portrait"></i><a href="personnelaccounts.php"> Personnel Accounts</a></li>
          <li><i class="fa-solid fa-box-archive"></i><a href="inventory.php"> Inventory</a></li>
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
            </div>
          </div>
        </div>
      </div>

      <!-- Visitors Table -->
      <div class="card mt-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Visitors</h5>
        </div>
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
              <tbody>
              <?php
              $stmt = $pdo->query("SELECT v.id, vr.visitor_name, vr.contact_number, vr.email, vr.home_address, vr.valid_id_path, vr.selfie_photo_path, v.date, v.time_in, v.time_out, v.status
                                   FROM visitors v
                                   LEFT JOIN visitation_requests vr ON v.id = vr.id
                                   ORDER BY v.date DESC");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr>
                          <td>{$row['visitor_name']}</td>
                          <td>{$row['contact_number']}</td>
                          <td>{$row['date']}</td>
                          <td>{$row['time_in']}</td>
                          <td>{$row['time_out']}</td>
                          <td>{$row['status']}</td>
                          <td>
                            <button class='btn btn-sm btn-warning edit-btn' data-id='{$row['id']}'>Edit</button>
                            <button class='btn btn-sm btn-info view-btn' data-id='{$row['id']}'>View</button>";
                  if ($row['status'] === 'Inside') {
                      echo "<button class='btn btn-sm btn-danger exit-btn' data-id='{$row['id']}'>Mark Exit</button>";
                  }
                  echo "</td></tr>";
              }
              ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Visitor View Modal -->
<div class="modal fade" id="visitorDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Visitor Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="visitorTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">Info</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="live-tab" data-bs-toggle="tab" data-bs-target="#live" type="button">Live Video</button>
          </li>
        </ul>
        <div class="tab-content mt-3">
          <div class="tab-pane fade show active" id="info">
            <p><strong>Full Name:</strong> <span id="visitorName"></span></p>
            <p><strong>Contact:</strong> <span id="visitorContact"></span></p>
            <p><strong>Email:</strong> <span id="visitorEmail"></span></p>
            <p><strong>Address:</strong> <span id="visitorAddress"></span></p>
            <p><strong>Time In:</strong> <span id="visitorTimeIn"></span></p>
            <p><strong>Time Out:</strong> <span id="visitorTimeOut"></span></p>
            <div class="row mt-3">
              <div class="col-md-6 text-center">
                <h6>ID Photo</h6>
                <img id="visitorIDPhoto" src="" alt="ID Photo" class="img-fluid rounded shadow">
              </div>
              <div class="col-md-6 text-center">
                <h6>Selfie Photo</h6>
                <img id="visitorSelfie" src="" alt="Selfie Photo" class="img-fluid rounded shadow">
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="live">
            <p class="text-center text-muted">Live video feed will appear here.</p>
            <div id="liveVideoContainer" class="text-center"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Visitor Edit Modal -->
<div class="modal fade" id="visitorEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Edit Visitor Exit Time</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editVisitorForm">
          <input type="hidden" id="editVisitorId" name="visitor_id">
          <div class="mb-3">
            <label for="editTimeOut" class="form-label">Time Out</label>
            <input type="time" id="editTimeOut" name="time_out" class="form-control" required>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Visitor Time Modal -->
<div class="modal fade" id="editTimeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Edit Visitor Exit Time</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editTimeForm">
          <input type="hidden" id="editVisitorId">
          <div class="mb-3">
            <label for="editTimeOut" class="form-label">New Exit Time</label>
            <input type="time" id="editTimeOut" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-warning w-100">Update Time</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Visitor Details Modal with Tabs -->
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
            <button class="nav-link" id="live-tab" data-bs-toggle="tab" data-bs-target="#liveVideo" type="button" role="tab">Live Video</button>
          </li>
        </ul>

        <div class="tab-content mt-3">
          <!-- Visitor Details -->
          <div class="tab-pane fade show active" id="details" role="tabpanel">
            <p><strong>Full Name:</strong> <span id="visitorName"></span></p>
            <p><strong>Contact:</strong> <span id="visitorContact"></span></p>
            <p><strong>Email:</strong> <span id="visitorEmail"></span></p>
            <p><strong>Address:</strong> <span id="visitorAddress"></span></p>
            <p><strong>Reason:</strong> <span id="visitorReason"></span></p>
            <div class="row mt-3">
              <div class="col-md-6 text-center">
                <h6>ID Photo</h6>
                <img id="visitorIDPhoto" src="" alt="ID Photo" class="img-fluid rounded shadow">
              </div>
              <div class="col-md-6 text-center">
                <h6>Selfie Photo</h6>
                <img id="visitorSelfie" src="" alt="Selfie Photo" class="img-fluid rounded shadow">
              </div>
            </div>
          </div>

          <!-- Live Video Tab -->
          <div class="tab-pane fade" id="liveVideo" role="tabpanel">
            <div class="text-center">
              <h6>Live Camera Feed</h6>
              <video id="visitorLiveVideo" autoplay playsinline muted class="rounded shadow" style="width: 100%; max-width: 600px;"></video>
              <p class="text-muted mt-2">Facial recognition will identify the visitor in real-time.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<script src="./scripts/visitors.js"></script>
<script src="./scripts/session_check.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
