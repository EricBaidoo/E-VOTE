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

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card shadow-lg text-center">
                    <div class="card-body p-5">
                        <div style="font-size: 4rem; color: #28a745;" class="mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        
                        <h2 class="card-title mb-3">Thank You!</h2>
                        <p class="card-text lead">Your vote has been successfully submitted.</p>
                        
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-lock"></i> 
                            <strong>Your vote is encrypted and anonymous</strong>
                            <p class="mb-0">It cannot be traced back to you.</p>
                        </div>

                        <div class="mt-5">
                            <a href="dashboard.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i> Go to Dashboard
                            </a>
                            <br><br>
                            <a href="results.php" class="btn btn-info btn-lg">
                                <i class="fas fa-chart-bar"></i> View Results
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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
