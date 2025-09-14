<?php
session_start();
require 'db_connect.php';

// Default fallbacks so template never sees an undefined variable
$fullName = 'Unknown User';
$role = 'Unknown Role';

// Check if session token exists
if (!isset($_SESSION['token'])) {
    header("Location: loginpage.php");
    exit;
}

// Validate token in DB and get session row
$stmt = $pdo->prepare("SELECT * FROM personnel_sessions WHERE token = :token AND expires_at > NOW()");
$stmt->execute([':token' => $_SESSION['token']]);
$session = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    // Session expired or invalid
    session_unset();
    session_destroy();
    header("Location: loginpage.php");
    exit;
}

// Fetch user info using the user_id from the personnel_sessions row
if (!empty($session['user_id'])) {
    $stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $session['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // always sanitize output
        $fullName = htmlspecialchars($user['full_name'] ?? 'Unknown User', ENT_QUOTES, 'UTF-8');
        $role = htmlspecialchars($user['role'] ?? 'Unknown Role', ENT_QUOTES, 'UTF-8');
    } else {
        // user record missing — log out to be safe
        session_unset();
        session_destroy();
        header("Location: loginpage.php");
        exit;
    }
} else {
    // weird session row with no user_id — destroy and redirect
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href=".\images\logo\5thFighterWing-logo.png">
    <link rel="stylesheet" href=".\stylesheet\personnelaccounts.css">
    <title>Main Dashboard</title>
</head>
<body>

<div class="body">

<div class="left-panel">
<div class="sidebar-panel">

    <h1 class="sidebar-header">
        iSecure
    </h1>

<div class="nav-links">

    <ul>
    <h6>MENU</h6>
        <li><i class="fa-solid fa-gauge-high"></i><a href="..\iSecure - final\maindashboard.php"> Main Dashboard</a></li>
        <li><i class="fa-solid fa-video"></i><a href="#"> Camera View</a></li>
        <li class="camera-view-drop-down"><i class="fa-solid fa-circle-dot"></i><a href="#"> Live Feed</a></li>
        <li class="camera-view-drop-down"><i class="fa-solid fa-id-card-clip"></i><a href="#"> Person Information</a></li>
        <li><i class="fa-solid fa-user"></i><a href="#"> Visitors</a></li>
        <li><i class="fa-solid fa-car-side"></i><a href="#"> Vehicles</a></li>
        <li><i class="fa-solid fa-user-gear"></i><a href="#"> Personnels</a></li>
        <li><i class="fa-solid fa-clock-rotate-left"></i><a href="#"> Pendings</a></li>
    <h6>DASHBOARD WIDGETS</h6>
        <li><i class="fa-solid fa-chart-column"></i><a href="#"> Daily Visits Analysis</a></li>
        <li><i class="fa-solid fa-list-check"></i><a href="#"> Visitor Status</a></li>
    <h6>DATA MANAGEMENT</h6>
        <li><i class="fa-solid fa-image-portrait"></i><a href="#"> Personnel Accounts</a></li>
        <li><i class="fa-solid fa-box-archive"></i><a href="#"> Inventory</a></li>
    <h6>CUSTOMIZATION</h6>
        <li><i class="fa-solid fa-newspaper"></i><a href="..\iSecure - final\customizelanding.php"> Landing Page</a></li>
    </ul>

</div>

</div>
</div>

<div class="right-panel">
<div class="main-content">
    
    <div class="main-header">
        <div class="header-left">
            <i class="fa-solid fa-home"></i> 
            <h6 class="path"> / Dashboards /</h6>
            <h6 class="current-loc">Personnel Accounts</h6>
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

    <div class="personnel-container">
    <div class="personnel-topbar">
      
         <!-- Only Admin should see this button (restrict via PHP session later) -->
        <input class="search-bar" type="text" name="search" id="search"><i id="search-icon" class="fa-solid fa-search"></i>
        <button class="role-btn" onclick="showDropdown()"><i class="fa-solid fa-user"></i>    Roles    <i class="fa-solid fa-caret-down"></i></button>
       <?php if ($role === 'Admin'): ?>
        <button id="addPersonnelBtn" class="add-btn">+ Add Personnel</button>
        
       <?php endif; ?>
    </div>
   
    
    <!-- Users Table -->
    <table id="userTable">
      <thead>
        <tr>
          <th>Full Name</th>
          <th>Email</th>
          <th>Rank</th>
          <th>Status</th>
          <th>Role</th>
          <th>Joined</th>
          <th>Last Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

<!-- Add Personnel Modal -->
<div id="addUserModal" class="modal" aria-hidden="true">
  <div class="modal-content form-modal">
    <span class="close-modal">&times;</span> <!-- This is the "X" close button -->
    <h2>Create Personnel Account</h2>

    <form id="addUserForm" novalidate>
      <label for="full_name">Full Name</label>
      <input id="full_name" name="full_name" type="text" placeholder="Enter full name" required>

      <label for="email">Email</label>
      <input id="email" name="email" type="email" placeholder="Enter email" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" placeholder="Enter password" required>

      <label for="password_confirm">Confirm Password</label>
      <input id="password_confirm" name="password_confirm" type="password" placeholder="Re-enter password" required>

      <label for="rank">Rank</label>
      <select id="rank" name="rank">
        <option value="Private">Private</option>
        <option value="Corporal">Corporal</option>
        <option value="Sergeant">Sergeant</option>
        <option value="Lieutenant">Lieutenant</option>
        <option value="Captain">Captain</option>
        <option value="Major">Major</option>
        <option value="Colonel">Colonel</option>
        <option value="General">General</option>
      </select>

      <label for="role">Role</label>
      <select id="role" name="role">
        <option value="User">User</option>
        <option value="Admin">Admin</option>
      </select>

      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
        <option value="Banned">Banned</option>
        <option value="Pending">Pending</option>
        <option value="Suspended">Suspended</option>
      </select>

      <div class="modal-actions">
        <button class="confirm" type="submit">✅ Create Account</button>
        <button class="cancel" type="button" onclick="closeAddUserForm()">❌ Cancel</button>
      </div>
    </form>
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


<!-- Notification Modal -->
<div id="notifyModal" class="custom-modal">
  <div class="custom-modal-content">
    <p id="notifyMessage">Action completed successfully!</p>
    <div class="modal-actions">
      <button id="notifyOk" class="btn ok-btn">OK</button>
    </div>
  </div>
</div>


</div>

</div>
</div>
<script src="personnelaccount.js"></script>
<script src="session_check.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>