<?php
require 'auth_check.php';
require 'db_connect.php';

// Fetch all clearance badges with visitor names
$stmt = $pdo->query("SELECT cb.*, v.first_name, v.last_name FROM clearance_badges cb JOIN visitors v ON cb.visitor_id = v.id ORDER BY cb.issued_at DESC");
$badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Key Cards List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="./images/logo/5thFighterWing-logo.png">
    <link rel="stylesheet" href="./stylesheet/admin-maindashboard.css" />
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
            <li><i class="fa-solid fa-user"></i><a href="visitors.php"> Visitors</a></li>
            <li><i class="fa-solid fa-car-side"></i><a href="vehicles.php"> Vehicles</a></li>
            <li><i class="fa-solid fa-user-gear"></i><a href="personnels.php"> Personnels</a></li>
            <li><i class="fa-solid fa-clock-rotate-left"></i><a href="pendings.php"> Pendings</a></li>
            <h6>DATA MANAGEMENT</h6>
            <li><i class="fa-solid fa-image-portrait"></i><a href="personnelaccounts.php"> Personnel Accounts</a></li>
            <li><i class="fa-solid fa-id-badge"></i><a href="key_cards.php"> Key Cards</a></li>
            <li><i class="fa-solid fa-list"></i><a href="key_card_list.php"> Key Cards List</a></li>
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
        <i class="fa-solid fa-id-badge"></i>
        <h6 class="path"> / Data Management /</h6>
        <h6 class="current-loc">Key Cards List</h6>
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
<div class="container-fluid mt-4">
    <h2>All Issued Key Cards</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered key-card-list-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Key Card Number</th>
                    <th>Validity Start</th>
                    <th>Validity End</th>
                    <th>Status</th>
                    <th>Issued At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($badges)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No key cards found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($badges as $badge): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($badge['id']); ?></td>
                            <td><?php echo htmlspecialchars($badge['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($badge['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($badge['key_card_number']); ?></td>
                            <td><?php echo date('Y-m-d h:i:s A', strtotime($badge['validity_start'])); ?></td>
                            <td><?php echo date('Y-m-d h:i:s A', strtotime($badge['validity_end'])); ?></td>
                            <td><?php echo htmlspecialchars($badge['status']); ?></td>
                            <td><?php echo date('Y-m-d h:i:s A', strtotime($badge['issued_at'])); ?></td>
                            <td><?php echo date('Y-m-d h:i:s A', strtotime($badge['updated_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</div>
</body>
</html>
