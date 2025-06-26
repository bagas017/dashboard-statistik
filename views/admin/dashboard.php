<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}
?>
<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        h1 {
            margin-bottom: 30px;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Dashboard</h1>
        <div class="btn-group">
            <a href="../public/beranda.php" style="
    background-color: green;
    color: white;
"class="btn">Go to Public View</a>
            <a href="kategori/index.php" class="btn">Go to Kategori</a>
            <a href="statistik/index.php" class="btn">Go to Statistik</a>
            <a href="submenu/index.php" class="btn">Go to Submenu</a>
        </div>
    </div>
</body>
</html>

