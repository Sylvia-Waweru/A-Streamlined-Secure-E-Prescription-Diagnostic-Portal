<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['patientName'] ?? '';
    $age = $_POST['age'] ?? 0;
    $gender = $_POST['gender'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $history = $_POST['medical_history'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO patients (name, age, gender, phone, email, address, medical_history) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $age, $gender, $phone, $email, $address, $history])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
} else {
    echo json_encode(["status" => "invalid_request"]);
}
?>
