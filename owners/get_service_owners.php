<?php
header('Content-Type: application/json');
include 'db_connection.php';

$id = $_GET['id'];
$query = $_GET['query'];

// Fetch the service name based on the id
$sql = "SELECT name FROM services WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();

// Fetch the service owners based on name and query
$sql = "SELECT * FROM serviceowners WHERE name = ? AND company_name LIKE ?";
$stmt = $conn->prepare($sql);
$like_query = "%$query%"; // Add wildcard characters for partial matching
$stmt->bind_param('ss', $name, $like_query);
$stmt->execute();
$result = $stmt->get_result();

$service_owners = array();
while ($row = $result->fetch_assoc()) {
    $service_owners[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($service_owners);
?>
