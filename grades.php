<?php
include 'db.php'; // Database connection

// Initialize variables
$editMode = false;
$grade = [
    'GradeID' => '',
    'StudentID' => '',
    'CourseID' => '',
    'Grade' => ''
];

// Fetch students and courses for dropdowns
$students = $conn->query("SELECT StudentID, FullName FROM Students");
$courses = $conn->query("SELECT CourseID, CourseName FROM Courses");

// Handle logic for adding, editing, and deleting grades
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save'])) {
        $id = $_POST['GradeID'];
        $studentID = $_POST['StudentID'];
        $courseID = $_POST['CourseID'];
        $gradeValue = $_POST['Grade'];

        if ($id) {
            // Edit grade
            $sql = "UPDATE Grades SET StudentID = ?, CourseID = ?, Grade = ? WHERE GradeID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iidi", $studentID, $courseID, $gradeValue, $id);
        } else {
            // Add new grade
            $sql = "INSERT INTO Grades (StudentID, CourseID, Grade) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iid", $studentID, $courseID, $gradeValue);
        }

        if ($stmt->execute()) {
            header("Location: grades.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['delete'];
        $sql = "DELETE FROM Grades WHERE GradeID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: grades.php");
        exit();
    }
}

// Load data for editing a grade
if (isset($_GET['edit'])) {
    $editMode = true;
    $id = $_GET['edit'];
    $sql = "SELECT * FROM Grades WHERE GradeID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $grade = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
        background: #4caf50; /* Nền màu xanh nhạt */
        font-family: 'Roboto', sans-serif;
    }
    .container {
        margin-top: 50px;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        border: none;
    }
    .card-header {
        border-bottom: none;
        font-weight: bold;
    }
    .card-header.bg-info {
        background-color: #007bb0; /* Xanh dương đậm */
        color: white;
    }
    .card-header.bg-dark {
        background-color: #4caf50; /* Xanh lá cây */
        color: white;
    }
    .btn {
        border-radius: 30px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .btn-success {
        background-color: #b71c1c; /* Xanh teal */
        border: none;
    }
    .btn-success:hover {
        background-color: #00695c; /* Xanh teal đậm */
    }
    .btn-warning {
        background-color: #f57f17; /* Cam vàng đậm */
        border: none;
    }
    .btn-warning:hover {
        background-color: #e65100; /* Cam đậm */
    }
    .btn-danger {
        background-color: #c62828; /* Đỏ đậm */
        border: none;
    }
    .btn-danger:hover {
        background-color: #b71c1c; /* Đỏ rất đậm */
    }
    .btn-secondary {
        background-color: #546e7a; /* Xám xanh */
        border: none;
    }
    .btn-secondary:hover {
        background-color: #37474f; /* Xám xanh đậm */
    }
    table.table-hover {
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    table.table-hover th {
        background: #f0f4c3; /* Màu xanh vàng nhạt */
        color: #212121;
        font-weight: bold;
    }
    table.table-hover td {
        background: #ffffff; /* Nền trắng */
        color: #424242;
    }
    table.table-hover tr:hover td {
        background: #dcedc8; /* Màu xanh lá nhạt */
    }
</style>

</head>
<body>
<div class="container">
    <h2 class="text-center text-primary mb-4">Grade Management</h2>



    <!-- Form for adding/editing grades -->
    <div class="card mb-5">
        <div class="card-header bg-info text-white">
            <h5><?= $editMode ? 'Edit Grade' : 'Add New Grade' ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="GradeID" value="<?= $grade['GradeID'] ?>">
                <div class="mb-3">
                    <label for="StudentID" class="form-label">Student:</label>
                    <select class="form-control" id="StudentID" name="StudentID" required>
                        <option value="">Select a student</option>
                        <?php while ($row = $students->fetch_assoc()): ?>
                            <option value="<?= $row['StudentID'] ?>" <?= $row['StudentID'] == $grade['StudentID'] ? 'selected' : '' ?>>
                                <?= $row['FullName'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="CourseID" class="form-label">Course:</label>
                    <select class="form-control" id="CourseID" name="CourseID" required>
                        <option value="">Select a course</option>
                        <?php while ($row = $courses->fetch_assoc()): ?>
                            <option value="<?= $row['CourseID'] ?>" <?= $row['CourseID'] == $grade['CourseID'] ? 'selected' : '' ?>>
                                <?= $row['CourseName'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="Grade" class="form-label">Grade:</label>
                    <input type="number" step="0.01" class="form-control" id="Grade" name="Grade" value="<?= $grade['Grade'] ?>" required>
                </div>
                <button type="submit" name="save" class="btn btn-success"><?= $editMode ? 'Update' : 'Add New' ?></button>
                <?php if ($editMode): ?>
                    <a href="grades.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Grade List -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5>Grade List</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT g.GradeID, s.FullName AS StudentName, c.CourseName, g.Grade 
                        FROM Grades g
                        JOIN Students s ON g.StudentID = s.StudentID
                        JOIN Courses c ON g.CourseID = c.CourseID";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['GradeID'] ?></td>
                        <td><?= $row['StudentName'] ?></td>
                        <td><?= $row['CourseName'] ?></td>
                        <td><?= $row['Grade'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['GradeID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" style="display: inline-block;">
                                <button type="submit" name="delete" value="<?= $row['GradeID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this grade?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
