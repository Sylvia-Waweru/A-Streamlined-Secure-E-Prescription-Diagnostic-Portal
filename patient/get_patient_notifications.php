<?php
session_name("patient_session");
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['patient_id'])) {
    echo json_encode([
        'error' => 'Not logged in',
        'session' => $_SESSION
    ]);
    exit;
}
 
$patientId = $_SESSION['patient_id'];

$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$patientId]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

$patientName = $user['fullname'];

$presStmt = $pdo->prepare("
    SELECT * FROM prescriptions 
    WHERE patient_name = ? 
    AND viewed_by_patient = 0 
    ORDER BY created_at DESC
");
$presStmt->execute([$patientName]);
$prescriptions = $presStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'prescriptions' => $prescriptions,
    'count' => count($prescriptions),
    'session_patient' => $patientName
]);
?>
