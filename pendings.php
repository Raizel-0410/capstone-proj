<?php
require 'auth_check.php';
require 'audit_log.php';
require 'db_connect.php';

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

// --- Fetch All Requests (not just pending) ---
$stmt = $pdo->prepare("SELECT * FROM visitation_requests ORDER BY created_at DESC");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" href="./stylesheet/pendings.css">
  <title>Pendings</title>
  <style>.modal img { max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 8px; }</style>
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
        <li class="camera-view-drop-down"><i class="fa-solid fa-circle-dot"></i><a href="livefeed.php"> Live Feed</a></li>
        <li class="camera-view-drop-down"><i class="fa-solid fa-id-card-clip"></i><a href="personinformation.php"> Person Information</a></li>
        <li><i class="fa-solid fa-user"></i><a href="visitors.php"> Visitors</a></li>
        <li><i class="fa-solid fa-car-side"></i><a href="vehicles.php"> Vehicles</a></li>
        <li><i class="fa-solid fa-user-gear"></i><a href="personnels.php"> Personnels</a></li>
        <li><i class="fa-solid fa-clock-rotate-left"></i><a href="pendings.php"> Pendings</a></li>
        <h6>CUSTOMIZATION</h6>
        <li><i class="fa-solid fa-newspaper"></i><a href="customizelanding.php"> Landing Page</a></li>
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
      <h6 class="current-loc">Pendings</h6>
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

 <div class="container mt-4">
  <h3><i class="fa-solid fa-clock-rotate-left"></i> Visitation Requests</h3>

  <ul class="nav nav-tabs mt-3" id="requestTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#pendingTab">Pending</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#approvedTab">Approved</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#rejectedTab">Rejected</a></li>
  </ul>

  <div class="tab-content mt-3">
    <!-- Pending -->
    <div class="tab-pane fade show active" id="pendingTab">
      <table class="table table-bordered">
        <thead class="table-light"><tr><th>Visitor</th><th>Date</th><th>Time</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="pendingTable">
        <?php foreach ($requests as $req): ?>
          <?php if ($req['status'] === 'Pending'): ?>
            <tr data-id="<?= $req['id'] ?>">
              <td><?= htmlspecialchars($req['visitor_name']) ?></td>
              <td><?= htmlspecialchars($req['visit_date']) ?></td>
              <td><?= htmlspecialchars($req['visit_time']) ?></td>
              <td><span class="badge bg-warning text-dark"><?= $req['status'] ?></span></td>
              <td>
                <button class="btn btn-primary btn-sm view-btn"
                  data-id="<?= $req['id'] ?>"
                  data-name="<?= htmlspecialchars($req['visitor_name']) ?>"
                  data-home="<?= htmlspecialchars($req['home_address']) ?>"
                  data-contact="<?= htmlspecialchars($req['contact_number']) ?>"
                  data-email="<?= htmlspecialchars($req['email']) ?>"
                  data-date="<?= $req['visit_date'] ?>"
                  data-time="<?= $req['visit_time'] ?>"
                  data-reason="<?= htmlspecialchars($req['reason']) ?>"
                  data-personnel="<?= htmlspecialchars($req['personnel_related']) ?>"
                  data-vehicle="<?= htmlspecialchars($req['vehicle_brand'].' '.$req['vehicle_model'].' '.$req['plate_number'].' '.$req['vehicle_color']) ?>"
                  data-validid="<?= htmlspecialchars($req['valid_id_path']) ?>"
                  data-selfie="<?= htmlspecialchars($req['selfie_photo_path']) ?>"
                  data-vehiclephoto="<?= htmlspecialchars($req['vehicle_photo_path']) ?>"
                >View</button>
              </td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Approved -->
    <div class="tab-pane fade" id="approvedTab">
      <table class="table table-bordered">
        <thead class="table-success"><tr><th>Visitor</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
        <tbody id="approvedTable">
        <?php foreach ($requests as $req): ?>
          <?php if ($req['status'] === 'Approved'): ?>
            <tr data-id="<?= $req['id'] ?>">
              <td><?= htmlspecialchars($req['visitor_name']) ?></td>
              <td><?= htmlspecialchars($req['visit_date']) ?></td>
              <td><?= htmlspecialchars($req['visit_time']) ?></td>
              <td><span class="badge bg-success"><?= $req['status'] ?></span></td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Rejected -->
    <div class="tab-pane fade" id="rejectedTab">
      <table class="table table-bordered">
        <thead class="table-danger"><tr><th>Visitor</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
        <tbody id="rejectedTable">
        <?php foreach ($requests as $req): ?>
          <?php if ($req['status'] === 'Rejected'): ?>
            <tr data-id="<?= $req['id'] ?>">
              <td><?= htmlspecialchars($req['visitor_name']) ?></td>
              <td><?= htmlspecialchars($req['visit_date']) ?></td>
              <td><?= htmlspecialchars($req['visit_time']) ?></td>
              <td><span class="badge bg-danger"><?= $req['status'] ?></span></td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

  <!-- Request Modal -->
  <div class="modal fade" id="requestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Visitation Request Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Name:</strong> <span id="modalName"></span></p>
          <p><strong>Home Address:</strong> <span id="modalHome"></span></p>
          <p><strong>Contact:</strong> <span id="modalContact"></span></p>
          <p><strong>Email:</strong> <span id="modalEmail"></span></p>
          <p><strong>Date:</strong> <span id="modalDate"></span></p>
          <p><strong>Time:</strong> <span id="modalTime"></span></p>
          <p><strong>Reason:</strong> <span id="modalReason"></span></p>
          <p><strong>Personnel Related:</strong> <span id="modalPersonnel"></span></p>
          <p><strong>Vehicle Info:</strong> <span id="modalVehicle"></span></p>
          <p><strong>Valid ID:</strong><br><img id="modalValidId" src="" alt="Valid ID"></p>
          <p><strong>Selfie Photo:</strong><br><img id="modalSelfie" src="" alt="Selfie"></p>
          <p><strong>Vehicle Photo:</strong><br><img id="modalVehiclePhoto" src="" alt="Vehicle"></p>
        </div>
        <div class="modal-footer">
          <input type="hidden" id="modalRequestId">
          <button id="approveBtn" class="btn btn-success">Approve</button>
          <button id="rejectBtn" class="btn btn-danger">Reject</button>
        </div>
      </div>
    </div>
  </div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
