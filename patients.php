<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT fullname FROM appointments WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $user = ['fullname' => 'Unknown'];
}
$prefilledPatient = isset($_GET['patient']) ? htmlspecialchars($_GET['patient']) : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patients - Doctor's Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Rubik:wght@400;500&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="patient\E.webp">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(circle at top left, #121c2b, #0a0f18);
      color: #e0e0e0;
      margin: 0;
    }
    .sidebar {
      min-height: 100vh;
      background: linear-gradient(180deg, #004d40, #001f1c);
      padding-top: 30px;
    }
    .sidebar h4 {
      font-family: 'Orbitron', sans-serif;
      font-weight: 700;
    }
    .sidebar a {
      color: #b2dfdb;
      font-weight: 500;
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
    .sidebar i {
      width: 20px;
    }
    .top-bar {
      background: #00332f;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #e0f2f1;
      border-bottom: 1px solid #004d40;
    }
    .dropdown-toggle {
      color: #fff;
    }
    .dropdown-menu {
      background-color: #263238;
    }
    .dropdown-item {
      color: #e0e0e0;
    }
    .dropdown-item:hover {
      background-color: #37474f;
      color: #00ffe7;
    }
    .content {
      padding: 30px;
    }
    .form-section {
      background: linear-gradient(145deg, #1f2933, #111827);
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,255,230,0.05);
      padding: 30px;
    }
    .form-section h5 {
      font-family: 'Orbitron', sans-serif;
      color: #90caf9;
      margin-bottom: 20px;
    }
    .form-control, .btn {
      border-radius: 8px;
    }
    .btn-success {
      background-color: #00bfa5;
      border: none;
    }
    .btn-success:hover {
      background-color: #00ffe7;
      color: #00332f;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <div class="sidebar p-3 ">
      <h4 class="text-center text-white">Doctor's Portal</h4>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="diagnosis.php"><i class="fas fa-notes-medical"></i> Diagnosis</a>
      <a href="medicines.php"><i class="fas fa-capsules"></i> Medicines</a>
      <a href="patients.php" class="active"><i class="fas fa-users"></i> Patients</a>
      <a href="appointments.php"><i class="fas fa-calendar-alt"></i> Future Appointments</a>
      <a href="prescriptions.php"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
    </div>
    <div class="flex-grow-1">
      <div class="top-bar">
        <h5 class="mb-0">Patients</h5>
        <div class="dropdown">
          <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="doctorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span><?= htmlspecialchars($user['fullname']) ?></span>

          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="doctorDropdown">
            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
            <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-in-alt me-2"></i>Login</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a></li>
          </ul>
        </div>
      </div>
      <div class="content">
        <div class="form-section">
          <h5>Register New Patient</h5>
          <form id="patientForm">
            <input type="text" class="form-control" name="patientName" id="patientName" required>
            <div class="mb-3"><label for="dob" class="form-label">Date of Birth</label><input type="date" class="form-control" id="dob" name="dob" required></div>
            <div class="mb-3"><label for="age" class="form-label">Age</label><input type="number" class="form-control" id="age" name="age" readonly required></div>
            <div class="mb-3"><label for="gender" class="form-label">Gender</label><select class="form-control" id="gender" name="gender" required><option value="">Select gender</option><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>
            <div class="mb-3"><label for="phone" class="form-label">Contact Number</label><input type="text" class="form-control" id="phone" name="phone" required></div>
            <div class="mb-3"><label for="email" class="form-label">Email Address</label><input type="email" class="form-control" id="email" name="email" required></div>
            <div class="mb-3"><label for="address" class="form-label">Address</label><textarea class="form-control" id="address" name="address" rows="2" required></textarea></div>
            <div class="mb-3"><label for="medical_history" class="form-label">Medical History</label><textarea class="form-control" id="medical_history" name="medical_history" rows="2"></textarea></div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Save Patient</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const dobInput = document.getElementById('dob');
      const ageInput = document.getElementById('age');
      dobInput.addEventListener('change', function () {
        const dob = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
        ageInput.value = age >= 0 ? age : '';
      });
    });
    document.getElementById('patientForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      console.log('Patient Name:', formData.get('patientName'));

      fetch('add_patient.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Patient registration successful',
              text: 'The patient has been registered successfully!',
              confirmButtonText: 'OK'
            }).then(() => {
              document.getElementById('patientForm').reset();
              window.location.href = 'diagnosis.php';
            });
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: 'There was a problem registering the patient.' });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
        });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?php include 'footer.php'; ?>

</body>
</html>
