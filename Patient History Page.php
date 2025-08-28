<?php
session_name("doctor_session");

session_start();
require 'db.php';

// Allow access if logged in as doctor or as an admin
if ($_SESSION['role'] !== 'doctor') {
    header("Location: login.html"); // or access denied
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
$patientName = '';
$appointments = [];
$diagnoses = [];
$prescriptions = [];
$patientProfile = null;
$medicines = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientName = trim($_POST['patient_name'] ?? '');

    if ($patientName !== '') {
        // Fetch patient profile
        $profileStmt = $pdo->prepare("SELECT * FROM patients WHERE name = ? LIMIT 1");
        $profileStmt->execute([$patientName]);
        $patientProfile = $profileStmt->fetch();

        // Fetch appointments
        $stmt1 = $pdo->prepare("SELECT * FROM appointments WHERE patient_name = ?");
        $stmt1->execute([$patientName]);
        $appointments = $stmt1->fetchAll();

        // Fetch diagnoses
        $stmt2 = $pdo->prepare("SELECT * FROM diagnoses WHERE patient_name = ?");
        $stmt2->execute([$patientName]);
        $diagnoses = $stmt2->fetchAll();

        // Fetch prescriptions
        $stmt3 = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_name = ?");
        $stmt3->execute([$patientName]);
        $prescriptions = $stmt3->fetchAll();

        // Fetch medicines
        $stmt4 = $pdo->prepare("SELECT * FROM medicines WHERE patient_name = ?");
        $stmt4->execute([$patientName]);
        $medicines = $stmt4->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient History Lookup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f9f9f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      margin-top: 40px;
    }
    .nav-tabs .nav-link.active {
      background-color: #00796b;
      color: white;
    }
    .card {
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .table th, .table td {
      vertical-align: middle;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="card p-4">
    <h3 class="mb-4">Search Patient History</h3>

    <form method="POST" class="mb-4">
      <div class="input-group">
        <input type="text" name="patient_name" class="form-control" placeholder="Enter patient's full name..." value="<?= htmlspecialchars($patientName) ?>" required>
        <button class="btn btn-primary" type="submit">Search</button>
      </div>
    </form>

    <?php if ($patientName && !$appointments && !$diagnoses && !$prescriptions && !$patientProfile && !$medicines): ?>
      <div class="alert alert-warning">No records found for <strong><?= htmlspecialchars($patientName) ?></strong>.</div>
    <?php endif; ?>

    <?php if ($patientProfile): ?>
      <div class="mb-4">
        <h5>Patient Details</h5>
        <ul class="list-group">
          <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($patientProfile['name']) ?></li>
          <li class="list-group-item"><strong>Age:</strong> <?= htmlspecialchars($patientProfile['age']) ?></li>
          <li class="list-group-item"><strong>Gender:</strong> <?= htmlspecialchars($patientProfile['gender']) ?></li>
          <li class="list-group-item"><strong>Phone:</strong> <?= htmlspecialchars($patientProfile['phone']) ?></li>
          <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($patientProfile['email']) ?></li>
          <li class="list-group-item"><strong>Address:</strong> <?= htmlspecialchars($patientProfile['address']) ?></li>
          <li class="list-group-item"><strong>Medical History:</strong> <?= htmlspecialchars($patientProfile['medical_history']) ?></li>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($patientName): ?>
      <ul class="nav nav-tabs mb-3" id="historyTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab">Appointments</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="diagnosis-tab" data-bs-toggle="tab" data-bs-target="#diagnosis" type="button" role="tab">Diagnosis</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab">Prescriptions</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="medicines-tab" data-bs-toggle="tab" data-bs-target="#medicines" type="button" role="tab">Medicines</button>
        </li>
      </ul>

      <div class="tab-content" id="historyTabContent">
        <!-- Appointments Tab -->
        <div class="tab-pane fade show active" id="appointments" role="tabpanel">
          <?php if (count($appointments) > 0): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Doctor</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Reason</th>
                  <th>Status</th>
                  <th>Payment</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $i => $a): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($a['doctor_name']) ?></td>
                    <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($a['time_slot']) ?></td>
                    <td><?= htmlspecialchars($a['reason']) ?></td>
                    <td><?= htmlspecialchars($a['status']) ?></td>
                    <td><?= htmlspecialchars($a['payment_status']) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">No appointments found.</p>
          <?php endif; ?>
        </div>

        <!-- Diagnosis Tab -->
        <div class="tab-pane fade" id="diagnosis" role="tabpanel">
          <?php if (count($diagnoses) > 0): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Symptoms</th>
                  <th>Diagnosis</th>
                  <th>Recommendation</th>
                  <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($diagnoses as $i => $d): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($d['symptoms']) ?></td>
                    <td><?= htmlspecialchars($d['diagnosis']) ?></td>
                    <td><?= htmlspecialchars($d['recommendation']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($d['created_at'])) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">No diagnosis records found.</p>
          <?php endif; ?>
        </div>

        <!-- Prescriptions Tab -->
        <div class="tab-pane fade" id="prescriptions" role="tabpanel">
          <?php if (count($prescriptions) > 0): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Medicine</th>
                  <th>Dosage</th>
                  <th>Instructions</th>
                  <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($prescriptions as $i => $p): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($p['medicine']) ?></td>
                    <td><?= htmlspecialchars($p['dosage']) ?></td>
                    <td><?= htmlspecialchars($p['instructions']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($p['created_at'])) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">No prescription records found.</p>
          <?php endif; ?>
        </div>

        <!-- Medicines Tab -->
        <div class="tab-pane fade" id="medicines" role="tabpanel">
          <?php if (count($medicines) > 0): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Description</th>
                  <th>Side Effects</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($medicines as $i => $m): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['type']) ?></td>
                    <td><?= htmlspecialchars($m['description']) ?></td>
                    <td><?= htmlspecialchars($m['side_effects']) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">No medicine records found.</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>

</body>
</html>
