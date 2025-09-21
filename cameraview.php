<?php
 require 'auth_check.php';
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
    <link rel="stylesheet" href=".\stylesheet\cameraview.css">
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
        <li><i class="fa-solid fa-video"></i><a href="..\iSecure - final\cameraview.php"> Camera View</a></li>
        <li class="camera-view-drop-down"><i class="fa-solid fa-circle-dot"></i><a href="..\iSecure - final\livefeed.php"> Live Feed</a></li>
        <li class="camera-view-drop-down"><i class="fa-solid fa-id-card-clip"></i><a href="..\iSecure - final\personinformation.php"> Person Information</a></li>
        <li><i class="fa-solid fa-user"></i><a href="..\iSecure - final\visitors.php"> Visitors</a></li>
        <li><i class="fa-solid fa-car-side"></i><a href="..\iSecure - final\vehicles.php"> Vehicles</a></li>
        <li><i class="fa-solid fa-user-gear"></i><a href="..\iSecure - final\personnels.php"> Personnels</a></li>
        <li><i class="fa-solid fa-clock-rotate-left"></i><a href="..\iSecure - final\pendings.php"> Pendings</a></li>
    <h6>DATA MANAGEMENT</h6>
        <li><i class="fa-solid fa-image-portrait"></i><a href="..\iSecure - final\personnelaccounts.php"> Personnel Accounts</a></li>
        <li><i class="fa-solid fa-box-archive"></i><a href="..\iSecure - final\inventory.php"> Inventory</a></li>
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
            <h6 class="path"> / Dashboard /</h6>
            <h6 class="current-loc">Camera View</h6>
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

<div class="camera-feed">
    <div class="camera-view"><i class="fa-solid fa-video"></i></div>
    <div class="camera-view"><i class="fa-solid fa-video"></i></div>
    <div class="camera-view"><i class="fa-solid fa-video"></i></div>
</div>

<div class="camera-feed-status">
    <div class="stats-container">
        <h2>Vehicle Recognition Camera</h2><br>
        <h3>Status: Live <i class="fa-solid fa-circle"></i></h3> 
    </div>
    <div class="stats-container">
        <h2>Vehicle Recognition Camera</h2><br>
        <h3>Status: Live <i class="fa-solid fa-circle"></i></h3> 
    </div>
    <div class="stats-container">
        <h2>Vehicle Recognition Camera</h2><br>
        <h3>Status: Live <i class="fa-solid fa-circle"></i></h3> 
    </div>
</div>

</div>

</div>
</div>
<script src="./scripts/cameraview.js"></script>
<script src="session_check.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>