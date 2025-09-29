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
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $cropX = (float)($_POST['crop_x'] ?? 0.5);
    $cropY = (float)($_POST['crop_y'] ?? 0.5);
    $zoomLevel = (float)($_POST['zoom_level'] ?? 1.0);
    $imagePath   = null;

    // Handle image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/news_headlines/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName   = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $tmpFile = $_FILES["image"]["tmp_name"];

        if (move_uploaded_file($tmpFile, $targetFile)) {
            // Resize the image to 200x150 to match headlines container
            resizeImage($targetFile, $targetFile, 200, 150, $cropX, $cropY, $zoomLevel);
            $imagePath = $targetFile;
        }
    }

    if ($id) {
        // Fetch current headline data for archiving
        $stmt = $pdo->prepare("SELECT title, description, image_path FROM news_headlines WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $current = $stmt->fetch();

        if ($current) {
            // Archive old data
            $archiveStmt = $pdo->prepare("
                INSERT INTO landing_archives (headline_title, headline_description, headline_image)
                VALUES (:title, :desc, :image)
            ");
            $archiveStmt->execute([
                ':title' => $current['title'],
                ':desc'  => $current['description'],
                ':image' => $current['image_path']
            ]);
        }

        // If updating and no new image, keep old image
        if (!$imagePath) {
            $imagePath = $current['image_path'];
        }
        // Update existing record
        $update = $pdo->prepare("
            UPDATE news_headlines
            SET title = :title, description = :desc, image_path = :image
            WHERE id = :id
        ");
        $update->execute([
            ':title' => $title,
            ':desc'  => $description,
            ':image' => $imagePath,
            ':id'    => $id
        ]);
    } else {
        // Insert new Headline
        $insert = $pdo->prepare("INSERT INTO news_headlines (title, description, image_path) VALUES (:title, :desc, :image)");
        $insert->execute([':title' => $title, ':desc' => $description, ':image' => $imagePath]);
    }

    $activeTab = $_POST['active_tab'] ?? 'headlines';
    $msg = 'Headline updated';
    header("Location: customizelanding.php?active_tab=" . urlencode($activeTab) . "&msg=" . urlencode($msg));
    exit;
}
?>
