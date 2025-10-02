<?php
require 'auth_check.php';
require 'db_connect.php';

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

// Fetch visitors inside the base with vehicle info
$stmt = $pdo->query("
    SELECT CONCAT(v.first_name, ' ', v.last_name) AS full_name, v.contact_number, v.email, v.reason, v.date, v.time_in,
           veh.vehicle_brand, veh.vehicle_model, veh.vehicle_color, veh.plate_number, v.id
    FROM visitors v
    LEFT JOIN visitation_requests vr ON vr.visitor_name = CONCAT(v.first_name, ' ', v.last_name) AND vr.visit_date = v.date
    LEFT JOIN vehicles veh ON veh.visitation_id = vr.id
    WHERE v.status = 'Inside'
    ORDER BY v.time_in DESC
");
$visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Personnel Visitors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./stylesheet/personnel_dashboard.css" />
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
        <i class="fa-solid fa-users"></i>
        <h6 class="path"> / Dashboard /</h6>
        <h6 class="current-loc">Visitors Inside</h6>
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
<div class="container-fluid mt-4">
    <h2>Visitors Currently Inside the Base</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Reason</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Vehicle Brand</th>
                    <th>Vehicle Model</th>
                    <th>Vehicle Color</th>
                    <th>Plate Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($visitors)): ?>
                    <tr>
                        <td colspan="12" class="text-center">No visitors currently inside.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($visitors as $visitor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($visitor['id']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['email']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['reason']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['date']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['time_in']); ?></td>
                            <td><?php echo htmlspecialchars($visitor['vehicle_brand'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($visitor['vehicle_model'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($visitor['vehicle_color'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($visitor['plate_number'] ?? 'N/A'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="markExit(<?php echo $visitor['id']; ?>)">Mark Exit</button>
                            </td>
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

<div class="container-fluid mt-4">
    <h3>Expected Visitors</h3>
    <div class="mb-3">
        <label for="expectedVisitorSelect" class="form-label">Select Expected Visitor</label>
        <select id="expectedVisitorSelect" class="form-select">
            <option value="">-- Select Expected Visitor --</option>
            <?php
            // Fetch expected visitors (status not 'Inside')
            $stmt = $pdo->query("SELECT id, full_name FROM visitors WHERE status != 'Inside' ORDER BY full_name ASC");
            $expectedVisitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($expectedVisitors as $ev) {
                echo '<option value="' . htmlspecialchars($ev['id']) . '">' . htmlspecialchars($ev['full_name']) . '</option>';
            }
            ?>
        </select>
    </div>
    <button class="btn btn-primary" onclick="markExpectedEntry()">Mark Entry</button>
</div>

<script>
function markExit(visitorId) {
    if (!confirm('Are you sure you want to mark this visitor as exited?')) {
        return;
    }
    fetch('mark_exit_visitor.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'visitor_id=' + encodeURIComponent(visitorId)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        alert('Error marking exit: ' + error);
    });
}

function markEntry(visitorId) {
    if (!confirm('Are you sure you want to mark this visitor as inside?')) {
        return;
    }
    fetch('mark_entry_visitor.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'visitor_id=' + encodeURIComponent(visitorId)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        alert('Error marking entry: ' + error);
    });
}

function markExpectedEntry() {
    const visitorId = document.getElementById('expectedVisitorSelect').value;
    if (!visitorId) {
        alert('Please select an expected visitor.');
        return;
    }
    if (!confirm('Are you sure you want to mark this visitor as inside?')) {
        return;
    }
    fetch('mark_entry_visitor.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'visitor_id=' + encodeURIComponent(visitorId)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        alert('Error marking entry: ' + error);
    });
}
</script>

<script src="./scripts/personnel_dashboard.js"></script>
<script src="./scripts/session_check.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
