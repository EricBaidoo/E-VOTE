<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/json_utils.php';

require_login('voter');

// Session-based voter identifier
$session_voter_id = isset($_SESSION['user_id']) ? (string)$_SESSION['user_id'] : session_id();
// Basic display name fallback
$display_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Voter';

// JSON-based: check if already voted
$already_voted = has_voted_json($session_voter_id);
if ($already_voted) {
    $error = "You have already cast your vote!";
}

// Load positions and candidates from JSON
$positions_config = get_positions_config();
$aspirants_by_pos = group_aspirants_by_position();
$positions = array_keys($aspirants_by_pos);
sort($positions);

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$already_voted) {
    $votes = $_POST['votes'] ?? [];
    // Normalize votes: ensure each position has an array of selected ids
    $normalized = [];
    $errors = [];
    foreach ($positions as $pos) {
        $needed = isset($positions_config[$pos]) ? (int)$positions_config[$pos] : 1;
        $selected = $votes[$pos] ?? [];
        if (!is_array($selected)) { $selected = $selected ? [$selected] : []; }
        if (count($selected) != $needed) {
            $errors[] = "$pos requires $needed selection(s).";
        }
        $normalized[$pos] = $selected;
    }
    if (empty($errors)) {
        if (record_vote_json($session_voter_id, $normalized)) {
            header('Location: vote_success.php');
            exit;
        } else {
            $error = "Error casting vote. Please try again!";
        }
    } else {
        $error = implode(' ', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Your Vote - <?php echo SITE_TITLE; ?></title>
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
                        <a class="nav-link active" href="vote.php">Vote</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="results.php">Results</a>
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
            <div class="col-lg-9 mx-auto">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php if ($already_voted): ?>
                        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="page-header mb-4">
                        <div class="container">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h4 class="mb-1"><i class="fas fa-vote-yea"></i> Cast Your Vote</h4>
                                    <p class="mb-0">Select candidates per category. Exact selections required per seat count.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-elevated mb-4">
                        <div class="card-body">

                            <form method="POST" id="voteForm">
                                <?php foreach ($positions as $position): ?>
                                    <?php $candidates = $aspirants_by_pos[$position] ?? []; $maxSel = isset($positions_config[$position]) ? (int)$positions_config[$position] : 1; ?>
                                    <div class="card mb-4 border-left-primary">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0"><?php echo $position; ?></h6>
                                                <span class="badge bg-primary">Seats: <?php echo $maxSel; ?></span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <?php if (count($candidates) > 0): ?>
                                                <div class="position-group" data-max-select="<?php echo $maxSel; ?>">
                                                    <div class="row g-3">
                                                    <?php foreach ($candidates as $candidate): ?>
                                                        <div class="col-12 col-sm-6 col-lg-4">
                                                        <div class="form-check candidate-option p-3 border rounded h-100">
                                                            <?php if ($maxSel === 1): ?>
                                                                <input class="form-check-input" type="radio" 
                                                                       name="votes[<?php echo $position; ?>]" 
                                                                       id="candidate_<?php echo $candidate['id']; ?>"
                                                                       value="<?php echo $candidate['id']; ?>" required>
                                                            <?php else: ?>
                                                                <input class="form-check-input" type="checkbox" 
                                                                       name="votes[<?php echo $position; ?>][]" 
                                                                       id="candidate_<?php echo $candidate['id']; ?>"
                                                                       value="<?php echo $candidate['id']; ?>">
                                                            <?php endif; ?>
                                                            <label class="form-check-label w-100" for="candidate_<?php echo $candidate['id']; ?>">
                                                                <div class="candidate-card">
                                                                    <?php if (!empty($candidate['image'])): ?>
                                                                        <img class="candidate-avatar img-fluid" src="../<?php echo $candidate['image']; ?>" alt="<?php echo $candidate['name']; ?>" />
                                                                    <?php else: ?>
                                                                        <div class="candidate-avatar bg-light d-flex align-items-center justify-content-center">
                                                                            <i class="fas fa-user"></i>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div>
                                                                        <strong><?php echo $candidate['name']; ?></strong>
                                                                        <div class="text-muted">Aspirant for <?php echo $position; ?></div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-warning mb-0">No candidates available for this position.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div class="form-check mb-4 p-3 border border-danger rounded">
                                    <input class="form-check-input" type="checkbox" id="confirmVote" required>
                                    <label class="form-check-label" for="confirmVote">
                                        I confirm that I have selected my choices correctly and this is my final vote.
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-check-circle"></i> Submit Vote
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="dashboard.php" class="btn btn-secondary btn-lg w-100">
                                            <i class="fas fa-times-circle"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 E-Voting System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="../js/voting.js"></script>
</body>
</html>
