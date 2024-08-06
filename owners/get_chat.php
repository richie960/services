<?php
// get_chat.php

// Get the JSON input from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Extract data from the request
$companyName = isset($data['company_name']) ? trim($data['company_name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';

// Initialize the response array
$response = array('messages' => array());

// Validate input
if (empty($companyName) || empty($email)) {
    $response['messages'][] = array('sender' => 'system', 'text' => 'Company name and email are required.');
    echo json_encode($response);
    exit;
}

// Define the directory path for storing the files
$directoryPath = __DIR__ . '/company_data/' . $companyName;

// Define the file path for the email
$filePath = $directoryPath . '/' . $email . '.txt';

// Check if the directory exists
if (!is_dir($directoryPath)) {
    // If directory doesn't exist, no messages can be found
    echo json_encode($response);
    exit;
}

// Check if the file exists and is readable
if (file_exists($filePath) && is_readable($filePath)) {
    // Read the file contents
    $fileContents = file_get_contents($filePath);
    $lines = explode("\n", trim($fileContents));

    // Parse the file contents into messages
    foreach ($lines as $line) {
        if (strpos($line, ':') !== false) {
            list($sender, $text) = explode(":", $line, 2);
            $response['messages'][] = array('sender' => $sender, 'text' => $text);
        }
    }
}

// Return JSON response
echo json_encode($response);
?>
