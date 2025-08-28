<?php
session_name("doctor_session");

session_start();
require 'db.php';
if (isset($_GET['appointment_id'])) {
    $appointmentId = $_GET['appointment_id'];

    // Mark the appointment as viewed
    $stmt = $pdo->prepare("UPDATE appointments SET viewed = 1 WHERE id = ?");
    $stmt->execute([$appointmentId]);
}
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.html");
    exit();
}

$patientId = $_SESSION['doctor_id'];
if (!isset($_GET['appointment_id'])) {
    die("No appointment selected.");
}

$appointment_id = $_GET['appointment_id'];

$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch();

if (!$appointment) {
    die("Appointment not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Diagnosis & Prescription</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Rubik', sans-serif;
            background: linear-gradient(to right top, #0f2027, #203a43, #2c5364);
            color: #e0e0e0;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #004d40, #001f1c);
            padding-top: 30px;
        }

        .sidebar h4 {
            font-weight: 700;
            color: #ffffff;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: #b2dfdb;
            display: block;
            padding: 12px 20px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #00ffe7;
            color: #ffffff;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
        }

        .form-container {
            background: rgba(0, 0, 0, 0.3);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #00ffe7;
            margin-bottom: 25px;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #b2dfdb;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #00ffe7;
            background: rgba(255, 255, 255, 0.08);
            color: #e0e0e0;
            box-sizing: border-box;
        }

        input[readonly] {
            background-color: rgba(255, 255, 255, 0.05);
            color: #cccccc;
        }

        textarea {
            resize: vertical;
        }

        button {
            margin-top: 25px;
            padding: 12px;
            background-color: #00bfa5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #00796b;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Doctor's Portal</h4>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="appointments.php" class="active"><i class="fas fa-calendar-alt"></i> Future Appointments</a>
                <a href="diagnosis.php" ><i class="fas fa-notes-medical"></i> Diagnosis</a>
                        <a href="diagnosis.php" ><i class="fas fa-notes-medical"></i> Diagnosis</a>
        <a href="Patient History Page.php"><i class="fas fa-prescription-bottle-alt"></i> Patient History</a>

    </div>

    <div class="main-content">
        <div class="form-container">
            <h2>Diagnosis & Prescription</h2>
            <form method="POST" action="add_prescription.php"  id="prescriptionForm">
                <input type="hidden" name="patient_id" value="<?= htmlspecialchars($_SESSION['doctor_id']) ?>">

                <label>Patient Name</label>
                <input type="text" name="patient_name" value="<?= htmlspecialchars($appointment['patient_name']) ?>" readonly>

                <label>Date of Birth</label>
                <input type="text" value="<?= htmlspecialchars($appointment['date_of_birth']) ?>" readonly>

                <label>Age</label>
                <input type="text" value="<?= htmlspecialchars($appointment['age']) ?>" readonly>

                <label>Gender</label>
                <input type="text" value="<?= htmlspecialchars($appointment['gender']) ?>" readonly>

                <label>Contact Number</label>
                <input type="text" value="<?= htmlspecialchars($appointment['phone']) ?>" readonly>

                <label>Email Address</label>
                <input type="email" value="<?= htmlspecialchars($appointment['email']) ?>" readonly>

                <label>Address</label>
                <input type="text" value="<?= htmlspecialchars($appointment['address']) ?>" readonly>

                <label>Date of Appointment</label>
                <input type="text" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" readonly>

                <label>Time of Appointment</label>
                <input type="text" value="<?= htmlspecialchars($appointment['time_slot']) ?>" readonly>

                <label>Reason for Appointment</label>
                <input type="text" value="<?= htmlspecialchars($appointment['reason']) ?>" readonly>

                
 <div>
    <label for="medicine_name">Medicine Name:</label>
    <input type="text" id="medicine_name" name="medicine_name" required>
  </div>
                
 

  <div>
    <label for="dosage">Dosage:</label>
    <input type="text" id="dosage" name="dosage" required>
  </div>

  <div>
    <label for="frequency">Frequency:</label>
    <input type="text" id="frequency" name="frequency" required>
  </div>

  <div>
    <label for="duration">Duration:</label>
    <input type="text" id="duration" name="duration" required>
  </div>

  <label>Prescription Notes</label>
                <textarea name="prescription" rows="4" required></textarea>

  <div>
    <label for="prescribed_by">Prescribed By (Doctor):</label>
    <input type="text" id="prescribed_by" name="prescribed_by" required>
  </div>

  <button type="submit">Submit Prescription</button>
</form>

                
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('prescriptionForm').addEventListener('submit', function(e) {
    e.preventDefault(); // prevent default form submission

    const form = this;
    const formData = new FormData(form);

    fetch('add_prescription.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: 'Prescription added successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });

            // Optional: Reset form
            form.reset();
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to add prescription. Please try again.',
                icon: 'error'
            });
        }
    })
    .catch(() => {
        Swal.fire({
            title: 'Oops!',
            text: 'Something went wrong with the request.',
            icon: 'error'
        });
    });
});
</script>


</body>
</html>
