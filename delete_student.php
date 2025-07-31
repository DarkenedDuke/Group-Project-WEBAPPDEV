<?php 
session_start();
include 'config.php';

if (!isset($_SESSION['loggedin'])) {
    $_SESSION['error'] = "You must be logged in to access this page.";
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "You don't have permission to perform this action.";
    header("Location: view_students.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Validate ID
    if ($id <= 0) {
        $_SESSION['error'] = "Invalid student ID";
        header("Location: view_students.php");
        exit;
    }
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Student deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting student: " . $conn->error;
    }
    
    $stmt->close();
}

header("Location: view_students.php");
exit;
?>