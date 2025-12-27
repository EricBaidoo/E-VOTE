<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/json_utils.php';

require_login('voter');

// Basic display name fallback
$display_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Voter';

// Build results from JSON
$tally = tally_results();
$asp_by_pos = group_aspirants_by_position();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results - <?php echo SITE_TITLE; ?></title>
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
                        <a class="nav-link active" href="results.php">Results</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($display_name); ?>
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

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="page-header mb-4">
                    <div class="container">
                        <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Election Results</h4>
                        <small class="text-white-50">Live tallies per category</small>
                    </div>
                </div>

                <?php foreach ($asp_by_pos as $position => $candidates): ?>
                    <div class="card card-elevated mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><?php echo $position; ?></h6>
                        </div>
                        <div class="card-body">
                            <?php 
                            // Build per-candidate vote counts
                            $counts = [];
                            $total_position_votes = 0;
                            foreach ($candidates as $cand) {
                                $id = $cand['id'];
                                $count = isset($tally[$position][$id]) ? (int)$tally[$position][$id] : 0;
                                $counts[$id] = $count;
                                $total_position_votes += $count;
                            }
                            // Sort candidates by count desc
                            usort($candidates, function($a, $b) use ($counts){
                                return ($counts[$b['id']] ?? 0) <=> ($counts[$a['id']] ?? 0);
                            });
                            ?>
                            <?php foreach ($candidates as $index => $cand): ?>
                                <?php $vote_count = $counts[$cand['id']] ?? 0; $percentage = ($total_position_votes > 0) ? round(($vote_count / $total_position_votes) * 100, 2) : 0; ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                <?php if (!empty($cand['image'])): ?>
                                                    <img class="candidate-avatar img-fluid" src="../<?php echo $cand['image']; ?>" alt="<?php echo $cand['name']; ?>" />
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?php echo $cand['name']; ?></h6>
                                                    <small class="text-muted">Aspirant</small>
                                                </div>
                                            </div>
                                            <?php if ($index == 0 && $vote_count > 0): ?>
                                                <span class="badge bg-gold ms-2">
                                                    <i class="fas fa-crown"></i> Leading
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-end">
                                            <strong><?php echo $vote_count; ?> votes</strong>
                                            <br>
                                            <small class="text-muted"><?php echo $percentage; ?>%</small>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $percentage; ?>%;" 
                                             aria-valuenow="<?php echo $percentage; ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            <span class="progress-text"><?php echo $percentage; ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Total Votes:</strong> <?php echo $total_position_votes; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="row mt-4 mb-5">
                    <div class="col-md-6">
                        <a href="dashboard.php" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
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
