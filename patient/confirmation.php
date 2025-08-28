<?php
// confirmation.php
header("Content-Type: application/json");

$mpesaResponse = file_get_contents('php://input');
$data = json_decode($mpesaResponse, true);

// Log to file or save to DB
file_put_contents('confirmation_log.txt', $mpesaResponse, FILE_APPEND);

// Respond to Safaricom
echo json_encode(["ResultCode" => 0, "ResultDesc" => "Confirmation received successfully"]);
?>
