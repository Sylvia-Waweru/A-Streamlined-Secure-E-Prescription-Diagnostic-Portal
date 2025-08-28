<?php
session_name("patient_session");
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$patientId = $_SESSION['patient_id'];

$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$patientId]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'Patient not found']);
    exit;
}

$patientName = $user['fullname'];

$updateStmt = $pdo->prepare("UPDATE prescriptions SET viewed_by_patient = 1 WHERE patient_name = ?");
$updateStmt->execute([$patientName]);

echo json_encode(['success' => true]);
?>
