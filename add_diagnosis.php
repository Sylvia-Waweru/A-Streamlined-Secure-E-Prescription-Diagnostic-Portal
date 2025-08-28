<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient = $_POST['patientName'] ?? '';
    $symptoms = $_POST['symptoms'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $recommendation = $_POST['recommendation'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO diagnoses (patient_name, symptoms, diagnosis, recommendation) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$patient, $symptoms, $diagnosis, $recommendation])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
} else {
    echo json_encode(["status" => "invalid_request"]);
}
?>
