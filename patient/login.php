<?php
session_name("patient_session");
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        die('Please enter both email and password.');
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'patient'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['password'] === $password) {
        // Clear doctor session if exists
        unset($_SESSION['doctor_id']);
        unset($_SESSION['doctor_name']);

        $_SESSION['patient_id'] = $user['id'];
        $_SESSION['patient_name'] = $user['fullname'];
        $_SESSION['role'] = 'patient';

        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid patient credentials.'); window.location.href='login.html';</script>";
        exit();
    }
}
?>
