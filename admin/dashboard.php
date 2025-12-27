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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

    <div class="container-fluid mt-4 mb-5">
        <div class="row mb-4">
            <div class="col-lg-12">
                <h2 class="mb-1"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
                <p class="text-muted">Overview of your e-voting system</p>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-primary" style="font-size: 2.5rem;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="text-end">
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Registered Voters</h6>
                                <h2 class="mb-0 fw-bold"><?php echo $total_voters; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-success" style="font-size: 2.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="text-end">
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Votes Cast</h6>
                                <h2 class="mb-0 fw-bold"><?php echo $total_votes; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-info" style="font-size: 2.5rem;">
                                <i class="fas fa-list-check"></i>
                            </div>
                            <div class="text-end">
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Positions</h6>
                                <h2 class="mb-0 fw-bold"><?php echo $positions_count; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-warning" style="font-size: 2.5rem;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="text-end">
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Candidates</h6>
                                <h2 class="mb-0 fw-bold"><?php echo $candidates_count; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voting Statistics -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card card-elevated">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Voting Turnout</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="mb-0 fw-semibold">Voter Participation</p>
                                <span class="badge bg-success fs-6"><?php echo $vote_percentage; ?>%</span>
                            </div>
                            <div class="progress" style="height: 28px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $vote_percentage; ?>%;" 
                                     aria-valuenow="<?php echo $vote_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <strong class="px-2"><?php echo $vote_percentage; ?>%</strong>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mb-0 mt-3">
                            <i class="fas fa-info-circle me-1"></i>
                            <?php echo $total_votes; ?> out of <?php echo $total_voters; ?> registered voters have participated
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card card-elevated">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="positions.php" class="btn btn-primary w-100">
                                    <i class="fas fa-list-check me-2"></i>Manage Positions
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="candidates.php" class="btn btn-success w-100">
                                    <i class="fas fa-user-tie me-2"></i>Manage Candidates
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="voters.php" class="btn btn-info w-100">
                                    <i class="fas fa-users me-2"></i>Manage Voters
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="results.php" class="btn btn-warning w-100">
                                    <i class="fas fa-chart-bar me-2"></i>View Results
                                </a>
                            </div>
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
