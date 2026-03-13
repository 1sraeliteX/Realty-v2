<?php
/**
 * File Upload Handler for ID Documents
 * Handles file uploads for occupant ID documents
 */

// Initialize framework
require_once __DIR__ . '/../config/bootstrap.php';

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Check if files were uploaded
    if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
        throw new Exception('No files uploaded');
    }

    $uploadedFiles = [];
    $files = $_FILES['files'];
    
    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/uploads/documents/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Process each uploaded file
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            throw new Exception('Upload error: ' . $files['error'][$i]);
        }

        $fileName = $files['name'][$i];
        $fileTmpName = $files['tmp_name'][$i];
        $fileSize = $files['size'][$i];
        $fileType = $files['type'][$i];

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type: ' . $fileType);
        }

        if ($fileSize > $maxSize) {
            throw new Exception('File too large: ' . $fileName);
        }

        // Generate unique filename
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('id_doc_', true) . '.' . $fileExtension;
        $uploadPath = $uploadDir . $uniqueName;

        // Move file to upload directory
        if (!move_uploaded_file($fileTmpName, $uploadPath)) {
            throw new Exception('Failed to move uploaded file: ' . $fileName);
        }

        // Store file info
        $uploadedFiles[] = [
            'name' => $fileName,
            'unique_name' => $uniqueName,
            'path' => '/uploads/documents/' . $uniqueName,
            'size' => $fileSize,
            'type' => $fileType,
            'uploaded_at' => date('Y-m-d H:i:s')
        ];
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Files uploaded successfully',
        'files' => $uploadedFiles
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'files' => []
    ]);
}
?>
