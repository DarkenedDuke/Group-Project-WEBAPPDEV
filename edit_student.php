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

$student = null;
$success = null;
$error = null;


if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
   
    if ($id <= 0) {
        header("Location: view_students.php");
        exit;
    }
    
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
    } else {
        header("Location: view_students.php");
        exit;
    }
    $stmt->close();
}


if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $dob = $_POST['dob'];
    
    
    if (empty($name) || empty($email) || empty($course) || empty($dob)) {
        $error = "All fields are required!";
    } else {
        
        $stmt = $conn->prepare("UPDATE students SET name=?, email=?, course=?, dob=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $course, $dob, $id);
        
        if ($stmt->execute()) {
            $success = "Student updated successfully!";
            
            $student = ['id' => $id, 'name' => $name, 'email' => $email, 'course' => $course, 'dob' => $dob];
        } else {
            $error = "Error updating record: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Student</h2>
        
        <?php if (isset($success)): ?>
            <p class="success"><?= $success ?></p>
        <?php elseif (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        
        <?php if (isset($student)): ?>
            <form method="post">
                <input type="hidden" name="id" value="<?= $student['id'] ?>">
                
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                
                <label>Course:</label>
                <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>" required>
                
                <label>Date of Birth:</label>
                <input type="date" name="dob" value="<?= $student['dob'] ?>" required>
                
                <input type="submit" name="update" value="Update">
            </form>
        <?php endif; ?>
        
        <a href="view_students.php" class="btn">Back to Students List</a>
    </div>
</body>
</html>