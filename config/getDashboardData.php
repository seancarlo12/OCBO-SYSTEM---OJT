<?php
include_once 'db.php';

header('Content-Type: application/json');

$sql = "SELECT 
    application_no,
    name,
    contact_no,
    application_type,
    project_title,
    location,
    plan_type,
    status,
    date_received,
    last_updated
FROM applications";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);