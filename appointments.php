<?php
session_name("doctor_session");
session_start();
require 'db.php';

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.html"); // or access denied
    exit();
}


$userId = $_SESSION['doctor_id'];

// Get doctor name
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}
$doctorName = $user['fullname'];

// Fetch appointments
$appointments = [];
try {
    $stmt = $pdo->prepare("SELECT id, patient_name, doctor_name, appointment_date, time_slot, reason, date_of_birth, age, gender, email, phone, address,
                           viewed FROM appointments 
                           WHERE doctor_name = ?
                           ORDER BY appointment_date, time_slot ASC");
    $stmt->execute([$doctorName]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching appointments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Appointments - Doctor's Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="patient/E.webp">

  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: linear-gradient(to right top, #0f2027, #203a43, #2c5364);
      color: #e0e0e0;
    }
    .sidebar {
      min-height: 100vh;
      background: linear-gradient(180deg, #004d40, #001f1c);
      padding-top: 30px;
    }
    .sidebar h4 {
      font-weight: 700;
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
    }
    .table {
      color: #e0e0e0;
      background: rgba(0, 0, 0, 0.15);
      border-radius: 8px;
    }
    .table th {
      background-color: #002d2a;
      color: #00ffe7;
    }
    a.patient-link {
      color: #4dc3ff;
      text-decoration: underline;
    }
    a.patient-link:hover {
      color: #ff8888;
    }
    .patient-link.viewed {
  color: black;
  font-weight: normal;
}

.patient-link.unviewed {
  color: blue;
  font-weight: bold;
}


  </style>
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
      <h4 class="text-center text-white">Doctor's Portal</h4>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="appointments.php" class="active"><i class="fas fa-calendar-alt"></i> Future Appointments</a>
              <a href="diagnosis.php" ><i class="fas fa-notes-medical"></i> Diagnosis</a>
                  <a href="Patient History Page.php"><i class="fas fa-prescription-bottle-alt"></i> Patient History</a>

    </div>

    <!-- Main Content -->
    <div class="flex-grow-1">
      <div class="top-bar">
        <h5 class="mb-0">Future Appointments</h5>
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
        <h5 class="mb-4">Upcoming Appointments</h5>

        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Patient Name</th>
              <th>Doctor Name</th>
              <th>Date</th>
              <th>Time</th>
              <th>Date of Birth</th>
              <th>Age</th>
              <th>Gender</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Reason</th>
              <th>Delete</th>

            </tr>
          </thead>
          <tbody>
            <?php foreach ($appointments as $appt): ?>
              <tr>
               <td>
   <a 
  href="prescriptions.php?appointment_id=<?= $appt['id'] ?>" 
  class="patient-link <?= $appt['viewed'] ? 'viewed' : 'unviewed' ?>" 
  data-id="<?= $appt['id'] ?>"
  onclick="markViewed(event, <?= $appt['id'] ?>)">
  <?= htmlspecialchars($appt['patient_name']) ?>
</a>





                </td>
                <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                <td><?= htmlspecialchars($appt['time_slot']) ?></td>
                <td><?= htmlspecialchars($appt['date_of_birth']) ?></td>
                <td><?= htmlspecialchars($appt['age']) ?></td>
                <td><?= htmlspecialchars($appt['gender']) ?></td>
                <td><?= htmlspecialchars($appt['email']) ?></td>
                <td><?= htmlspecialchars($appt['phone']) ?></td>
                <td><?= htmlspecialchars($appt['address']) ?></td>
                <td><?= htmlspecialchars($appt['reason']) ?></td>
                <td>
  <button class="btn btn-sm btn-danger" onclick="deleteRow(this)">Delete</button>
</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?php include 'footer.php'; ?>
 <script>
function markViewed(event, appointmentId) {
  event.preventDefault();  // Prevent default link behavior

  const targetUrl = event.currentTarget.href;
  console.log("Clicked:", appointmentId, "Going to:", targetUrl);

  fetch('mark_single_appointment_viewed.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'id=' + encodeURIComponent(appointmentId)
  })
  .then(response => {
    console.log("Response status:", response.status);
    // No matter what, continue to the prescription page
    window.location.href = targetUrl;
  })
  .catch(error => {
    console.error("Error marking viewed:", error);
    // Still redirect even if error
    window.location.href = targetUrl;
  });
}
 function deleteRow(button) {
    const row = button.closest('tr');
    if (row) {
      row.remove();
    }
  }
  function deleteRow(button) {
  if (confirm("Are you sure you want to remove this appointment from view?")) {
    const row = button.closest('tr');
    if (row) row.remove();
  }
}

</script>




</body>
</html>
