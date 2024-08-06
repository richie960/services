<?php
// book_service.php

// Get the JSON input from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Extract data from the request
$companyName = isset($data['company_name']) ? trim($data['company_name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$message = isset($data['message']) ? trim($data['message']) : '';

$response = array('success' => false, 'message' => '');

// Validate input
if (empty($companyName) || empty($email)) {
    $response['message'] = 'Company name and email are required.';
    echo json_encode($response);
    exit;
}

// Define the directory path for storing the files
$directoryPath = __DIR__ . '/company_data/' . $companyName;

// Create the directory if it does not exist
if (!is_dir($directoryPath)) {
    if (!mkdir($directoryPath, 0755, true)) {
        $response['message'] = 'Failed to create directory.';
        echo json_encode($response);
        exit;
    }
}

// Define the file path for the email
$filePath = $directoryPath . '/' . $email . '.txt';

// Check if the file already exists
if (file_exists($filePath)) {
    $response['success'] = true;  // File already exists, consider it a success
    $response['message'] = 'File already exists, booking successful.';
} else {
    // Create the file with the initial message (if any)
    if (file_put_contents($filePath, $message) !== false) {
        $response['success'] = true;
        $response['message'] = 'Booking successful, file created.';
    } else {
        $response['message'] = 'Failed to create file.';
    }
}

// Return JSON response
echo json_encode($response);
?>
