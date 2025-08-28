<?php
session_name("doctor_session");
session_start();
require 'db.php';

// Confirm session is valid and doctor is logged in
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

// Count total patients
$totalPatientsStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_name = ?");
$totalPatientsStmt->execute([$user['fullname']]);
$totalPatients = $totalPatientsStmt->fetchColumn();
// Count total diagnoses
$totalDiagnoses = $pdo->query("SELECT COUNT(*) FROM diagnoses")->fetchColumn();

// Count appointments booked with this doctor
$appointmentsStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_name = ?");
$appointmentsStmt->execute([$user['fullname']]);
$totalAppointments = $appointmentsStmt->fetchColumn();

// Count total prescriptions
$totalPrescriptions = $pdo->query("SELECT COUNT(*) FROM prescriptions")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Rubik:wght@400;500&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="patient/E.webp">
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
    .top-bar {
      background: #00332f;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #e0f2f1;
      border-bottom: 1px solid #004d40;
    }
    .card-stats {
      background: linear-gradient(145deg, #1f2933, #111827);
      color: #e0e0e0;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,255,230,0.05);
      transition: all 0.3s ease;
      padding: 25px;
    }
    .card-stats:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,255,230,0.15);
    }
    .card-stats h6 {
      margin-top: 10px;
      font-size: 1rem;
      color: #90caf9;
    }
    .card-stats h4 {
      font-family: 'Orbitron', sans-serif;
      font-weight: 700;
    }
    .badge {
      background: red;
      color: white;
      padding: 2px 5px;
      border-radius: 50%;
      font-size: 0.8rem;
    }
  </style>
</head>
<body>
<div class="d-flex">
  <div class="sidebar p-3 text-white">
    <h4 class="text-center">Doctor's Portal</h4>
    <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="appointments.php"><i class="fas fa-calendar-alt"></i> Future Appointments</a>
        <a href="diagnosis.php" ><i class="fas fa-notes-medical"></i> Diagnosis</a>

    <a href="Patient History Page.php"><i class="fas fa-prescription-bottle-alt"></i> Patient History</a>
  </div>

  <div class="flex-grow-1">
    <div class="top-bar">
      <h5 class="mb-0">Dashboard</h5>
      <!-- Notification Bell -->
<!-- Bell Button -->
<button id="notifBell" class="btn btn-outline-light position-relative">
  🔔
  <span id="notifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">
    0
  </span>
</button>



      <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="doctorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <span><?= htmlspecialchars($user['fullname']) ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="doctorDropdown">
          <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
          <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-in-alt me-2"></i>Login</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a></li>
        </ul>
      </div>
    </div>

    <div class="content p-4">
      <div class="row g-4">
        <div class="col-md-3">
          <div class="card card-stats text-center">
            <i class="fas fa-users fa-2x text-info mb-2"></i>
            <h6>Patients</h6>
            <h4><?= $totalPatients ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <a href="appointments.php" class="text-decoration-none text-white">
            <div class="card card-stats text-center">
              <i class="fas fa-calendar-check fa-2x text-warning mb-2"></i>
              <h6>Appointments</h6>
              <h4><?= $totalAppointments ?></h4>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const notifBell = document.getElementById("notifBell");
  const notifCountBadge = document.getElementById("notifCount");

  // Update the notification badge
  function updateBadge() {
    fetch('get_unread_appointments_count.php')
      .then(res => res.json())
      .then(data => {
        if (data.count > 0) {
          notifCountBadge.textContent = data.count;
          notifCountBadge.style.display = 'inline-block';
        } else {
          notifCountBadge.style.display = 'none';
        }
      });
  }

  // Show the alert with plain text names (no links)
  function handleBellClick() {
    fetch('get_unread_appointments.php')
      .then(res => res.json())
      .then(data => {
        if (data.length > 0) {
          // Display patient names as plain text (no <a>, no <br> as HTML)
          let list = data.map(n => 
            `${n.patient_name} (${n.appointment_date} @ ${n.time_slot})`
          ).join('\n'); // <- changed to plain text

          Swal.fire({
            title: `🔔 ${data.length} new appointment(s)`,
            text: list, // <- changed from html: to text:
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Go to Appointments',
            cancelButtonText: 'Later'
          }).then(result => {
            if (result.isConfirmed) {
              window.location.href = 'appointments.php';
            }
          });

          // Mark appointments as viewed after showing
          fetch('mark_appointments_viewed.php', { method: 'POST' });
        } else {
          Swal.fire({
            title: 'No new appointments',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
          });
        }

        notifCountBadge.style.display = 'none'; // Hide badge after check
      });
  }

  // Initial badge check
  updateBadge();

  // Periodic refresh every 15s
  setInterval(updateBadge, 15000);

  // Attach bell click listener
  if (notifBell) {
    notifBell.addEventListener("click", handleBellClick);
  }
});
</script>




</body>
</html>
