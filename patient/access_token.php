<?php
// access_token.php
$consumerKey = "xnZ0ceAgXvAvg2aY0G4FOg9GhRSuZHb0nuu0ZizBHQAeySIl"; // from you
$consumerSecret = "d5Dk2v2VPHcY4r2JrDs5lo0oOMYfWKgZanDEr7w33kVlrS8bUCWbkShNdPMkgOcJ"; // from you

$credentials = base64_encode($consumerKey . ':' . $consumerSecret);

$url = 'https://ngrok.com';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response);
$access_token = $result->access_token;
