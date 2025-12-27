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
foreach ($voters as $v) { if ((string)($v['id'] ?? '') === $voter_id) { $voter = $v; break; } }

// Voting status and time
$has_voted_flag = false; $vote_time = null;
foreach ($votes as $vt) {
    if (($vt['voter_session_id'] ?? null) === $voter_id) {
        $has_voted_flag = true;
        $ts = $vt['timestamp'] ?? null;
        if ($ts && (!$vote_time || strtotime($ts) > strtotime($vote_time))) { $vote_time = $ts; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_TITLE; ?></title>
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vote.php">Vote</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="results.php">Results</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $voter['name'] ?? 'Voter'; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userMenu">
                            <li><a class="dropdown-item active" href="profile.php">Profile</a></li>
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
            <div class="col-lg-6 mx-auto">
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-circle"></i> My Profile</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            <div style="font-size: 4rem; color: #007bff;">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        </div>

                        <table class="table">
                            <tr>
                                <th>Name:</th>
                                <td><?php echo $voter['name'] ?? 'Voter'; ?></td>
                            </tr>
                            <tr>
                                <th>Voter ID:</th>
                                <td><?php echo $voter['id'] ?? '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Voting Status:</th>
                                <td>
                                    <?php if ($has_voted_flag): ?>
                                        <span class="badge bg-success">✓ Voted</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if ($has_voted_flag && $vote_time): ?>
                            <tr>
                                <th>Vote Time:</th>
                                <td><?php echo format_date($vote_time); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
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
