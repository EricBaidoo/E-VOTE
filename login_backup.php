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
        // Fallback: allow default admin even if admins.json is empty or missing the entry
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
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            padding: 1.5rem 0;
            overflow-y: auto;
        }
        
        .brand-section {
            text-align: center;
            margin-bottom: 1.25rem;
        }
        
        .brand-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #2563eb;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            margin-bottom: 1rem;
        }
        
        .brand-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .brand-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
        }
        
        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: none;
            overflow: hidden;
        }
        
        .login-card .card-body {
            padding: 2rem;
        }
        
        .role-btn {
            padding: 0.75rem 1rem;
            border-width: 2px;
            border-radius: 0.625rem;
            font-weight: 600;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .role-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .role-btn i {
            font-size: 1.125rem;
        }
        
        .btn-check:checked + .role-btn {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
        }
        
        .btn-check:checked + .btn-outline-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }
        
        .btn-check:checked + .btn-outline-danger {
            background: #ef4444;
            border-color: #ef4444;
            color: white;
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.375rem;
            font-size: 0.875rem;
        }
        
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 0.625rem;
            padding: 0.75rem 0.875rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .btn-primary {
            background: #2563eb;
            border: none;
            border-radius: 0.625rem;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .alert {
            border: none;
            border-radius: 0.625rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .footer-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8125rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            margin: 0;
        }
        
        .container {
            max-width: 100%;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        @media (max-width: 576px) {
            .login-card .card-body {
                padding: 1.5rem 1.25rem;
            }
            
            .brand-title {
                font-size: 1.25rem;
            }
            
            .brand-icon {
                width: 50px;
                height: 50px;
                font-size: 1.75rem;
            }
            
            .brand-subtitle {
                font-size: 0.8125rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-wrapper">
            <div class="col-md-6 col-lg-5 c3">
                                <label class="form-label">Account Type</label>
                                <div class="d-grid gap-2">
                                    <input type="radio" class="btn-check" name="role" id="voter" value="voter" checked>
                                    <label class="btn btn-outline-primary role-btn text-start" for="voter">
                                        <i class="fas fa-user me-2"></i>Voter
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="role" id="admin" value="admin">
                                    <label class="btn btn-outline-danger role-btn text-start" for="admin">
                                        <i class="fas fa-user-shield me-2"></i>Administrator
                                    </label>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="pin" class="form-label">PIN</label>
                                <input type="password" class="form-control" id="pin" name="pin" placeholder="Enter your PIN" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign Ind="admin" value="admin">
                                    <label class="btn btn-outline-danger role-btn text-start" for="admin">
                                        <i class="fas fa-user-shield me-2"></i>Administrator
                                    </label>
                                </div>
                            </div>
3">
                    <p class="footer-text">&copy; <?php echo date('Y'); ?> <?php echo SITE_TITLE; ?>
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Enter your full name" required autofocus>
                            </div>

                            <div class="mb-4">
                                <label for="pin" class="form-label">Security PIN</label>
                                <input type="password" class="form-control form-control-lg" id="pin" name="pin" placeholder="Enter your PIN" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In Securely
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="footer-text mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_TITLE; ?>. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
