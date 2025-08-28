<?php
session_name("doctor_session");
session_start();
require 'db.php';

if (!isset($_SESSION['doctor_id'])) {
    echo json_encode([]);
    exit();
}

$userId = $_SESSION['doctor_id'];
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode([]);
    exit();
}

$doctorName = $user['fullname'];

$stmt = $pdo->prepare("SELECT id, patient_name, appointment_date, time_slot FROM appointments WHERE doctor_name = ? AND viewed = 0");
$stmt->execute([$doctorName]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($appointments);
?>