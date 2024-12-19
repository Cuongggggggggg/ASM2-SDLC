<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: loginn.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Home</title>
    <style>
        body {
    font-family: 'Roboto', sans-serif;
    background-color: #f0f2f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    color: #333;
}

.container {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    width: 80%;
    max-width: 500px;
    text-align: center;
}

.container h2 {
    font-size: 26px;
    margin-bottom: 25px;
    color: #2c3e50;
    font-weight: bold;
}

.container ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.container ul li {
    margin: 15px 0;
}

.container ul li a {
    text-decoration: none;
    font-size: 18px;
    color: #ffffff;
    padding: 12px 25px;
    border-radius: 8px;
    background-color: #3498db;
    transition: transform 0.2s, background-color 0.3s ease;
    display: inline-block;
    font-weight: 500;
}

.container ul li a:hover {
    background-color: #2980b9;
    transform: translateY(-3px);
}

.container ul li a:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .container {
        width: 90%;
        padding: 20px;
    }

    .container h2 {
        font-size: 22px;
    }

    .container ul li a {
        font-size: 16px;
        padding: 10px 20px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome Teacher!</h2>
        <ul>
            <li><a href="grades.php">Manage Grades</a></li>
            <li><a href="courses.php">Manage Courses</a></li>
        </ul>
    </div>
</body>
</html>
