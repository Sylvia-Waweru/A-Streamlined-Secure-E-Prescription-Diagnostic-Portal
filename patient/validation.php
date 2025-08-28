<?php
// validation.php
header("Content-Type: application/json");

// Log or validate incoming payment
file_put_contents('validation_log.txt', file_get_contents('php://input'), FILE_APPEND);

// Accept the transaction
echo json_encode(["ResultCode" => 0, "ResultDesc" => "Accepted"]);
?>
