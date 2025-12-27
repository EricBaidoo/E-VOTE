<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/json_utils.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (is_logged_in()) {
    if (is_admin()) {
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
    } else {
        header('Location: ' . SITE_URL . '/voter/vote.php');
    }
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
        if ($adminMatch && ($pin === ($adminMatch['pin'] ?? '') || $pin === ADMIN_DEFAULT_PASSWORD)) {
            $_SESSION['user_id'] = 'admin_' . ($adminMatch['id'] ?? 1);
            $_SESSION['username'] = $adminMatch['name'] ?? ADMIN_DEFAULT_USERNAME;
            $_SESSION['role'] = 'admin';
            header('Location: ' . SITE_URL . '/admin/dashboard.php');
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
            header('Location: ' . SITE_URL . '/voter/vote.php');
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
    <title>Login - <?php echo SITE_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-vote-yea"></i> <?php echo SITE_TITLE; ?>
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h3 class="card-title text-center mb-4">Login</h3>

                        <form method="POST">
                            <div class="text-center mb-3">
                                <div class="btn-group" role="group" aria-label="Role selection">
                                    <input type="radio" class="btn-check" name="role" id="role_admin" value="admin" autocomplete="off">
                                    <label class="btn btn-outline-danger" for="role_admin"><i class="fas fa-user-shield"></i> Admin</label>
                                    <input type="radio" class="btn-check" name="role" id="role_voter" value="voter" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary" for="role_voter"><i class="fas fa-user"></i> Voter</label>
                                </div>
                            </div>
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="pin" class="form-label">PIN</label>
                                <input type="password" class="form-control" id="pin" name="pin" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center mb-0 text-muted">
                            Select Admin or Voter above. Login uses JSON data (no database).
                        </p>
                    </div>
                </div>
                
                <div class="card mt-3 bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Example Credentials</h6>
                        <small>
                            <strong>Admin:</strong> admin / admin123<br>
                            <strong>Voter:</strong> Use a Name + PIN from data/voters.json
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
