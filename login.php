<?php
session_name("doctor_session");
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        die('Please enter both email and password.');
    }

    // Check user with role=doctor
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'doctor'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['password'] === $password) {
        $_SESSION['doctor_id'] = $user['id'];
        $_SESSION['doctor_name'] = $user['fullname'];
        $_SESSION['role'] = 'doctor';

        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid doctor credentials.'); window.location.href='login.html';</script>";
        exit();
    }
}
?>
