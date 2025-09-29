<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Get the current data for archiving
        $stmt = $pdo->prepare("SELECT image_path, caption_title, caption_text FROM landing_carousel WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if ($row) {
            // Archive old data
            $archiveStmt = $pdo->prepare("
                INSERT INTO landing_archives (carousel_title, carousel_text, carousel_image)
                VALUES (:title, :text, :image)
            ");
            $archiveStmt->execute([
                ':title' => $row['caption_title'],
                ':text'  => $row['caption_text'],
                ':image' => $row['image_path']
            ]);

            // Delete the file if exists
            if (file_exists($row['image_path'])) {
                unlink($row['image_path']);
            }
        }

        // Delete from database
        $delete = $pdo->prepare("DELETE FROM landing_carousel WHERE id = :id");
        $delete->execute([':id' => $id]);
    }

    $activeTab = $_POST['active_tab'] ?? 'carousel';
    $msg = 'Carousel deleted';
    header("Location: customizelanding.php?active_tab=" . urlencode($activeTab) . "&msg=" . urlencode($msg));
    exit;
}
?>
