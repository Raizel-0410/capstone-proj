<?php
require 'auth_check.php';
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? 'approve'; // Default to approve

    if ($id) {
        try {
            // Mark visitation request as approved or rejected based on action
            $status = ($action === 'reject') ? 'Rejected' : 'Approved';
            $stmt = $pdo->prepare("UPDATE visitation_requests SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);

            // Fetch visitation request details
            $stmt = $pdo->prepare("SELECT * FROM visitation_requests WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($request) {
                // Insert or update vehicle status to Expected only if vehicle data exists
                if (!empty($request['plate_number'])) {
                    $stmt = $pdo->prepare("
                        INSERT INTO vehicles
                            (visitation_id, vehicle_owner, vehicle_brand, vehicle_model, vehicle_color, plate_number, vehicle_photo_path, entry_time, exit_time, status)
                        VALUES
                            (:visitation_id, :vehicle_owner, :vehicle_brand, :vehicle_model, :vehicle_color, :plate_number, :vehicle_photo_path, NULL, NULL, 'Expected')
                        ON DUPLICATE KEY UPDATE status = 'Expected'
                    ");
                    $stmt->execute([
                        ':visitation_id'      => $request['id'],
                        ':vehicle_owner'      => $request['visitor_name'],
                        ':vehicle_brand'      => $request['vehicle_brand'],
                        ':vehicle_model'      => $request['vehicle_model'],
                        ':vehicle_color'      => $request['vehicle_color'],
                        ':plate_number'       => $request['plate_number'],
                        ':vehicle_photo_path' => $request['vehicle_photo_path'] ?? null
                    ]);
                }

                // Split visitor_name into first and last name
                $nameParts = explode(' ', $request['visitor_name']);
                $lastName = array_pop($nameParts);
                $firstName = implode(' ', $nameParts);

                // Insert into visitors table (now includes full details)
                $stmt = $pdo->prepare("
                    INSERT INTO visitors
                        (first_name, last_name, contact_number, email, address, reason, id_photo_path, selfie_photo_path, date, time_in, status)
                    VALUES
                        (:first_name, :last_name, :contact_number, :email, :address, :reason, :id_photo, :selfie, :visit_date, NULL, 'Expected')
                ");
                $stmt->execute([
                    ':first_name'     => $firstName,
                    ':last_name'      => $lastName,
                    ':contact_number' => $request['contact_number'] ?? null,
                    ':email'          => $request['email'] ?? null,
                    ':address'        => $request['home_address'] ?? null,
                    ':reason'         => $request['reason'],
                    ':id_photo'       => $request['valid_id_path'] ?? null,
                    ':selfie'         => $request['selfie_photo_path'] ?? null,
                    ':visit_date'     => $request['visit_date'] ?? date('Y-m-d')
                ]);

                // Insert notification for personnel user
                $stmt = $pdo->prepare("
                    SELECT id FROM users WHERE full_name = :full_name LIMIT 1
                ");
                $stmt->execute([':full_name' => $request['personnel_related']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $actionText = ($action === 'reject') ? 'rejected' : 'approved';
                    $notificationMessage = "Your visitation request for " . htmlspecialchars($request['visitor_name']) . " has been " . $actionText . ".";
                    $stmt = $pdo->prepare("
                        INSERT INTO notifications (user_id, message, created_at, read_status)
                        VALUES (:user_id, :message, NOW(), 'Unread')
                    ");
                    $stmt->execute([
                        ':user_id' => $user['id'],
                        ':message' => $notificationMessage
                    ]);
                }
            }

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing request ID']);
    }
}
