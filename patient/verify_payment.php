<?php
session_start();
require '../db.php'; // adjust path if needed

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointment_id = $_POST['appointment_id'];
    $amount = $_POST['amount'];
    $mpesa_code = strtoupper(trim($_POST['mpesa_code']));

    // Basic validation
    if (empty($appointment_id) || empty($amount) || empty($mpesa_code)) {
        die("Please fill in all fields.");
    }

    // OPTIONAL: Check if the same MPESA code was already used (to prevent duplicates)
    $check = $pdo->prepare("SELECT * FROM payments WHERE mpesa_code = ?");
    $check->execute([$mpesa_code]);

    if ($check->rowCount() > 0) {
        die("This M-Pesa code has already been used.");
    }

    // Save payment in DB
    $stmt = $pdo->prepare("INSERT INTO payments (appointment_id, amount, mpesa_code, payment_date) VALUES (?, ?, ?, NOW())");

    if ($stmt->execute([$appointment_id, $amount, $mpesa_code])) {
        echo "<h3>Payment successful!</h3>";
        echo "<a href='appointment.php'>Back to Appointments</a>";
    } else {
        echo "Something went wrong. Please try again.";
    }
} else {
    header("Location: payment_form.php"); // redirect back if accessed directly
    exit();
}
?>
