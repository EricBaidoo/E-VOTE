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
</head>
<body class="auth-bg">
    <div class="container py-5">
        <div class="row align-items-center justify-content-center min-vh-100">
            <div class="col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <div class="brand-pill d-inline-flex mb-3">
                        <i class="fas fa-vote-yea"></i>
                        <span><?php echo SITE_TITLE; ?></span>
                    </div>
                    <h1 class="h2 fw-bold mb-2">Welcome back</h1>
                    <p class="text-muted mb-0">Sign in to continue to your account</p>
                </div>
                
                <div class="card auth-card">
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Account Type</label>
                                <div class="btn-group w-100" role="group" aria-label="Role selection">
                                    <input type="radio" class="btn-check" name="role" id="role_voter" value="voter" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary" for="role_voter">
                                        <i class="fas fa-user me-2"></i>Voter
                                    </label>
                                    <input type="radio" class="btn-check" name="role" id="role_admin" value="admin" autocomplete="off">
                                    <label class="btn btn-outline-danger" for="role_admin">
                                        <i class="fas fa-user-shield me-2"></i>Admin
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Name</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Enter your name" required autofocus>
                            </div>

                            <div class="mb-4">
                                <label for="pin" class="form-label fw-semibold">PIN</label>
                                <input type="password" class="form-control form-control-lg" id="pin" name="pin" placeholder="Enter your PIN" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 mt-5">
        <p class="text-muted small mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_TITLE; ?>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
