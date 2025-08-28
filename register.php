<?php
// register.php

session_start();
require 'db.php'; // Your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Required line you were missing
    $role = $_POST['role'];
    $doctor_code = $_POST['doctor_code'] ?? '';
        $specialization = $_POST['specialization'];
         $phone = $_POST['phone'];
        $office_hours = $_POST['office_hours'];



    // Verify required fields
    if (empty($fullname) || empty($email) || empty($password) || empty($role)) {
        die('Please fill in all required fields.');
    }

    // Check secret code for doctors
    if ($role === 'doctor') {
        $expected_code = 'DOC2025';
        if ($doctor_code !== $expected_code) {
            die('Invalid doctor secret code.');
        }
    }

    // Insert user into database (no hashing)
    try {
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role,specialization,phone,office_hours) VALUES (?, ?, ?, ?,?,?,?)");
        $stmt->execute([$fullname, $email, $password, $role,$specialization,$phone,$office_hours]);

        // Set session
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['role'] = $role;
        $_SESSION['fullname'] = $fullname;

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Registration failed: " . $e->getMessage());
    }

} else {
    die("Invalid request method.");
}
