<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #f8f9fa;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #e9ecef;
        }
        .sidebar .active {
            font-weight: bold;
            color: #0d6efd;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }
        h2 {
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="content">
    <h2>Selamat Datang di Dashboard Admin</h2>
    <p>Gunakan menu di sebelah kiri untuk mengelola konten pada halaman publik.</p>
</div>

</body>
</html>
