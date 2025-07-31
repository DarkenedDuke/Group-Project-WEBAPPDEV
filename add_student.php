<?php 
session_start();
require 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "You don't have permission to access this page.";
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if (isset($_POST['submit'])) {
    // Sanitize and validate input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $dob = $_POST['dob'];

    // Validate required fields
    if (empty($name) || empty($email) || empty($course) || empty($dob)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        try {
            // Prepare statement without specifying ID (let MySQL auto-increment handle it)
            $stmt = $conn->prepare("INSERT INTO students (name, email, course, dob) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $course, $dob);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Student added successfully!";
                header("Location: view_students.php");
                exit;
            } else {
                throw new Exception("Failed to add student: " . $stmt->error);
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="cat-theme.css">
</head>
<body class="bg-dark text-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-black text-light shadow">
                    <div class="card-header border-bottom border-secondary">
                        <h2 class="mb-0 text-center">Add New Student</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="post" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name:</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary" 
                                       id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control bg-dark text-light border-secondary" 
                                       id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="course" class="form-label">Course:</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary" 
                                       id="course" name="course" required>
                            </div>

                            <div class="mb-4">
                                <label for="dob" class="form-label">Date of Birth:</label>
                                <input type="date" class="form-control bg-dark text-light border-secondary" 
                                       id="dob" name="dob" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="submit" class="btn btn-outline-primary">Add Student</button>
                                <a href="view_students.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>