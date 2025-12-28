<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db_connect.php';

require_login('voter');

$voter_id = (string)$_SESSION['user_id'];

// Get voter record from MySQL
$stmt = $conn->prepare("SELECT id, name, email, phone FROM voters WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$voter = $result->fetch_assoc();
$stmt->close();

// Get voting status and time from MySQL
$stmt = $conn->prepare("SELECT MAX(timestamp) as vote_time FROM votes WHERE voter_session_id = ?");
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$result = $stmt->get_result();
$vote_data = $result->fetch_assoc();
$stmt->close();

$has_voted_flag = !empty($vote_data['vote_time']);
$vote_time = $vote_data['vote_time'];

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

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-lg-9 mx-auto">
                <div class="mb-4">
                    <h2 class="mb-1"><i class="fas fa-tachometer-alt me-2"></i>Voter Dashboard</h2>
                    <p class="text-muted">Welcome, <?php echo $voter['name'] ?? 'Voter'; ?></p>
                </div>

                <div class="card card-elevated mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Your Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="mb-2"><strong class="text-muted">Name:</strong></p>
                                <p class="mb-0 fs-5"><?php echo $voter['name'] ?? 'Voter'; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2"><strong class="text-muted">Voter ID:</strong></p>
                                <p class="mb-0 fs-5"><code class="fs-5"><?php echo $voter['id'] ?? '-'; ?></code></p>
                            </div>
                            <div class="col-12">
                                <p class="mb-2"><strong class="text-muted">Voting Status:</strong></p>
                                <p class="mb-0">
                                    <?php if ($has_voted_flag): ?>
                                        <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i>Voted</span>
                                        <?php if ($vote_time): ?><small class="d-block text-muted mt-2">Voted on <?php echo format_date($vote_time); ?></small><?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning px-3 py-2"><i class="fas fa-clock me-1"></i>Pending</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-left-primary shadow h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="text-primary" style="font-size: 2.5rem;">
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
                    <div class="col-md-6">
                        <div class="card border-left-success shadow h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="text-success" style="font-size: 2.5rem;">
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
                </div>

                <div class="card card-elevated mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Voting Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="mb-0 fw-semibold">Turnout</p>
                                <span class="badge bg-success fs-6"><?php echo $vote_percentage; ?>%</span>
                            </div>
                            <div class="progress" style="height: 28px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $vote_percentage; ?>%" 
                                     aria-valuenow="<?php echo $vote_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <strong class="px-2"><?php echo $vote_percentage; ?>%</strong>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mb-0 mt-3">
                            <i class="fas fa-info-circle me-1"></i>
                            <?php echo $total_votes; ?> out of <?php echo $total_voters; ?> voters have participated
                        </p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <?php if (!$has_voted_flag): ?>
                            <a href="vote.php" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-vote-yea me-2"></i>Cast Your Vote
                            </a>
                        <?php else: ?>
                            <button class="btn btn-success btn-lg w-100" disabled>
                                <i class="fas fa-check-circle me-2"></i>Already Voted
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <a href="results.php" class="btn btn-info btn-lg w-100">
                            <i class="fas fa-chart-bar me-2"></i>View Results
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
</body>
</html>
