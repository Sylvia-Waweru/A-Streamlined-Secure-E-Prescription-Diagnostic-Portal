<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['patients' => 0, 'appointments' => 0]);
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['patients' => 0, 'appointments' => 0]);
    exit();
}

$doctorName = $user['fullname'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_name = ?");
$stmt->execute([$doctorName]);
$appointments = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT patient_name) FROM appointments WHERE doctor_name = ?");
$stmt->execute([$doctorName]);
$patients = $stmt->fetchColumn();

echo json_encode(['appointments' => $appointments, 'patients' => $patients]);
