<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/json_utils.php';

require_login('admin');

$admin = $_SESSION['username'];

// Get statistics from JSON
$total_votes = get_total_votes_cast();
$total_voters = get_total_registered_voters();
$vote_percentage = ($total_voters > 0) ? round(($total_votes / $total_voters) * 100, 2) : 0;

// Get total positions and candidates from aspirants.json
$aspirants = json_load('aspirants.json');
$candidates_count = is_array($aspirants) ? count($aspirants) : 0;
$positions_set = [];
if (is_array($aspirants)) {
    foreach ($aspirants as $a) {
        if (!empty($a['position'])) {
            $positions_set[$a['position']] = true;
        }
    }
}
$positions_count = count($positions_set);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-vote-yea"></i> <?php echo SITE_TITLE; ?> [ADMIN]
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="positions.php">Positions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="candidates.php">Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="voters.php">Voters</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="results.php">Results</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $admin; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-lg-12">
                <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-primary" style="font-size: 2rem;">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="card-title mt-2">Total Registered Voters</h6>
                        <p class="card-text" style="font-size: 1.8rem;">
                            <strong><?php echo $total_voters; ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-success" style="font-size: 2rem;">
                            <i class="fas fa-ballot-check"></i>
                        </div>
                        <h6 class="card-title mt-2">Total Votes Cast</h6>
                        <p class="card-text" style="font-size: 1.8rem;">
                            <strong><?php echo $total_votes; ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="text-info" style="font-size: 2rem;">
                            <i class="fas fa-chair"></i>
                        </div>
                        <h6 class="card-title mt-2">Total Positions</h6>
                        <p class="card-text" style="font-size: 1.8rem;">
                            <strong><?php echo $positions_count; ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="text-warning" style="font-size: 2rem;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h6 class="card-title mt-2">Total Candidates</h6>
                        <p class="card-text" style="font-size: 1.8rem;">
                            <strong><?php echo $candidates_count; ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voting Statistics -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Voting Turnout</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1"><strong>Voter Participation: <span class="float-end"><?php echo $vote_percentage; ?>%</span></strong></p>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $vote_percentage; ?>%;" 
                                     aria-valuenow="<?php echo $vote_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <strong><?php echo $vote_percentage; ?>%</strong>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted"><?php echo $total_votes; ?> out of <?php echo $total_voters; ?> registered voters have participated</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-cogs"></i> Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="positions.php" class="btn btn-primary me-2 mb-2">
                            <i class="fas fa-plus"></i> Manage Positions
                        </a>
                        <a href="candidates.php" class="btn btn-success me-2 mb-2">
                            <i class="fas fa-plus"></i> Manage Candidates
                        </a>
                        <a href="voters.php" class="btn btn-info me-2 mb-2">
                            <i class="fas fa-list"></i> Manage Voters
                        </a>
                        <a href="results.php" class="btn btn-warning mb-2">
                            <i class="fas fa-chart-bar"></i> View Results
                        </a>
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
