<?php
session_name("patient_session");
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$patient_id = $_SESSION['patient_id'];
$patient_name = trim($_POST['patientName'] ?? '');
$doctor_name = trim($_POST['doctorName'] ?? '');
$appointment_date = trim($_POST['appointmentDate'] ?? '');
$appointment_time = trim($_POST['appointmentTime'] ?? '');
$reason = trim($_POST['reason'] ?? '');
$status = 'Pending Payment';
$payment_status = 'pending';
$date_of_birth = trim($_POST['date_of_birth'] ?? '');
$age = trim($_POST['age'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$gender = trim($_POST['gender'] ?? '');

// Validate input
if (
    empty($patient_name) || empty($doctor_name) || empty($appointment_date) || empty($appointment_time) ||
    empty($reason) || empty($date_of_birth) || empty($age) || empty($email) || empty($phone) || empty($address) || empty($gender)
) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}



try {
    // Prevent double-booking
    $check = $pdo->prepare("SELECT id FROM appointments WHERE doctor_name = ? AND appointment_date = ? AND time_slot = ?");
    $check->execute([$doctor_name, $appointment_date, $appointment_time]);

    if ($check->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This time slot is already booked for the doctor.']);
        exit;
    }

    // Insert with payment pending
    $stmt = $pdo->prepare("INSERT INTO appointments 
    (patient_name, doctor_name, appointment_date, time_slot, reason, status, payment_status, date_of_birth, age, email, phone, address,gender)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->execute([$patient_name, $doctor_name, $appointment_date, $appointment_time, $reason, $status, $payment_status, $date_of_birth, $age, $email,$phone,$address,$gender]);

    $appointmentId = $pdo->lastInsertId();
    echo json_encode([
        'status' => 'success',
        'appointment_id' => $appointmentId,
        'redirect_url' => "pay.php?appointment_id=$appointmentId"
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
