<?php
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id'])) {
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medicines - Patient Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <link rel="icon" type="image/jpg" href="patient\E.jpg">

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
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

    .sidebar a:hover,
    .sidebar a.active {
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
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
    }

    .top-bar span {
      margin: 0 15px;
      font-size: 1.1rem;
    }

    .medicines-list {
      margin-top: 30px;
    }

    .card {
      background: linear-gradient(145deg, rgba(20, 20, 40, 0.8), rgba(10, 10, 25, 0.8));
      box-shadow: 0 12px 36px rgba(0,0,0,0.75);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 18px;
      color: #e6f1ff;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h4>✨ Patient Portal ✨</h4>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a>
    <a href="medicines.php" class="active"><i class="fas fa-capsules"></i> Medicines</a>
    <a href="diagnosis.php"><i class="fas fa-stethoscope"></i> Diagnosis</a>
    <a href="prescriptions.php"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
    <a href="doctor.php"><i class="fas fa-user-md"></i> Doctor Info</a>
  </div>

  <div class="content">
    <div class="top-bar">
      <h5 class="mb-0 text-light">Medicines</h5>
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

    <div class="my-5">
      <h4 class="text-light">Search Medicines by Patient Name</h4>
      <div class="input-group mb-3">
        <input type="text" id="patientName" class="form-control" placeholder="Enter your full name">
        <button class="btn btn-success" id="searchBtn">Search</button>
      </div>
    </div>

    <div class="medicines-list" id="medicinesList">
      <!-- Results will appear here -->
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.getElementById('searchBtn').addEventListener('click', function () {
      const patientName = document.getElementById('patientName').value.trim();
      if (patientName === '') {
        Swal.fire('Error', 'Please enter your name to search.', 'warning');
        return;
      }

      fetch('fetch_patient_medicines.php?name=' + encodeURIComponent(patientName))
        .then(response => response.json())
        .then(data => {
          const medicinesList = document.getElementById('medicinesList');
          medicinesList.innerHTML = '';

          if (data.length === 0) {
            medicinesList.innerHTML = '<div class="alert alert-info">No medicines found for this patient.</div>';
          } else {
            data.forEach(medicine => {
              const card = `
                <div class="card mb-3 p-4">
                  <h5 class="card-title">${medicine.name}</h5>
                  <p class="card-text">Type: ${medicine.type}</p>
                  <p class="card-text">Description: ${medicine.description}</p>
                  <p class="card-text">Side Effects: ${medicine.side_effects}</p>
                </div>
              `;
              medicinesList.innerHTML += card;
            });
          }
        })
        .catch(error => {
          console.error('Error fetching medicines:', error);
          Swal.fire('Error', 'Failed to fetch medicines.', 'error');
        });
    });
  </script>

</body>

</html>
