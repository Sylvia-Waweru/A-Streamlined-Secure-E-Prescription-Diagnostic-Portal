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
  <title>Appointments - Patient Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="patient\E.jpg">

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
    .form-section {
      background: linear-gradient(145deg, rgba(20, 20, 40, 0.8), rgba(10, 10, 25, 0.8));
      box-shadow: 0 12px 36px rgba(0,0,0,0.75);
      backdrop-filter: blur(15px);
      color: #e6f1ff;
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 18px;
      padding: 40px;
    }
    .btn {
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
  </style>
</head>
<body>
  <div class="sidebar">
    <h4>✨ Patient Portal ✨</h4>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="appointment.php" class="active"><i class="fas fa-calendar-alt"></i> Appointments</a>
    <a href="prescriptions.php"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
          <a href="diagnosis.php" ><i class="fas fa-stethoscope"></i> Diagnosis</a>
    <a href="doctor.php"><i class="fas fa-user-md"></i> Doctor Info</a>
  </div>
  <div class="content">
    <div class="top-bar">
      <h5 class="mb-0 text-light">Appointments</h5>
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

    <div class="form-section mt-5">
      <h4 class="mb-4">Schedule a New Appointment</h4>
      <form id="appointmentForm"> 
        <div class="mb-3">
          <label for="doctorName" class="form-label">Doctor Name</label>
          <input type="text" class="form-control" id="doctorName" name="doctorName" required>
        </div>
        <div class="mb-3">
          <label for="patientName" class="form-label">Patient Name</label>
<input type="text" class="form-control" id="patientName" name="patientName" value="<?php echo $patientName; ?>" readonly>
        </div>
        <div class="mb-3"><label for="date_of_birth" class="form-label">Date of Birth</label><input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required></div>
            <div class="mb-3"><label for="age" class="form-label">Age</label><input type="number" class="form-control" id="age" name="age" required></div>
            <div class="mb-3"><label for="gender" class="form-label">Gender</label><select class="form-control" id="gender" name="gender" required><option value="">Select gender</option><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>
            <div class="mb-3"><label for="phone" class="form-label">Contact Number</label><input type="text" class="form-control" id="phone" name="phone" required></div>
            <div class="mb-3"><label for="email" class="form-label">Email Address</label><input type="email" class="form-control" id="email" name="email" required></div>
            <div class="mb-3"><label for="address" class="form-label">Address</label><textarea class="form-control" id="address" name="address" rows="2" required></textarea></div>
        <div class="mb-3">
          <label for="appointmentDate" class="form-label">Date Of Appointment</label>
          <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" required>
        </div>
        <div class="mb-3">
          <label for="appointmentTime" class="form-label">Time Of Appointment</label>
          <input type="time" class="form-control" id="appointmentTime" name="appointmentTime" required>
        </div>
        <div class="mb-3">
          <label for="reason" class="form-label">Reason For Appointment</label>
          <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn"><i class="fas fa-calendar-check me-2"></i>Schedule Appointment</button>
      </form>
    </div>
  </div>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
 document.getElementById('appointmentForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('add_appointment.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      Swal.fire({
        title: 'Redirecting to Payment',
        text: 'Please complete the payment to confirm your appointment.',
        icon: 'info',
        timer: 2500,
        showConfirmButton: false
      }).then(() => {
        window.location.href = data.redirect_url;
      });
    } else {
      Swal.fire('Error', data.message || 'Failed to schedule appointment.', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
  });
});

</script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
