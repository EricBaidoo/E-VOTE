<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
ini_set('log_errors', 1);
ini_set('error_log', $logDir . '/php_errors.log');

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/json_utils.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (is_logged_in()) {
    $base = rtrim(SITE_URL, '/');
    if (is_admin()) {
        $target = $base ? $base . '/admin/dashboard.php' : 'admin/dashboard.php';
    } else {
        $target = $base ? $base . '/voter/vote.php' : 'voter/vote.php';
    }
    header('Location: ' . $target);
    exit;
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = sanitize($_POST['role'] ?? 'voter');
    $name = sanitize($_POST['name'] ?? '');
    $pin = $_POST['pin'] ?? '';

    $admins = json_load('admins.json');
    $voters = json_load('voters.json');

    if ($role === 'admin') {
        $adminMatch = null;
        foreach ($admins as $a) {
            if (strcasecmp($name, $a['name'] ?? '') === 0 || strcasecmp($name, ADMIN_DEFAULT_USERNAME) === 0) {
                $adminMatch = $a;
                break;
            }
        }
        if (!$adminMatch && strcasecmp($name, ADMIN_DEFAULT_USERNAME) === 0) {
            $adminMatch = ['id' => 1, 'name' => ADMIN_DEFAULT_USERNAME, 'pin' => ADMIN_DEFAULT_PASSWORD];
        }

        if ($adminMatch && ($pin === ($adminMatch['pin'] ?? '') || $pin === ADMIN_DEFAULT_PASSWORD)) {
            $_SESSION['user_id'] = 'admin_' . ($adminMatch['id'] ?? 1);
            $_SESSION['username'] = $adminMatch['name'] ?? ADMIN_DEFAULT_USERNAME;
            $_SESSION['role'] = 'admin';
            $base = rtrim(SITE_URL, '/');
            $target = $base ? $base . '/admin/dashboard.php' : 'admin/dashboard.php';
            header('Location: ' . $target);
            exit;
        }
        $error = 'Invalid admin credentials. Use Admin Name and PIN.';
    } else {
        $foundVoter = null;
        foreach ($voters as $v) {
            $matchName = strcasecmp($name, $v['name'] ?? '') === 0;
            if ($matchName && ($pin === ($v['pin'] ?? ''))) {
                $foundVoter = $v;
                break;
            }
        }
        if ($foundVoter) {
            $_SESSION['user_id'] = (string)$foundVoter['id'];
            $_SESSION['username'] = $foundVoter['name'] ?? 'Voter';
            $_SESSION['role'] = 'voter';
            $base = rtrim(SITE_URL, '/');
            $target = $base ? $base . '/voter/vote.php' : 'voter/vote.php';
            header('Location: ' . $target);
            exit;
        }
        $error = 'Invalid voter credentials. Use your Name and PIN.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - <?php echo SITE_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 0 1.5rem;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-icon {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }
        
        .logo-icon i {
            font-size: 2.25rem;
            color: #2563eb;
        }
        
        .logo-title {
            color: white;
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }
        
        .logo-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9375rem;
        }
        
        .card {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: none;
            padding: 2.5rem;
        }
        
        .form-section-title {
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 1rem;
        }
        
        .role-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.875rem;
            margin-bottom: 1.75rem;
        }
        
        .role-option {
            position: relative;
        }
        
        .role-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .role-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
        }
        
        .role-option label:hover {
            border-color: #d1d5db;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .role-option input[type="radio"]:checked + label {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.2);
        }
        
        .role-option.admin input[type="radio"]:checked + label {
            border-color: #ef4444;
            background: #fef2f2;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.2);
        }
        
        .role-icon {
            width: 48px;
            height: 48px;
            background: #f3f4f6;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.625rem;
            transition: all 0.2s ease;
        }
        
        .role-icon i {
            font-size: 1.5rem;
            color: #6b7280;
        }
        
        .role-option input[type="radio"]:checked + label .role-icon {
            background: #2563eb;
        }
        
        .role-option.admin input[type="radio"]:checked + label .role-icon {
            background: #ef4444;
        }
        
        .role-option input[type="radio"]:checked + label .role-icon i {
            color: white;
        }
        
        .role-label {
            font-size: 0.9375rem;
            font-weight: 600;
            color: #111827;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            font-size: 0.9375rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .form-control::placeholder {
            color: #9ca3af;
        }
        
        .btn-signin {
            width: 100%;
            padding: 0.9375rem 1.5rem;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            margin-top: 0.5rem;
        }
        
        .btn-signin:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
            transform: translateY(-1px);
        }
        
        .btn-signin:active {
            transform: translateY(0);
        }
        
        .btn-signin i {
            margin-right: 0.5rem;
        }
        
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            border: none;
            font-size: 0.875rem;
        }
        
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
        }
        
        .alert i {
            margin-right: 0.5rem;
        }
        
        .footer {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.8125rem;
        }
        
        @media (max-width: 576px) {
            .card {
                padding: 2rem 1.5rem;
            }
            
            .logo-title {
                font-size: 1.5rem;
            }
            
            .role-options {
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-vote-yea"></i>
            </div>
            <h1 class="logo-title"><?php echo SITE_TITLE; ?></h1>
            <p class="logo-subtitle">Secure Online Voting Platform</p>
        </div>

        <div class="card">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-section-title">Select Account Type</div>
                
                <div class="role-options">
                    <div class="role-option">
                        <input type="radio" name="role" id="voter" value="voter" checked>
                        <label for="voter">
                            <div class="role-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="role-label">Voter</span>
                        </label>
                    </div>
                    
                    <div class="role-option admin">
                        <input type="radio" name="role" id="admin" value="admin">
                        <label for="admin">
                            <div class="role-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span class="role-label">Admin</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required autofocus>
                </div>

                <div class="form-group">
                    <label for="pin" class="form-label">Security PIN</label>
                    <input type="password" class="form-control" id="pin" name="pin" placeholder="Enter your PIN" required>
                </div>

                <button type="submit" class="btn-signin">
                    <i class="fas fa-sign-in-alt"></i>Sign In
                </button>
            </form>
        </div>

        <div class="footer">
            &copy; <?php echo date('Y'); ?> <?php echo SITE_TITLE; ?>. All Rights Reserved.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