const requestModal = new bootstrap.Modal(document.getElementById("requestModal"));

document.querySelectorAll(".view-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    document.getElementById("modalName").textContent = btn.dataset.name;
    document.getElementById("modalHome").textContent = btn.dataset.home;
    document.getElementById("modalContact").textContent = btn.dataset.contact;
    document.getElementById("modalEmail").textContent = btn.dataset.email;
    document.getElementById("modalDate").textContent = btn.dataset.date;
    document.getElementById("modalTime").textContent = btn.dataset.time;
    document.getElementById("modalReason").textContent = btn.dataset.reason;
    document.getElementById("modalPersonnel").textContent = btn.dataset.personnel;
    document.getElementById("modalVehicle").textContent = btn.dataset.vehicle;
    document.getElementById("modalValidId").src = btn.dataset.validid || "placeholder.png";
    document.getElementById("modalSelfie").src = btn.dataset.selfie || "placeholder.png";
    document.getElementById("modalVehiclePhoto").src = btn.dataset.vehiclephoto || "placeholder.png";
    document.getElementById("modalRequestId").value = btn.dataset.id;
    requestModal.show();
  });
});

// async function handleAction(id, action) {
//   try {
//     const res = await fetch("approve_visitation_request.php", {
//       method: "POST",
//       headers: {"Content-Type": "application/x-www-form-urlencoded"},
//       body: new URLSearchParams({ id: id, action: action })
//     });
//     const data = await res.json();
//     if (data.success) {
//       // Move row to correct tab
//       const row = document.querySelector(`tr[data-id='${id}']`);
//       if (row) {
//         row.remove();
//         row.querySelector("td:nth-child(4)").innerHTML = 
//           `<span class="badge ${action==='approve'?'bg-success':'bg-danger'}">${action==='approve'?'Approved':'Rejected'}</span>`;
//         document.getElementById(action==='approve'?'approvedTable':'rejectedTable').appendChild(row);
//       }
//       requestModal.hide();
//     } else {
//       alert("Error: " + data.message);
//     }
//   } catch (err) {
//     console.error(err);
//     alert("Something went wrong.");
//   }
// }

document.getElementById("approveBtn").addEventListener("click", () => {
  handleDecision("approve");
});
document.getElementById("rejectBtn").addEventListener("click", () => {
  handleDecision("reject");
});

function handleDecision(action) {
  const id = document.getElementById("modalRequestId").value;

  fetch("approve_visitation_request.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${encodeURIComponent(id)}&action=${action}`
  })
  .then(res => res.json())
  .then(data => {
  if (data.success) {
    const row = document.querySelector(`.view-btn[data-id="${id}"]`).closest("tr");
    if (row) {
      // Update status badge
      let badgeClass = "bg-warning text-dark";
      if (data.status === "Approved") badgeClass = "bg-success";
      if (data.status === "Rejected") badgeClass = "bg-danger";
      row.querySelector("td:nth-child(4)").innerHTML =
        `<span class="badge ${badgeClass}">${data.status}</span>`;

      // Move row to correct tab
      if (data.status === "Approved") {
        document.querySelector("#approvedTab tbody").appendChild(row);
      } else if (data.status === "Rejected") {
        document.querySelector("#rejectedTab tbody").appendChild(row);
      }
    }
    requestModal.hide();

    // 🔹 NEW: refresh vehicles tables if available
    if (typeof window.loadExpectedVehicles === "function") {
      window.loadExpectedVehicles();
    }
    if (typeof window.loadInsideVehicles === "function") {
      window.loadInsideVehicles();
    }

  } else {
    alert("Error: " + data.error);
  }
})

  .catch(err => alert("Request failed: " + err));
}

</script>
<script src="./scripts/session_check.js"></script>
</body>
</html>
