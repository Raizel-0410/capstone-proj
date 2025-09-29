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
    $cropX = (float)($_POST['crop_x'] ?? 0.5);
    $cropY = (float)($_POST['crop_y'] ?? 0.5);
    $zoomLevel = (float)($_POST['zoom_level'] ?? 1.0);
    $imagePath = null;

    // Handle image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/news_carousel/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName   = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $tmpFile = $_FILES["image"]["tmp_name"];

        if (move_uploaded_file($tmpFile, $targetFile)) {
            // Resize the image to 800x450 to match carousel container
            resizeImage($targetFile, $targetFile, 800, 450, $cropX, $cropY, $zoomLevel);
            $imagePath = $targetFile;
        }
    }

    if ($id) {
        // Fetch current news carousel data for archiving
        $stmt = $pdo->prepare("SELECT image_path FROM news_carousel WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $current = $stmt->fetch();

        if ($current) {
            // Archive old data
            $archiveStmt = $pdo->prepare("
                INSERT INTO landing_archives (news_carousel_image)
                VALUES (:image)
            ");
            $archiveStmt->execute([
                ':image' => $current['image_path']
            ]);
        }

        // If updating and no new image, keep old image
        if (!$imagePath) {
            $imagePath = $current['image_path'];
        }
        // Update existing record
        $update = $pdo->prepare("
            UPDATE news_carousel
            SET image_path = :image
            WHERE id = :id
        ");
        $update->execute([
            ':image' => $imagePath,
            ':id'    => $id
        ]);
    } else {
        // Insert new News Carousel image
        $insert = $pdo->prepare("INSERT INTO news_carousel (image_path) VALUES (:image)");
        $insert->execute([':image' => $imagePath]);
    }

    $activeTab = $_POST['active_tab'] ?? 'newsCarousel';
    $msg = 'News Carousel updated';
    header("Location: customizelanding.php?active_tab=" . urlencode($activeTab) . "&msg=" . urlencode($msg));
    exit;
}
?>
