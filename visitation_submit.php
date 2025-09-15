<?php
session_start();
require 'db_connect.php'; // use your existing connection
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
$visitor_name       = $_POST['visitor_name'] ?? null;
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
$visit_date         = $_POST['visit_date'] ?? null;
$visit_time         = $_POST['visit_time'] ?? null;

// Upload files
$valid_id_path      = uploadFile("valid_id");
$selfie_photo_path  = uploadFile("selfie_photo");
$vehicle_photo_path = uploadFile("vehicle_photo");

// Insert into DB
$stmt = $pdo->prepare("
    INSERT INTO visitation_requests 
    (visitor_name, home_address, contact_number, email, valid_id_path, selfie_photo_path, vehicle_owner, vehicle_brand, plate_number, vehicle_color, vehicle_model, vehicle_photo_path, reason, personnel_related, visit_date, visit_time) 
    VALUES (:visitor_name, :home_address, :contact_number, :email, :valid_id_path, :selfie_photo_path, :vehicle_owner, :vehicle_brand, :plate_number, :vehicle_color, :vehicle_model, :vehicle_photo_path, :reason, :personnel_related, :visit_date, :visit_time)
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
    ':visit_date'        => $visit_date,
    ':visit_time'        => $visit_time
]);

if ($success) {
    log_landing_action($pdo, $token, "Submitted visitation request form");
    echo "<script>alert('Visitation request submitted successfully!'); window.location.href='landingpage.php';</script>";
} else {
    echo "<script>alert('Error saving request. Please try again.'); window.history.back();</script>";
}
