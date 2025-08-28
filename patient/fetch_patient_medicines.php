<?php
require 'db.php';

$patientName = $_GET['name'] ?? '';

if ($patientName !== '') {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE patient_name = ?");
    $stmt->execute([$patientName]);
    $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($medicines);
} else {
    echo json_encode([]);
}
?>
