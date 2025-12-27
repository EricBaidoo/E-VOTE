<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/json_utils.php';

require_login('voter');

$voter_id = (string)$_SESSION['user_id'];
$voters = json_load('voters.json');
$votes = json_load('votes.json');

// Find voter record by id
$voter = null;
foreach ($voters as $v) {
    if ((string)($v['id'] ?? '') === $voter_id) { $voter = $v; break; }
}

// Determine voting status and last vote time
$has_voted_flag = false;
$vote_time = null;
foreach ($votes as $vt) {
    if (($vt['voter_session_id'] ?? null) === $voter_id) {
        $has_voted_flag = true;
        $ts = $vt['timestamp'] ?? null;
        if ($ts && (!$vote_time || strtotime($ts) > strtotime($vote_time))) { $vote_time = $ts; }
    }
}

// Get total votes and voters
$total_votes = get_total_votes_cast();
$total_voters = get_total_registered_voters();
$vote_percentage = ($total_voters > 0) ? round(($total_votes / $total_voters) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard - <?php echo SITE_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-vote-yea"></i> <?php echo SITE_TITLE; ?>
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
                        <a class="nav-link" href="vote.php">Vote</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="results.php">Results</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $voter['name'] ?? 'Voter'; ?>
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Voter Dashboard</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p><strong>Name:</strong> <?php echo $voter['name'] ?? 'Voter'; ?></p>
                                <p><strong>Voter ID:</strong> <?php echo $voter['id'] ?? '-'; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p><strong>Voting Status:</strong> 
                                    <?php if ($has_voted_flag): ?>
                                        <span class="badge bg-success">✓ Voted</span>
                                        <?php if ($vote_time): ?><small class="d-block text-muted">on <?php echo format_date($vote_time); ?></small><?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-left-primary shadow">
                            <div class="card-body">
                                <div class="text-primary" style="font-size: 2rem;">
                                    <i class="fas fa-ballot-check"></i>
                                </div>
                                <h6 class="card-title mt-2">Total Votes Cast</h6>
                                <p class="card-text" style="font-size: 1.5rem;">
                                    <strong><?php echo $total_votes; ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-success shadow">
                            <div class="card-body">
                                <div class="text-success" style="font-size: 2rem;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h6 class="card-title mt-2">Registered Voters</h6>
                                <p class="card-text" style="font-size: 1.5rem;">
                                    <strong><?php echo $total_voters; ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Voting Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1"><strong>Turnout: <span class="float-end"><?php echo $vote_percentage; ?>%</span></strong></p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $vote_percentage; ?>%" 
                                     aria-valuenow="<?php echo $vote_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <small class="text-muted"><?php echo $total_votes; ?> out of <?php echo $total_voters; ?> voters have participated</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?php if (!$has_voted_flag): ?>
                            <a href="vote.php" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-vote-yea"></i> Cast Your Vote
                            </a>
                        <?php else: ?>
                            <button class="btn btn-success btn-lg w-100" disabled>
                                <i class="fas fa-check-circle"></i> Already Voted
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="results.php" class="btn btn-info btn-lg w-100">
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
