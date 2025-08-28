<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientName = $_POST['patientName'] ?? '';
    $name = $_POST['medicineName'] ?? '';
    $type = $_POST['medicineType'] ?? '';
    $description = $_POST['medicineDescription'] ?? '';
    $side_effects = $_POST['sideEffects'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO medicines (patient_name, name, type, description, side_effects) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$patientName, $name, $type, $description, $side_effects])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>
