<?php
include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = $_POST['name'];
    $contact  = $_POST['contact'];
    $project  = $_POST['project'];
    $location = $_POST['location'];
    $plans    = $_POST['plans'];
    $comments = $_POST['comments'];
    $status   = $_POST['status'];
    $application_type   = $_POST['application_type'];

    try {

        //  GET CURRENT YEAR & MONTH
        $month = date("m");
        $year  = date("y");

        //  GET LAST APPLICATION NUMBER (GLOBAL CONTINUOUS)
        $stmt = $conn->prepare("
            SELECT application_no 
            FROM applications 
            ORDER BY application_id DESC 
            LIMIT 1
        ");

        $stmt->execute();
        $result = $stmt->get_result();

        $lastNumber = 0;

        if ($row = $result->fetch_assoc()) {
            $lastAppNo = $row['application_no'];

            // extract last 5 digits (00001 format)
            $lastNumber = (int) substr($lastAppNo, -5);
        }

        //  increment globally
        $newNumber = str_pad($lastNumber + 1, 5, "0", STR_PAD_LEFT);

        //  final format: YY-MM-00001
        $applicationNo = $year . '-' . $month . '-' . $newNumber;

        //  INSERT
        $stmt = $conn->prepare("
            INSERT INTO applications 
            (application_no, name, contact_no, project_title, application_type, location, plan_type, comments, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssss",
            $applicationNo,
            $name,
            $contact,
            $project,
            $application_type,
            $location,
            $plans,
            $comments,
            $status
        );

        if ($stmt->execute()) {

            echo json_encode([
                "status" => "success",
                "appNo" => $applicationNo
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to insert"
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
