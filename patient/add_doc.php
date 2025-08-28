<?php
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.html");
    exit();
}

$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$user = $stmt->fetch();
$patientName = $user ? htmlspecialchars($user['fullname']) : 'Patient';

$specialization = $_GET['specialization'] ?? '';
if ($specialization) {
    $query = $pdo->prepare("SELECT * FROM users WHERE role = 'doctor' AND specialization LIKE ?");
    $query->execute(["%$specialization%"]);
} else {
    $query = $pdo->prepare("SELECT * FROM users WHERE role = 'doctor'");
    $query->execute();
}
$doctors = $query->fetchAll();
?>
