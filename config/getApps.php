<?php
require_once 'db.php';

$sql = "SELECT * FROM applications ORDER BY application_id DESC";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
