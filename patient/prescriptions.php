<?php
session_name("patient_session");
session_start();
require 'db.php';

if (!isset($_SESSION['patient_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.html");
    exit();
}
$timeout = 900;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    // Session expired
    session_unset();
    session_destroy();
    header("Location: login.html?timeout=1");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // update last activity
// Fetch patient full name
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$user = $stmt->fetch();
$patientName = $user ? htmlspecialchars($user['fullname']) : 'Patient';

// Fetch prescriptions for this patient
$prescriptionStmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_name = ? ORDER BY date_prescribed DESC");
$prescriptionStmt->execute([$patientName]);
$prescriptions = $prescriptionStmt->fetchAll(PDO::FETCH_ASSOC);
$ageStmt = $pdo->prepare("SELECT age FROM appointments WHERE patient_name = ? ORDER BY appointment_date DESC");
$ageStmt->execute([$patientName]);
$ageResult = $ageStmt->fetch();
$patientAge = $ageResult ? $ageResult['age'] : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prescriptions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
      <link rel="icon" type="image/jpg" href="patient\E.jpg">

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: radial-gradient(ellipse at top left, #050505, #0c0c1e, #1a1a2e);
      color:rgb(3, 90, 112);
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

    .btn-teal {
      background-color: #008080;
      color: white;
      border: none;
    }

    .btn-teal:hover {
      background-color: #006666;
      color: white;
    }

    .prescription-print {
      background: navy blue;
      color: white;
      font-family: 'Times New Roman', Times, serif;
      border: 1px solid #ccc;
      padding: 30px;
      max-width: 700px;
      margin: auto;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    .prescription-print h3 {
      color: teal;
    }

    .prescription-print ul {
      list-style-type: disc;
      padding-left: 20px;
    }

    .prescription-print img {
      height: 50px;
    }

    @media print {
      body * {
        visibility: hidden;
      }
      .prescription-print, .prescription-print * {
        visibility: visible;
      }
      .prescription-print {
        position: absolute;
        left: 0;
        top: 0;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h4>✨ Patient Portal ✨</h4>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a>
    <a href="prescriptions.php" class="active"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
      <a href="diagnosis.php" ><i class="fas fa-stethoscope"></i> Diagnosis</a>
    <a href="doctor.php"><i class="fas fa-user-md"></i> Doctor Info</a>
  </div>

  <div class="content">
    <div class="top-bar">
      <h5 class="mb-0 text-light">Your Prescriptions</h5>
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

    <div class="mt-5" >
      <?php if ($prescriptions): ?>
        <?php foreach ($prescriptions as $prescription): ?>
          <div class="prescription-print mb-5" id="printable-prescription-<?= $prescription['id'] ?>">
            <div class="text-center mb-4">
              <h3 class="fw-bold">Dr. <?= htmlspecialchars($prescription['prescribed_by']) ?>, MD</h3>
              <p class="mb-0">License No: DOC123456789</p>
              <p class="mb-0">City Health Hospital</p>
            </div>

            <div class="mb-3">
  <strong>Patient Name:</strong> <?= htmlspecialchars($prescription['patient_name']) ?><br>
<strong>Age:</strong> <?= htmlspecialchars($patientAge) ?><br>
  <strong>Prescribed By:</strong> <?= htmlspecialchars($prescription['prescribed_by']) ?><br>
  <strong>Date Prescribed:</strong> <?= htmlspecialchars($prescription['date_prescribed']) ?><br>
  <strong>Created At:</strong> <?= htmlspecialchars($prescription['created_at']) ?><br>
</div>

<div class="border p-3" style="border-style: dotted;">
  <h5 class="fw-bold">Rx:</h5>
  <ul class="mb-2">
    <li><strong>Medicine Name:</strong> <?= htmlspecialchars($prescription['medicine_name']) ?></li>
    <li><strong>Dosage:</strong> <?= htmlspecialchars($prescription['dosage']) ?></li>
    <li><strong>Frequency:</strong> <?= htmlspecialchars($prescription['frequency']) ?></li>
    <li><strong>Duration:</strong> <?= htmlspecialchars($prescription['duration']) ?></li>
    <li><strong>Prescription Notes:</strong> <?= htmlspecialchars($prescription['prescription']) ?: 'N/A' ?></li>
    <li><strong>Doctor Notes:</strong> <?= htmlspecialchars($prescription['notes']) ?: 'N/A' ?></li>
  </ul>
</div>


            <div class="mt-4 d-flex justify-content-end">
              <div class="text-end">
                
              </div>
            </div>

            <div class="text-center mt-3">
              <button class="btn btn-teal" onclick="printPrescription('printable-prescription-<?= $prescription['id'] ?>')">
                <i class="fas fa-print me-2"></i> Print Prescription
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-warning">You have no prescriptions yet.</div>
      <?php endif; ?>
    </div>
  </div>

 <script>
function printPrescription(id) {
  const content = document.getElementById(id).outerHTML;

  const style = `
    <style>
      body {
        font-family: 'Times New Roman', Times, serif;
        padding: 40px;
        background-color: #0d2b2d;
        color: #e0ffff;
      }
      .prescription-print {
        background: #0d2b2d;
        color: #e0ffff;
        padding: 30px;
        max-width: 700px;
        margin: auto;
        border: 1px solid #008080;
        box-shadow: 0 0 15px rgba(0,255,255,0.3);
      }
      .prescription-print h3 {
        color: #00ffff;
      }
      .prescription-print ul {
        list-style-type: disc;
        padding-left: 20px;
      }
    </style>
  `;

  const printWindow = window.open('', '', 'width=800,height=600');
  printWindow.document.write(`<html><head><title>Print Prescription</title>${style}</head><body>${content}</body></html>`);
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
  printWindow.close();
}
</script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
