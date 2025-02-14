<?php
include 'db.php'; // Database connection

// Initialize variables
$editMode = false;
$enrollment = [
    'EnrollmentID' => '',
    'StudentID' => '',
    'CourseID' => '',
    'EnrollmentDate' => ''
];

// Fetch students and courses for dropdowns
$students = $conn->query("SELECT StudentID, FullName FROM Students");
$courses = $conn->query("SELECT CourseID, CourseName FROM Courses");

// Handle search functionality
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Handle logic for adding, editing, and deleting enrollments
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save'])) {
        $id = $_POST['EnrollmentID'];
        $studentID = $_POST['StudentID'];
        $courseID = $_POST['CourseID'];
        $enrollmentDate = $_POST['EnrollmentDate'];

        if ($id) {
            // Edit enrollment
            $sql = "UPDATE Enrollments SET StudentID = ?, CourseID = ?, EnrollmentDate = ? WHERE EnrollmentID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisi", $studentID, $courseID, $enrollmentDate, $id);
        } else {
            // Add new enrollment
            $sql = "INSERT INTO Enrollments (StudentID, CourseID, EnrollmentDate) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $studentID, $courseID, $enrollmentDate);
        }

        if ($stmt->execute()) {
            header("Location: Enrollments.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['delete'];
        $sql = "DELETE FROM Enrollments WHERE EnrollmentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: Enrollments.php");
        exit();
    }
}

// Load data for editing an enrollment
if (isset($_GET['edit'])) {
    $editMode = true;
    $id = $_GET['edit'];
    $sql = "SELECT * FROM Enrollments WHERE EnrollmentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $enrollment = $result->fetch_assoc();
    }
}

// Search query to filter enrollments based on student or course name
if ($searchTerm) {
    $sql = "SELECT e.EnrollmentID, s.FullName AS StudentName, c.CourseName, e.EnrollmentDate 
            FROM Enrollments e
            JOIN Students s ON e.StudentID = s.StudentID
            JOIN Courses c ON e.CourseID = c.CourseID
            WHERE s.FullName LIKE ? OR c.CourseName LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // If no search term, fetch all enrollments
    $sql = "SELECT e.EnrollmentID, s.FullName AS StudentName, c.CourseName, e.EnrollmentDate 
            FROM Enrollments e
            JOIN Students s ON e.StudentID = s.StudentID
            JOIN Courses c ON e.CourseID = c.CourseID";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            text-align: center;
        }

        .form-container {
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form input, form select, form button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            background-color: #5cb85c;
            color: white;
            cursor: pointer;
        }

        form button:hover {
            background-color: #4cae4c;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            display: inline-block;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            text-align: center;
        }

        .btn-edit {
            background-color: #f0ad4e;
        }

        .btn-edit:hover {
            background-color: #ec971f;
        }

        .btn-delete {
            background-color: #d9534f;
        }

        .btn-delete:hover {
            background-color: #c9302c;
        }

        .btn-cancel {
            background-color: #6c757d;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            form input, form select, form button {
                font-size: 14px;
            }

            table th, table td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center text-primary mb-4">Enrollment Management</h2>

   

    <!-- Search Form -->
    <form method="GET" action="Enrollments.php" class="mb-4">
        <input type="text" class="form-control" name="search" placeholder="Search by student or course" value="<?= htmlspecialchars($searchTerm) ?>" />
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    <!-- Form for adding/editing enrollments -->
    <div class="card mb-5">
        <div class="card-header bg-info text-white">
            <h5><?= $editMode ? 'Edit Enrollment' : 'Add New Enrollment' ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="EnrollmentID" value="<?= $enrollment['EnrollmentID'] ?>">
                <div class="mb-3">
                    <label for="StudentID" class="form-label">Student:</label>
                    <select class="form-control" id="StudentID" name="StudentID" required>
                        <option value="">Select a student</option>
                        <?php while ($row = $students->fetch_assoc()): ?>
                            <option value="<?= $row['StudentID'] ?>" <?= $row['StudentID'] == $enrollment['StudentID'] ? 'selected' : '' ?>>
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
                            <option value="<?= $row['CourseID'] ?>" <?= $row['CourseID'] == $enrollment['CourseID'] ? 'selected' : '' ?>>
                                <?= $row['CourseName'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="EnrollmentDate" class="form-label">Enrollment Date:</label>
                    <input type="date" class="form-control" id="EnrollmentDate" name="EnrollmentDate" value="<?= $enrollment['EnrollmentDate'] ?>" required>
                </div>
                <button type="submit" name="save" class="btn btn-success"><?= $editMode ? 'Update' : 'Add New' ?></button>
                <?php if ($editMode): ?>
                    <a href="Enrollments.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Enrollment List -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5>Enrollment List</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Enrollment Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['EnrollmentID'] ?></td>
                        <td><?= $row['StudentName'] ?></td>
                        <td><?= $row['CourseName'] ?></td>
                        <td><?= $row['EnrollmentDate'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['EnrollmentID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" style="display: inline-block;">
                                <button type="submit" name="delete" value="<?= $row['EnrollmentID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this enrollment?')">Delete</button>
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
