<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient = $_POST['patientName'] ?? '';
    $doctor = $_POST['doctorName'] ?? '';
    $date = $_POST['appointmentDate'] ?? '';
    $time = $_POST['appointmentTime'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $status = 'Scheduled';
    $payment_status = 'Unpaid'; // or whatever logic you're using

    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO appointments 
        (patient_name, doctor_name, appointment_date, time_slot, reason, status, payment_status, date_of_birth, age, email, phone, address, gender)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt->execute([$patient, $doctor, $date, $time, $reason, $status, $payment_status, $date_of_birth, $age, $email, $phone, $address, $gender])) {
        
        // ✅ Insert notification for the doctor
        $notif_stmt = $pdo->prepare("INSERT INTO notifications (recipient_type, recipient_name, message) VALUES (?, ?, ?)");
        $message = "New appointment with $patient on $date at $time.";
        $notif_stmt->execute(['doctor', $doctor, $message]);

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>
