<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/json_utils.php';

require_login('voter');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Submitted - <?php echo SITE_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-vote-yea"></i> <?php echo SITE_TITLE; ?>
            </a>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-lg-7 mx-auto">
                <div class="card card-elevated text-center">
                    <div class="card-body p-5">
                        <div style="font-size: 6rem; color: #16a34a;" class="mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        
                        <h1 class="card-title mb-3 fw-bold">Thank You!</h1>
                        <p class="card-text lead mb-4">Your vote has been successfully submitted.</p>
                        
                        <div class="alert alert-success border-0 mt-4" style="background: linear-gradient(to right, rgba(22, 163, 74, 0.1), rgba(22, 163, 74, 0.05));">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-shield-halved fa-2x text-success me-3"></i>
                                <div class="text-start">
                                    <h6 class="mb-1 fw-bold text-success">Your vote is encrypted and anonymous</h6>
                                    <p class="mb-0 text-muted">It cannot be traced back to you.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <a href="dashboard.php" class="btn btn-primary btn-lg px-5 mb-3">
                                <i class="fas fa-home me-2"></i>Go to Dashboard
                            </a>
                            <br>
                            <a href="results.php" class="btn btn-outline-primary btn-lg px-5">
                                <i class="fas fa-chart-bar me-2"></i>View Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 E-Voting System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
