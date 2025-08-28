<?php
session_start();
require 'db.php';

if (!isset($_GET['appointment_id']) || !is_numeric($_GET['appointment_id'])) {
    die('Invalid appointment ID.');
}

$appointmentId = $_GET['appointment_id'];

$stmt = $pdo->prepare("
    SELECT a.*, u.phone 
    FROM appointments a
    JOIN users u ON a.patient_name = u.fullname
    WHERE a.id = ?
");
$stmt->execute([$appointmentId]);
$appointment = $stmt->fetch();

if (!$appointment) {
    die('Appointment not found.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pay for Appointment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(ellipse at top left, #050505, #0c0c1e, #1a1a2e);
      color: #e0f1f1;
      padding: 60px 30px;
    }
    .container {
      max-width: 600px;
      background: rgba(20, 20, 40, 0.9);
      padding: 30px;
      border-radius: 16px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: 0 0 40px rgba(0,0,0,0.5);
    }
    h3 {
      color: #00ffe7;
      margin-bottom: 30px;
      text-align: center;
    }
    label {
      color: #ccc;
    }
    .btn-primary {
      background-color: #00ffe7;
      border: none;
      color: #000;
      font-weight: 600;
    }
    .btn-primary:hover {
      background-color: #00ccb9;
    }
  </style>
</head>
<body>
  <div class="container">
    <h3>Pay for Your Appointment</h3>
    <p><strong>Till Number: 6502529</strong></p>
    <p>To complete your payment:</p>
    <ol>
        <li>Go to M-Pesa on your phone.</li>
        <li>Select <strong>Lipa na M-Pesa</strong>.</li>
        <li>Choose <strong>Buy Goods and Services</strong>.</li>
        <li>Enter Till Number: <strong>6502529</strong>.</li>
        <li>Enter Amount: <strong>KES 2000</strong>.</li>
        <li>Enter your M-Pesa PIN and confirm.</li>
        <li>Once done, enter the M-Pesa Transaction Code below.</li>
    </ol>

    <form action="verify_payment.php" method="POST">
        <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']) ?>">

        <div class="mb-3">
            <label>Patient Name:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($appointment['patient_name']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label>Doctor:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($appointment['doctor_name']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label>Amount Paid (KES):</label>
            <input type="number" name="amount" class="form-control" value="10" required>
        </div>

        <div class="mb-3">
            <label>M-Pesa Transaction Code:</label>
            <input type="text" name="mpesa_code" class="form-control" placeholder="e.g. QJD4R5Y7J9" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Submit Payment Details</button>
    </form>
</div>

</body>
</html>
