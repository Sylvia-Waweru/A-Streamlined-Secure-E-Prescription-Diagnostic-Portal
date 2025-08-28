<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Not logged in.");
}

$user_id = $_SESSION['user_id'];
echo "Logged in as user ID: $user_id<br>"; // DEBUG

$stmt = $pdo->prepare("SELECT fullname, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    die("User with ID $user_id not found in DB.");
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Profile</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="icon" type="image/png" href="patient/E.webp">

  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rubik:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      margin: 0;
      padding: 0;
      background: radial-gradient(circle at top right, #1a1a40, #000);
      color: #f1f1f1;
      font-family: 'Rubik', sans-serif;
    }
    .top-bar {
      background-color: #00332f;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #e0f2f1;
    }
    .profile-container {
      max-width: 900px;
      margin: 60px auto;
      background: linear-gradient(135deg, #2b2b3c 0%, #191933 100%);
      padding: 50px;
      border-radius: 20px;
      box-shadow: 0 0 40px rgba(0,255,200,0.1);
      border: 2px solid rgba(0, 209, 178, 0.3);
      animation: fadeIn 1s ease-out;
    }
    .profile-header {
      text-align: center;
      margin-bottom: 40px;
    }
    .profile-header h2 {
      font-family: 'Orbitron', sans-serif;
      font-weight: bold;
      font-size: 2.5rem;
      color: #00ffe7;
      text-shadow: 0 0 10px rgba(0,255,230,0.7);
    }
    .form-label {
      color: #ccc;
      font-weight: 500;
    }
    .form-control {
      background-color: #222;
      border: 1px solid #555;
      color: #f1f1f1;
      border-radius: 10px;
      transition: all 0.3s;
    }
    .form-control:focus {
      border-color: #00ffe7;
      box-shadow: 0 0 10px rgba(0, 255, 230, 0.4);
      background-color: #1a1a1a;
    }
    .btn-custom {
      background: linear-gradient(135deg, #00ffe7, #00b7c2);
      color: #000;
      font-weight: bold;
      border: none;
      padding: 12px 30px;
      border-radius: 30px;
      box-shadow: 0 4px 20px rgba(0,255,230,0.3);
      transition: transform 0.2s ease-in-out;
    }
    .btn-custom:hover {
      transform: scale(1.05);
      background: linear-gradient(135deg, #00e6d2, #009faa);
    }
    .btn-outline-light {
      border-radius: 30px;
    }
    .footer-link {
      text-align: center;
      margin-top: 40px;
      display: block;
      color: #888;
      text-decoration: none;
      font-weight: 500;
    }
    .footer-link:hover {
      color: #fff;
      text-shadow: 0 0 5px #00ffe7;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="top-bar">
    <h5 class="mb-0">Doctor Profile</h5>
    <div class="dropdown">
      <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="doctorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <span><?= htmlspecialchars($doctor['fullname']) ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="doctorDropdown">
        <li><a class="dropdown-item" href="login.html"><i class="fas fa-sign-in-alt me-2"></i>Login</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a></li>
      </ul>
    </div>
  </div>

  <div class="container">
    <div class="profile-container">
      <div class="profile-header">
        <h2><i class="fas fa-user-md me-2"></i>Doctor Profile</h2>
      </div>
      <form action="update_profile.php" method="POST">
        <div class="row g-4">
          <div class="col-md-6">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($doctor['fullname']) ?>" required>
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($doctor['email']) ?>" required>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-5">
          <button type="submit" class="btn btn-custom">Update Profile</button>
          <a href="change_password.php" class="btn btn-outline-light">Change Password</a>
        </div>
      </form>
      <a href="dashboard.php" class="footer-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <?php include 'footer.php'; ?>

</body>
</html>
 