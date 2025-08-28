<?php
session_name("patient_session");
session_start();
require 'db.php';

// ✅ Make sure user is logged in and is a patient
if (!isset($_SESSION['patient_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.html");
    exit();
}

$userId = $_SESSION['patient_id'];

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
  <title>Patient Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpg" href="patient\E.jpg">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(ellipse at top left, #050505, #0c0c1e, #1a1a2e);
      color: #d1d1d1;
      overflow-x: hidden;
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
      padding: 40px 20px;
      height: 100vh;
      border-radius: 0 20px 20px 0;
    }

    .sidebar h4 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
      color: #0fffd7;
    }

    .sidebar a {
      color: white;
      padding: 12px 20px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      margin-bottom: 12px;
      transition: all 0.3s ease;
      font-size: 1rem;
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
      padding: 60px 40px;
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

    .top-bar img {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      object-fit: cover;
    }

    .top-bar span {
      margin: 0 20px;
      font-weight: 600;
      font-size: 1.1rem;
    }

    .dashboard-heading h1 {
      color: #0fffd7;
      text-shadow: 0 0 6px #0fffd7, 0 0 12px rgba(0, 255, 204, 0.4);
    }

    .dashboard-heading p {
      color: #b0b0b0;
      font-weight: 300;
    }

    .card {
      background: linear-gradient(145deg, rgba(20, 20, 40, 0.8), rgba(10, 10, 25, 0.8));
      box-shadow: 0 12px 36px rgba(0,0,0,0.75);
      backdrop-filter: blur(15px);
      color: #e6f1ff;
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 18px;
      transition: transform 0.3s ease, box-shadow 0.4s ease;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 50px rgba(0, 255, 255, 0.15);
    }

    .card .card-body {
      padding: 32px;
    }

    .card h5 {
      font-weight: 600;
    }

    .btn-primary, .btn-success, .btn-warning, .btn-danger, .btn-info {
      background-color: rgba(0, 255, 204, 0.1);
      border: 1px solid #0fffd7;
      color: #0fffd7;
      box-shadow: 0 0 10px rgba(0, 255, 204, 0.2);
      transition: all 0.3s ease;
    }

    .btn:hover {
      background-color: #0fffd7;
      color: #000;
      transform: scale(1.08);
      box-shadow: 0 0 20px #0fffd7;
    }

    .btn-lg {
      font-size: 1.2rem;
      padding: 14px 32px;
      border-radius: 32px;
    }

    .mt-10 {
      margin-top: 100px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h4>✨ Patient Portal ✨</h4>
    <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="Appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a>
    <a href="prescriptions.php"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
          <a href="diagnosis.php" ><i class="fas fa-stethoscope"></i> Diagnosis</a>
    <a href="doctor.php"><i class="fas fa-user-md"></i> Doctor Info</a>
  </div>

  <div class="content">
    <div class="top-bar">
      <h5 class="mb-0 text-light">Patient Dashboard</h5> 
      <button id="notifyBtn" class="btn btn-sm btn-outline-info position-relative">
        <i class="fas fa-bell"></i>
        <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">!</span>
      </button>
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

    <div class="dashboard-heading mt-5">
      <h1>Welcome, <?php echo $patientName; ?>!</h1>
      <p>Your galaxy of health insights awaits ✨</p>
    </div>

    <div class="row mt-5 g-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5>Appointments</h5>
            <p></p>
            <a href="Appointment.php" class="btn btn-primary">Make An Appointment</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5>Prescriptions</h5>
            <a href="prescriptions.php" class="btn btn-success">View Prescriptions</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5>Diagnosis</h5>
            <a href="diagnosis.php" class="btn btn-warning">View Results</a>
          </div>
        </div>
      </div>

      

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5>Doctor Info</h5>
            <p></p>
            <a href="doctor.php" class="btn btn-info">Contact Doctor</a>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-10">
       <a href="Appointment.php" class="btn btn-primary btn-lg" >🚀 Book New Appointment</a>
    </div>
  </div>
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const badge = document.getElementById("notifBadge");
    const notifyBtn = document.getElementById("notifyBtn");

    // Check notifications on page load and every 15s
    function checkNotifications() {
      fetch('get_patient_notifications.php')
        .then(res => res.json())
        .then(data => {
          if (data.prescriptions.length > 0) {
            badge.style.display = 'inline-block';
            badge.innerText = data.prescriptions.length;
          } else {
            badge.style.display = 'none';
          }
        });
    }

    // Handle bell click
    notifyBtn.addEventListener("click", () => {
      fetch('get_patient_notifications.php')
        .then(res => res.json())
        .then(data => {
          if (data.prescriptions.length > 0) {
            let messages = `📦 ${data.prescriptions.length} new prescription(s)`;

            // Show popup
            Swal.fire({
              title: 'New Medical Updates',
              html: messages,
              icon: 'info',
              confirmButtonText: 'View'
            }).then(result => {
              if (result.isConfirmed) {
                window.location.href = 'prescriptions.php';
              }
            });

            // Mark them as viewed
            fetch('mark_notifications_viewed.php', {
              method: 'POST'
            }).then(() => {
              badge.style.display = 'none'; // remove badge immediately
            });
          } else {
            Swal.fire('No new updates!', '', 'info');
          }
        });
    });

    // Start checking
    checkNotifications();
    setInterval(checkNotifications, 15000);
  });
</script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
