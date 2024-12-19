<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <style>
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            color: #444;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
            text-align: center;
        }

        .container h2 {
            font-size: 30px;
            margin-bottom: 20px;
            color: #2a9d8f;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .container ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .container ul li {
            margin: 20px 0;
        }

        .container ul li a {
            text-decoration: none;
            font-size: 18px;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            background: linear-gradient(135deg, #2a9d8f, #264653);
            transition: all 0.4s ease;
            display: inline-block;
        }

        .container ul li a:hover {
            background: linear-gradient(135deg, #264653, #2a9d8f);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome Admin!</h2>
        <ul>
            <li><a href="enrollments.php">Manage Enrollments</a></li>
            <li><a href="student.php">Manage Students</a></li>
        </ul>
    </div>
</body>
</html>
