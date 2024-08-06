<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "workplace";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM serviceowners WHERE id = $id";
$result = $conn->query($sql);

$owner = null;
if ($result->num_rows > 0) {
    $owner = $result->fetch_assoc();
}

$conn->close();
echo json_encode($owner);
?>
