<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/json_utils.php';

require_login('admin');

$admin = $_SESSION['username'];

// Load voters from JSON and build vote status map
$voters = json_load('voters.json');
$votes = json_load('votes.json');
$voteMap = [];
foreach ($votes as $v) {
    $uid = $v['voter_session_id'] ?? null;
    $ts = $v['timestamp'] ?? null;
    if ($uid) {
        if (!isset($voteMap[$uid]) || strtotime($ts) > strtotime($voteMap[$uid])) {
            $voteMap[$uid] = $ts;
        }
    }
}

$total_voters = is_array($voters) ? count($voters) : 0;
$voted_count = 0;
if (is_array($voters)) {
    foreach ($voters as $v) {
        if (isset($voteMap[(string)($v['id'] ?? '')])) { $voted_count++; }
    }
}
$pending_count = max(0, $total_voters - $voted_count);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voters - <?php echo SITE_TITLE; ?></title>
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="positions.php">Positions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="candidates.php">Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="voters.php">Voters</a>
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
        <div class="row">
            <div class="col-lg-11 mx-auto">
                <div class="mb-4">
                    <h2 class="mb-1"><i class="fas fa-users me-2"></i>Manage Voters</h2>
                    <p class="text-muted">Overview of registered voters and their voting status</p>
                </div>

                <!-- Statistics -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-4">
                        <div class="card border-left-primary shadow h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="text-primary" style="font-size: 2.5rem;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Voters</h6>
                                        <h2 class="mb-0 fw-bold"><?php echo $total_voters; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-left-success shadow h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="text-success" style="font-size: 2.5rem;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Voted</h6>
                                        <h2 class="mb-0 fw-bold"><?php echo $voted_count; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-left-warning shadow h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="text-warning" style="font-size: 2.5rem;">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Pending</h6>
                                        <h2 class="mb-0 fw-bold"><?php echo $pending_count; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-elevated">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Voters List</h5>
                    </div>
                    <div class="card-body">
                        <?php if (is_array($voters) && count($voters) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Voter ID</th>
                                            <th>Status</th>
                                            <th>Vote Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($voters as $voter): ?>
                                            <tr>
                                                <td><strong><?php echo $voter['name'] ?? 'Voter'; ?></strong></td>
                                                <td><code><?php echo $voter['id'] ?? 'N/A'; ?></code></td>
                                                <td>
                                                    <?php $vid = (string)($voter['id'] ?? ''); $vt = $voteMap[$vid] ?? null; ?>
                                                    <?php if ($vt): ?>
                                                        <span class="badge bg-success">✓ Voted</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo isset($vt) ? format_date($vt) : '-'; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No voters registered yet.
                            </div>
                        <?php endif; ?>
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
