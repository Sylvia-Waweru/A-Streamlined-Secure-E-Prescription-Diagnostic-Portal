<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit("Unauthorized");
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(403);
    exit("No doctor found");
}

$doctorName = $user['fullname'];

$stmt = $pdo->prepare("UPDATE appointments SET is_read = 1 WHERE doctor_name = ?");
$stmt->execute([$doctorName]);

echo "Marked as read";
