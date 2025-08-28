<?php
session_name("patient_session");

session_start();
require 'db.php';

if (!isset($_SESSION['patient_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.html");
    exit();
}

$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$user = $stmt->fetch();
$patientName = $user ? htmlspecialchars($user['fullname']) : 'Patient';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Diagnosis - Patient Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/jpg" href="patient/E.jpg">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(ellipse at top left, #050505, #0c0c1e, #1a1a2e);
      color: #d1d1d1;
      overflow-x: hidden;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .sidebar {
      background: rgba(10, 10, 25, 0.85);
      backdrop-filter: blur(25px);
      border-right: 1px solid rgba(255,255,255,0.04);
      box-shadow: 4px 0 40px rgba(0,0,0,0.8);
      color: white;
      width: 260px;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      padding: 40px 20px;
      border-radius: 0 20px 20px 0;
    }
    .sidebar h4 {
      text-align: center;
      margin-bottom: 30px;
      color: #0fffd7;
      font-weight: 700;
    }
    .sidebar a {
      color: white;
      padding: 12px 20px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      margin-bottom: 12px;
      transition: all 0.3s ease;
      text-decoration: none;
    }
    .sidebar a i {
      margin-right: 12px;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255, 255, 255, 0.15);
      transform: translateX(6px);
    }
    .content {
      margin-left: 280px;
      padding: 60px 40px 80px;
      flex: 1;
    }
    .top-bar {
      background: rgba(20, 20, 40, 0.8);
      box-shadow: 0 0 40px rgba(0,0,0,0.6);
      border-radius: 16px;
      padding: 25px 35px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .card {
      background: linear-gradient(145deg, rgba(20, 20, 40, 0.8), rgba(10, 10, 25, 0.8));
      box-shadow: 0 12px 36px rgba(0,0,0,0.75);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 18px;
      color: #e6f1ff;
    }
    .table {
      color: #e6f1ff;
    }
    footer {
      background: #101820;
      color: #ccc;
      text-align: center;
      padding: 20px;
    }
  </style>
</head>
<body>
<div class="sidebar">
  <h4>✨ Patient Portal ✨</h4>
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="Appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a>
    <a href="prescriptions.php"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
  <a href="diagnosis.php" class="active"><i class="fas fa-stethoscope"></i> Diagnosis</a>
  <a href="doctor.php"><i class="fas fa-user-md"></i> Doctor Info</a>
</div>
<div class="content">
  <div class="top-bar">
    <h5 class="mb-0 text-light">Diagnoses</h5>
    <div class="d-flex align-items-center gap-3">
     
      <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="doctorDropdown" data-bs-toggle="dropdown">
          <span><?php echo $patientName; ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="doctorDropdown">
          <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
          <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-in-alt me-2"></i> Login</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="card my-5">
    <div class="card-header bg-success text-white">Search Diagnosis</div>
    <div class="card-body">
      <form id="searchForm">
        <div class="mb-3">
          <label for="patientName" class="form-label">Enter Your Name</label>
          <input type="text" id="patientName" name="patient_name" class="form-control" placeholder="Search by patient name" required>
        </div>
        <button type="submit" class="btn btn-success">Search</button>
      </form>
    </div>
  </div>

  <div class="diagnostics-list">
    <h2 class="text-info">Diagnostic Reports</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Patient Name</th>
          <th>Diagnosis</th>
          <th>Symptoms</th>
          <th>Recommendation</th>
        </tr>
      </thead>
      <tbody id="diagnosticTable"></tbody>
    </table>
  </div>
</div>
<footer>
  &copy; <?= date('Y') ?> E-Prescription Portal. All rights reserved.
</footer>
<script>
const patientName = "<?= addslashes($patientName) ?>";

document.getElementById('searchForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const inputName = document.getElementById('patientName').value;
  fetch('fetch_diagnosis.php?patient_name=' + encodeURIComponent(inputName))
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById('diagnosticTable');
      tbody.innerHTML = '';
      if (data.length > 0) {
        data.forEach(d => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${d.patient_name}</td>
            <td>${d.diagnosis}</td>
            <td>${d.symptoms}</td>
            <td>${d.recommendation}</td>
          `;
          tbody.appendChild(row);
        });
      } else {
        Swal.fire({ icon: 'info', title: 'No Records Found', text: 'No diagnosis found.' });
      }
    })
    .catch(err => {
      console.error('Error:', err);
      Swal.fire({ icon: 'error', title: 'Fetch Error', text: 'Could not fetch data.' });
    });
});



</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
