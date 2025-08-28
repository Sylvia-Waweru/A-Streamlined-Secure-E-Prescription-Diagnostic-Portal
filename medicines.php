<?php
require 'db.php';
$stmt = $pdo->query("SELECT * FROM medicines");
$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $user = ['fullname' => 'Unknown'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medicines - Doctor's Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Rubik:wght@400;500&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="patient\E.webp">

  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(circle at top left, #121c2b, #0a0f18);
      color: #e0e0e0;
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
    .form-section {
      background: rgba(230, 225, 225, 0.05);
      backdrop-filter: blur(8px);
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,255,230,0.08);
    }
    .form-control, textarea {
      background-color: rgba(8, 1, 1, 0.99);
      color:rgb(240, 233, 233);
      border: 1px solid #555;
    }
    .form-control:focus {
  border-color: #00ffe7;
  box-shadow: 0 0 0 0.2rem rgba(0,255,230,0.25);
  background-color: rgba(255, 255, 255, 0.9); /* Lighter but not transparent */
  color: #000; /* Make sure text remains visible */
}

    .table {
      color:rgb(5, 5, 5);
      background: rgba(255, 255, 255, 0.83);
    }
    .table th {
      background-color: #002d2a;
      color: #00ffe7;
    }
    .btn-success {
      background-color: #00bfa5;
      border-color: #00bfa5;
    }
    .btn-success:hover {
      background-color: #00ffe7;
      color: #00332f;
      border-color: #00ffe7;
    }
  </style>
</head>

<body>
  <div class="d-flex">
    <div class="sidebar p-3">
      <h4 class="text-center text-white">Doctor's Portal</h4>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="diagnosis.php"><i class="fas fa-notes-medical"></i> Diagnosis</a>
          <a href="medicines.php" class="active"><i class="fas fa-capsules"></i> Medicines</a>
      <a href="patients.php"><i class="fas fa-users"></i> Patients</a>
      <a href="appointments.php"><i class="fas fa-calendar-alt"></i> Future Appointments</a>
      <a href="prescriptions.php"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
    </div>

    <div class="flex-grow-1">
      <div class="top-bar">
        <h5 class="mb-0">Medicines</h5>
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

      <div class="content p-4">
        <div class="form-section">
          <h5 class="mb-4">Add a New Medicine</h5>
          <form id="medicineForm">
            <div class="mb-3">
              <label for="patientName" class="form-label">Patient Name</label>
              <input type="text" class="form-control" id="patientName" name="patientName" required>
            </div>
            <div class="mb-3">
              <label for="medicineName" class="form-label">Medicine Name</label>
              <input type="text" class="form-control" id="medicineName" name="medicineName" required>
            </div>
            <div class="mb-3">
              <label for="medicineType" class="form-label">Medicine Type</label>
              <input type="text" class="form-control" id="medicineType" name="medicineType" required>
            </div>
            <div class="mb-3">
              <label for="medicineDescription" class="form-label">Description</label>
              <textarea class="form-control" id="medicineDescription" name="medicineDescription" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label for="sideEffects" class="form-label">Side Effects</label>
              <textarea class="form-control" id="sideEffects" name="sideEffects" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-capsules me-2"></i>Add Medicine</button>
          </form>
        </div>

        <div class="mt-5">
          <h5 class="mb-4">Available Medicines</h5>
          <table class="table table-bordered" id="medicinesTable">
            <thead>
              <tr>
                <th>Patient Name</th>
                <th>Medicine Name</th>
                <th>Type</th>
                <th>Description</th>
                <th>Side Effects</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($medicines as $medicine): ?>
              <tr>
                <td><?= htmlspecialchars($medicine['patient_name']) ?></td>
                <td><?= htmlspecialchars($medicine['name']) ?></td>
                <td><?= htmlspecialchars($medicine['type']) ?></td>
                <td><?= htmlspecialchars($medicine['description']) ?></td>
                <td><?= htmlspecialchars($medicine['side_effects']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('medicineForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('add_medicine.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            Swal.fire('Success', 'Medicine added successfully!', 'success');

            const table = document.querySelector('#medicinesTable tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
              <td>${formData.get('patientName')}</td>
              <td>${formData.get('medicineName')}</td>
              <td>${formData.get('medicineType')}</td>
              <td>${formData.get('medicineDescription')}</td>
              <td>${formData.get('sideEffects')}</td>
            `;
            table.appendChild(newRow);

            document.getElementById('medicineForm').reset();
          } else {
            Swal.fire('Error', 'Failed to add medicine.', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error', 'An error occurred.', 'error');
        });
    });
  </script>
  <?php include 'footer.php'; ?>

</body>

</html>
