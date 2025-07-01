<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        
        .welcome-container {
            width: 100%;
            max-width: 500px;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .welcome-container:hover {
            transform: translateY(-5px);
        }
        
        .welcome-header h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .welcome-header p {
            color: var(--gray-color);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        .btn-welcome {
            background-color: var(--primary-color);
            border: none;
            height: 48px;
            border-radius: 8px;
            font-weight: 600;
            padding: 0 2rem;
            margin: 0.5rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-welcome:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }
        
        .btn-welcome i {
            margin-right: 8px;
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2rem;
        }
        
        @media (min-width: 576px) {
            .btn-group {
                flex-direction: row;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-header">
            <h1>Welcome to Our Platform</h1>
            <p>Please login to access your account or explore our public content</p>
        </div>
        
        <div class="btn-group">
            <a href="/login.php" class="btn btn-primary btn-welcome">
                <i class="fas fa-sign-in-alt"></i> Go to Login
            </a>
            <a href="/views/public/beranda.php" class="btn btn-primary btn-welcome">
                <i class="fas fa-globe"></i> View Public
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>