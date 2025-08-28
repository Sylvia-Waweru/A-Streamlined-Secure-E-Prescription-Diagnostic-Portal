<?php
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.html");
    exit();
}

$patientId = $_SESSION['patient_id'];
$stmt = $pdo->prepare("SELECT fullname, email FROM users WHERE id = ?");
$stmt->execute([$patientId]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

$names = explode(" ", $user['fullname']);
$initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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

    .profile-card {
      background: linear-gradient(145deg, rgba(20, 20, 40, 0.8), rgba(10, 10, 25, 0.8));
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 20px;
      padding: 40px 30px;
      max-width: 600px;
      margin: 60px auto;
      color: #e6f1ff;
      box-shadow: 0 12px 36px rgba(0,0,0,0.75);
      text-align: center;
    }

    .avatar-initials {
      background: #0fffd7;
      color: #000;
      width: 90px;
      height: 90px;
      font-size: 2rem;
      font-weight: 700;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      box-shadow: 0 0 20px rgba(0, 255, 204, 0.4);
    }

    .profile-card h3 {
      color: #0fffd7;
      text-shadow: 0 0 6px #0fffd7, 0 0 12px rgba(0, 255, 204, 0.2);
      margin-bottom: 10px;
    }

    .info-label {
      font-weight: 600;
      color: #aaa;
      margin-top: 20px;
    }

    .info-text {
      font-size: 1.1rem;
      color: #f1f1f1;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h4>✨ Patient Portal ✨</h4>
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="Appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a>
  <a href="medicines.php"><i class="fas fa-capsules"></i> Medicines</a>
  <a href="diagnosis.php"><i class="fas fa-stethoscope"></i> Diagnosis</a>
  <a href="prescriptions.php"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
  <a href="doctor.php"><i class="fas fa-user-md"></i> Doctor Info</a>
  <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
</div>

<div class="content">
  <div class="top-bar">
    <h5 class="mb-0 text-light">My Profile</h5>
    <div class="text-end">
        <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="doctorDropdown" data-bs-toggle="dropdown">
      <span>Welcome, <strong><?php echo htmlspecialchars($user['fullname']); ?></strong></span></a>
       <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="doctorDropdown">
          <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-in-alt me-2"></i> Login</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="profile-card">
    <div class="avatar-initials"><?php echo $initials; ?></div>
    <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
    <div class="info-label">Email Address</div>
    <div class="info-text"><?php echo htmlspecialchars($user['email']); ?></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
