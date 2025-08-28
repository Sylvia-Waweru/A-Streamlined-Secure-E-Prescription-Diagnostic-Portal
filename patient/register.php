<?php
session_name("patient_session");
session_start();         // Start the session
session_unset();         // Clear any existing session variables
session_destroy();       // Destroy old session

session_start();         // Start a fresh session
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Insert new patient into users table with role 'patient'
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, 'patient')");
        $stmt->execute([$fullname, $email, $password]);

        // Get the last inserted user ID
        $userId = $pdo->lastInsertId();

        // Set session variables for the newly registered user
        $_SESSION['patient_id'] = $userId; 
        $_SESSION['role'] = 'patient';
        $_SESSION['fullname'] = $fullname;

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Registration failed: " . $e->getMessage());
    }
}
?>
