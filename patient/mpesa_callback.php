<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents('php://input'), true);

$amount = $data['TransAmount'];
$mpesaCode = $data['TransID'];
$phone = $data['MSISDN'];
$name = $data['FirstName'] . ' ' . $data['LastName'];
$time = $data['TransTime'];

require 'db.php';
$stmt = $pdo->prepare("INSERT INTO payments (phone, mpesa_code, amount, customer_name, time, status) VALUES (?, ?, ?, ?, ?, 'Paid')");
$stmt->execute([$phone, $mpesaCode, $amount, $name, $time]);

echo json_encode(["ResultCode" => 0, "ResultDesc" => "Accepted"]);
?>
