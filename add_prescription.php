<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient = $_POST['patient_name'] ?? '';
    $age = $_POST['age'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $prescription = $_POST['prescription'] ?? '';
    $medicine = $_POST['medicine_name'] ?? '';
    $dosage = $_POST['dosage'] ?? '';
    $frequency = $_POST['frequency'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $doctor = $_POST['prescribed_by'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO prescriptions (patient_name, age, diagnosis, prescription, medicine_name, dosage, frequency, duration, notes, prescribed_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$patient, $age, $diagnosis, $prescription, $medicine, $dosage, $frequency, $duration, $notes, $doctor])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>
