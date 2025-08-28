<?php
$consumerKey = "xnZ0ceAgXvAvg2aY0G4FOg9GhRSuZHb0nuu0ZizBHQAeySIl"; // from you
$consumerSecret = "d5Dk2v2VPHcY4r2JrDs5lo0oOMYfWKgZanDEr7w33kVlrS8bUCWbkShNdPMkgOcJ"; // from you
$shortCode = '6502529';
$confirmationUrl = 'https://e-prescription.onrender.com/confirmation.php';
$validationUrl = 'https://e-prescription.onrender.com/validation.php';

// Get access token
$credentials = base64_encode("$consumerKey:$consumerSecret");
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$access_token = json_decode($response)->access_token;

// Register URLs
$registerUrl = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
$ch = curl_init($registerUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
]);

$data = [
    'ShortCode' => $shortCode,
    'ResponseType' => 'Completed',
    'ConfirmationURL' => $confirmationUrl,
    'ValidationURL' => $validationUrl
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
