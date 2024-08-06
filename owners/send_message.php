<?php
// send_message.php

// Get the JSON input from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Extract data from the request
$companyName = isset($data['company_name']) ? trim($data['company_name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$message = isset($data['message']) ? trim($data['message']) : '';

// Initialize the response array
$response = array('success' => false, 'message' => '');

// Validate input
if (empty($companyName) || empty($email) || empty($message)) {
    $response['message'] = 'Company name, email, and message are required.';
    echo json_encode($response);
    exit;
}

// Define the directory path for storing the files
$directoryPath = __DIR__ . '/company_data/' . $companyName;

// Define the file path for the email
$filePath = $directoryPath . '/' . $email . '.txt';

// Check if the directory exists
if (!is_dir($directoryPath)) {
    $response['message'] = 'Directory does not exist.';
    echo json_encode($response);
    exit;
}

// Define the message format
$formattedMessage = 'user:' . $message . "\n";

// Append the message to the file
if (file_put_contents($filePath, $formattedMessage, FILE_APPEND | LOCK_EX) !== false) {
    $response['success'] = true;
    $response['message'] = 'Message sent successfully.';
} else {
    $response['message'] = 'Failed to write message to file.';
}

// Return JSON response
echo json_encode($response);
?>
