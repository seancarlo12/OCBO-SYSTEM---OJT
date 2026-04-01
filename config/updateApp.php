<?php
include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $appNo   = $_POST['appNo'];
    $name    = $_POST['name'];
    $contact = $_POST['contact'];
    $project = $_POST['project'];
    $location = $_POST['location'];
    $plans   = $_POST['plans'];
    $comments = $_POST['comments'];
    $status  = $_POST['status'];

    try {
        $stmt = $conn->prepare("
            UPDATE applications 
            SET name = ?, 
                contact_no = ?, 
                project_title = ?, 
                location = ?, 
                plan_type = ?, 
                comments = ?, 
                status = ?
            WHERE application_no = ?
        ");

        $stmt->bind_param(
            "ssssssss",
            $name,
            $contact,
            $project,
            $location,
            $plans,
            $comments,
            $status,
            $appNo
        );

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update"
            ]);
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}
?>