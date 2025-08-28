<?php
session_name("doctor_session");
session_start();
require 'db.php';

if (!isset($_SESSION['doctor_id'])) {
    echo json_encode([]);
    exit();
}

// Get current doctor's full name
$userId = $_SESSION['doctor_id'];
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode([]);
    exit();
}

$doctorName = $user['fullname'];

// Fetch only unread appointments for this doctor
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE doctor_name = ? AND is_read = 0 ORDER BY created_at DESC");
$stmt->execute([$doctorName]);

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($notifications);
