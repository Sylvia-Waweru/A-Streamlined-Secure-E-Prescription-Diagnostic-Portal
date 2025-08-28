<?php
require 'db.php';
header('Content-Type: application/json');

if (isset($_GET['patient_name'])) {
    $patient_name = $_GET['patient_name'];

    $stmt = $pdo->prepare("SELECT * FROM diagnoses WHERE patient_name LIKE ?");
    $stmt->execute(["%$patient_name%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} else {
    echo json_encode([]);
}
?>
