<?php
require 'db_connect.php';

function resizeImage($source, $dest, $width, $height, $cropX = 0.5, $cropY = 0.5, $zoomLevel = 1.0) {
    list($origWidth, $origHeight, $type) = getimagesize($source);

    // Create source image based on type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($source);
            break;
        default:
            return false; // Unsupported type
    }

    // Calculate scaling factors for cover crop
    $scaleX = $width / $origWidth;
    $scaleY = $height / $origHeight;
    $scale = max($scaleX, $scaleY) * $zoomLevel;

    // Source dimensions to sample (zoomed/crop area in original coords)
    $srcWidth = $width / $scale;
    $srcHeight = $height / $scale;

    // Calculate crop start positions in original image (0-1 normalized)
    $srcX = ($origWidth - $srcWidth) * $cropX;
    $srcY = ($origHeight - $srcHeight) * $cropY;

    // Create destination image
    $destImage = imagecreatetruecolor($width, $height);

    // Resize and crop from original
    imagecopyresampled(
        $destImage, $sourceImage,
        0, 0, $srcX, $srcY,
        $width, $height, $srcWidth, $srcHeight
    );

    // Save based on original type
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($destImage, $dest, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($destImage, $dest, 6);
            break;
    }

    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($destImage);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $caption_title = $_POST['caption_title'] ?? null;
    $caption_text  = $_POST['caption_text'] ?? null;
    $cropX = (float)($_POST['crop_x'] ?? 0.5);
    $cropY = (float)($_POST['crop_y'] ?? 0.5);
    $zoomLevel = (float)($_POST['zoom_level'] ?? 1.0);
    $imagePath     = null;

    // Handle image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/carousel/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName   = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $tmpFile = $_FILES["image"]["tmp_name"];

        if (move_uploaded_file($tmpFile, $targetFile)) {
            // Resize the image to 1480x500 to match carousel container
            resizeImage($targetFile, $targetFile, 1480, 500, $cropX, $cropY, $zoomLevel);
            $imagePath = $targetFile;
        }
    }

    if ($id) {
        // Fetch current carousel data for archiving
        $stmt = $pdo->prepare("SELECT image_path, caption_title, caption_text FROM landing_carousel WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $current = $stmt->fetch();

        if ($current) {
            // Archive old data
            $archiveStmt = $pdo->prepare("
                INSERT INTO landing_archives (carousel_title, carousel_text, carousel_image)
                VALUES (:title, :text, :image)
            ");
            $archiveStmt->execute([
                ':title' => $current['caption_title'],
                ':text'  => $current['caption_text'],
                ':image' => $current['image_path']
            ]);
        }

        // If updating and no new image, keep old image
        if (!$imagePath) {
            $imagePath = $current['image_path'];
        }
        // Update existing record
        $update = $pdo->prepare("
            UPDATE landing_carousel
            SET image_path = :image, caption_title = :title, caption_text = :text
            WHERE id = :id
        ");
        $update->execute([
            ':image' => $imagePath,
            ':title' => $caption_title,
            ':text'  => $caption_text,
            ':id'    => $id
        ]);
    } else {
        // Insert new Carousel slide with sort_order
        $stmt = $pdo->query("SELECT COALESCE(MAX(sort_order), 0) + 1 AS new_order FROM landing_carousel");
        $newOrder = $stmt->fetchColumn();
        $insert = $pdo->prepare("
            INSERT INTO landing_carousel (image_path, caption_title, caption_text, sort_order)
            VALUES (:image, :title, :text, :order)
        ");
        $insert->execute([
            ':image' => $imagePath,
            ':title' => $caption_title,
            ':text'  => $caption_text,
            ':order' => $newOrder
        ]);
    }

    $activeTab = $_POST['active_tab'] ?? 'carousel';
    $msg = 'Carousel updated';
    header("Location: customizelanding.php?active_tab=" . urlencode($activeTab) . "&msg=" . urlencode($msg));
    exit;
}
?>
