<?php
session_name("doctor_session");
session_start();
require 'db.php';

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

$userId = $_SESSION['doctor_id'];
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
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
  <title>Diagnosis - Doctor's Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&family=Rubik&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="patient\E.webp">

  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
      color: #e0f2f1;
    }
    .sidebar {
      min-height: 100vh;
      background: linear-gradient(to bottom, #00695c, #004d40);
      color: white;
    }
    .sidebar h4 {
      font-family: 'Orbitron', sans-serif;
    }
    .sidebar a {
      color: #b2dfdb;
      text-decoration: none;
      padding: 12px 20px;
      display: block;
      transition: background 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255, 255, 255, 0.15);
      border-left: 4px solid #00ffe7;
    }
    .top-bar {
      background-color: #004d40;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .form-section {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(10px);
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,255,230,0.1);
      margin-bottom: 40px;
    }
    .form-control, textarea.form-control {
      background-color: #1a1a1a;
      border: 1px solid #555;
      color: #fff;
      border-radius: 10px;
    }
    .form-control:focus {
      border-color: #00ffe7;
      box-shadow: 0 0 10px rgba(0, 255, 230, 0.3);
    }
    .btn-primary {
      background: linear-gradient(135deg, #00ffe7, #00bfa5);
      border: none;
      color: #000;
      font-weight: bold;
      border-radius: 30px;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #00e6d2, #009688);
    }
    .table {
      background: rgba(0, 0, 0, 0.2);
      color: #e0f2f1;
      border-radius: 10px;
    }
    .table th, .table td {
      vertical-align: middle;
    }
  </style>
</head>
<body>
<div class="d-flex">
  <div class="sidebar p-3">
    <h4 class="text-white text-center">Doctor's Portal</h4>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="diagnosis.php" class="active"><i class="fas fa-notes-medical"></i> Diagnosis</a>
    <a href="appointments.php"><i class="fas fa-calendar-alt"></i> Future Appointments</a>
        <a href="Patient History Page.php"><i class="fas fa-prescription-bottle-alt"></i> Patient History</a>

  </div>
  <div class="flex-grow-1">
    <div class="top-bar text-white">
      <h5 class="mb-0">Diagnosis</h5>
      <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="doctorDropdown" data-bs-toggle="dropdown">
                    <span><?= htmlspecialchars($user['fullname']) ?></span>

        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
          <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-in-alt me-2"></i>Login</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a></li>
        </ul>
      </div>
    </div>
    <div class="content p-4">
      <div class="form-section">
        <h5 class="mb-3">Add New Diagnosis</h5>
        <form id="diagnosisForm">
          <div class="mb-3">
            <label for="patientName" class="form-label">Patient Name</label>
<input type="text" class="form-control" name="patientName" id="patientName" required>

          <div class="mb-3">
            <label for="diagnosis" class="form-label">Diagnosis Details</label>
            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="symptoms" class="form-label">Symptoms</label>
            <textarea class="form-control" id="symptoms" name="symptoms" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="recommendation" class="form-label">Recommendation</label>
            <textarea class="form-control" id="recommendation" name="recommendation" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Save Diagnosis</button>
        </form>
      </div>
      <h5>Recent Diagnoses</h5>
      <div class="table-responsive">
        <table class="table table-bordered mt-3" id="diagnosisTable">
          <thead class="table-dark">
            <tr>
              <th>Patient Name</th>
              <th>Diagnosis</th>
              <th>Symptoms</th>
              <th>Recommendation</th>
            </tr>
          </thead>
          <tbody>
            <!-- Diagnoses will be added here dynamically -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
  document.getElementById('diagnosisForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('add_diagnosis.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Diagnosis Added',
          text: 'The diagnosis has been successfully added!',
          confirmButtonText: 'OK'
        });
        const table = document.getElementById('diagnosisTable').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        newRow.innerHTML = `
<td>${formData.get('patientName')}</td>
          <td>${formData.get('diagnosis')}</td>
          <td>${formData.get('symptoms')}</td>
          <td>${formData.get('recommendation')}</td>
        `;
        document.getElementById('diagnosisForm').reset();
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to add diagnosis. Please try again.' });
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
