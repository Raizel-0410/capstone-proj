<?php
require 'auth_check.php';
require 'db_connect.php';
require 'audit_log.php';

// File upload function
function uploadFile($fileInput, $uploadDir = "uploads/") {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES[$fileInput]["name"]);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES[$fileInput]["tmp_name"], $targetFile)) {
        return $targetFile;
    }
    return null;
}

// Collect form inputs
$first_name         = $_POST['first_name'] ?? null;
$last_name          = $_POST['last_name'] ?? null;
$visitor_name       = trim($first_name . ' ' . $last_name);
$home_address       = $_POST['home_address'] ?? null;
$contact_number     = $_POST['contact_number'] ?? null;
$email              = $_POST['email'] ?? null;
$vehicle_owner      = $_POST['vehicle_owner'] ?? null;
$vehicle_brand      = $_POST['vehicle_brand'] ?? null;
$plate_number       = $_POST['plate_number'] ?? null;
$vehicle_color      = $_POST['vehicle_color'] ?? null;
$vehicle_model      = $_POST['vehicle_model'] ?? null;
$reason             = $_POST['reason'] ?? null;
$personnel_related  = $_POST['personnel_related'] ?? null;
$office_to_visit    = $_POST['office_to_visit'] ?? null;
$visit_date         = date('Y-m-d'); // Fixed to today
$visit_time         = date('H:i:s'); // Set to current time

// Upload files
$valid_id_path      = uploadFile("valid_id");
$selfie_photo_path  = uploadFile("selfie_photo");
$vehicle_photo_path = uploadFile("vehicle_photo");

// Insert into visitation_requests
$stmt = $pdo->prepare("
    INSERT INTO visitation_requests
    (visitor_name, home_address, contact_number, email, valid_id_path, selfie_photo_path,
     vehicle_owner, vehicle_brand, plate_number, vehicle_color, vehicle_model, vehicle_photo_path,
     reason, personnel_related, office_to_visit, visit_date, visit_time, status)
    VALUES (:visitor_name, :home_address, :contact_number, :email, :valid_id_path, :selfie_photo_path,
            :vehicle_owner, :vehicle_brand, :plate_number, :vehicle_color, :vehicle_model, :vehicle_photo_path,
            :reason, :personnel_related, :office_to_visit, :visit_date, :visit_time, 'Pending')
");

$success = $stmt->execute([
    ':visitor_name'      => $visitor_name,
    ':home_address'      => $home_address,
    ':contact_number'    => $contact_number,
    ':email'             => $email,
    ':valid_id_path'     => $valid_id_path,
    ':selfie_photo_path' => $selfie_photo_path,
    ':vehicle_owner'     => $vehicle_owner,
    ':vehicle_brand'     => $vehicle_brand,
    ':plate_number'      => $plate_number,
    ':vehicle_color'     => $vehicle_color,
    ':vehicle_model'     => $vehicle_model,
    ':vehicle_photo_path'=> $vehicle_photo_path,
    ':reason'            => $reason,
    ':personnel_related' => $personnel_related,
    ':office_to_visit'   => $office_to_visit,
    ':visit_date'        => $visit_date,
    ':visit_time'        => $visit_time
]);

if ($success) {
    // Get the new visitation ID
    $visitationId = $pdo->lastInsertId();

    // If a vehicle was included, insert it properly now
    if (!empty($plate_number)) {
        $stmt = $pdo->prepare("
            INSERT INTO vehicles
            (visitation_id, vehicle_owner, vehicle_brand, vehicle_model, vehicle_color, plate_number, vehicle_photo_path, status)
            VALUES (:visitation_id, :vehicle_owner, :vehicle_brand, :vehicle_model, :vehicle_color, :plate_number, :vehicle_photo_path, 'Pending')
        ");
        $stmt->execute([
            ':visitation_id'      => $visitationId,
            ':vehicle_owner'      => $vehicle_owner ?: $visitor_name,
            ':vehicle_brand'      => $vehicle_brand,
            ':vehicle_model'      => $vehicle_model,
            ':vehicle_color'      => $vehicle_color,
            ':plate_number'       => $plate_number,
            ':vehicle_photo_path' => $vehicle_photo_path
        ]);
    }

    // Log action
    $user_id = $_SESSION['user_id'] ?? null;
    log_admin_action($pdo, $user_id, "Submitted walk-in visitation request for $visitor_name");

    echo "<script>alert('Walk-in visitation request submitted successfully!'); window.location.href='personnel_dashboard.php';</script>";
} else {
    echo "<script>alert('Error saving request. Please try again.'); window.history.back();</script>";
}
