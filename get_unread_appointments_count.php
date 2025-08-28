<?php
session_name("doctor_session");
session_start();
require 'db.php';

if (!isset($_SESSION['doctor_id'])) {
    echo json_encode(['count' => 0]);
    exit();
}

$userId = $_SESSION['doctor_id'];
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['count' => 0]);
    exit();
}

$doctorName = $user['fullname'];
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_name = ? AND viewed = 0"); 
$stmt->execute([$doctorName]);
$count = $stmt->fetchColumn();

echo json_encode(['count' => $count]);
?>