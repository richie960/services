<?php
header('Content-Type: application/json');
include 'db_connection.php';

$owner_id = $_GET['owner_id'];

$sql = "SELECT * FROM reviews WHERE owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = array();
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($reviews);
?>
