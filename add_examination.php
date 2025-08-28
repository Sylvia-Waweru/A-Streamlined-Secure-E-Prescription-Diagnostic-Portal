<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient = $_POST['patient_name'] ?? '';
    $exam_type = $_POST['exam_type'] ?? '';
    $results = $_POST['results'] ?? '';
    $notes = $_POST['notes'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO examinations (patient_name, exam_type, results, notes) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$patient, $exam_type, $results, $notes])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>
