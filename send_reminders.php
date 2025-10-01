<?php
require 'auth_check.php';
require 'db_connect.php';

// Function to send reminder email
function sendReminderEmail($contactNumber, $idPass, $validityEnd) {
    $to = $contactNumber; // Assuming contactNumber is email
    $subject = "Reminder: Your Visitor ID/Pass Expires Soon";
    $message = "Dear Visitor,\n\nYour ID/Pass number: $idPass\n";
    $message .= "Your access expires at: $validityEnd\n\n";
    $message .= "If you need to extend your visit, please contact the base administration.\n";
    $message .= "Thank you.\n";
    $headers = "From: no-reply@isecure.com\r\n" .
               "Reply-To: no-reply@isecure.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    return mail($to, $subject, $message, $headers);
}

try {
    // Find visitors whose validity_end is within 1 hour from now
    $stmt = $pdo->prepare("
        SELECT * FROM visitors
        WHERE validity_end BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 HOUR)
        AND status = 'Inside'
        AND id_pass_number IS NOT NULL
    ");
    $stmt->execute();
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($visitors as $visitor) {
        $contactNumber = $visitor['contact_number'];
        if ($contactNumber) {
            $sent = sendReminderEmail($contactNumber, $visitor['id_pass_number'], $visitor['validity_end']);
            if ($sent) {
                // Log successful send (optional)
                error_log("Reminder sent to visitor ID: " . $visitor['id'] . " at " . $contactNumber);
            } else {
                error_log("Failed to send reminder to visitor ID: " . $visitor['id']);
            }
        }
    }

    echo "Reminder check completed.\n";
} catch (Exception $e) {
    error_log("Error in send_reminders.php: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}
