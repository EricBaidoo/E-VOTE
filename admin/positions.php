<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/json_utils.php';

require_login('admin');

$admin = $_SESSION['username'];
$message = '';
$error = '';

// JSON-backed: update seat counts only
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cfg = get_positions_config();
    if (isset($_POST['seats']) && is_array($_POST['seats'])) {
        foreach ($_POST['seats'] as $name => $count) {
            $nameNorm = normalize_position($name);
            $cfg[$nameNorm] = max(1, (int)$count);
        }
        if (json_save('positions_config.json', $cfg)) {
            $message = 'Position seat counts updated.';
        } else {
            $error = 'Failed to save seat counts.';
        }
    }
}

$positions = derive_positions_from_aspirants();
$cfg = get_positions_config();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Positions - <?php echo SITE_TITLE; ?></title>
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
                        <a class="nav-link active" href="positions.php">Positions</a>
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
        <div class="row">
            <div class="col-lg-11 mx-auto">
                <div class="mb-4">
                    <h2 class="mb-1"><i class="fas fa-list-check me-2"></i>Manage Positions</h2>
                    <p class="text-muted">Set seat counts per category</p>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card border-left-info shadow">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="text-info" style="font-size: 2rem;">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                </div>
                                <h5 class="mb-3">Position Information</h5>
                                <div class="alert alert-info mb-0">
                                    <p class="mb-0"><i class="fas fa-database me-2"></i>Positions are derived from aspirants in <code>data/aspirants.json</code>. Use the table to set seat counts per position.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card card-elevated">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-chair me-2"></i>Positions & Seat Counts</h5>
                            </div>
                            <div class="card-body">
                                <?php if (count($positions) > 0): ?>
                                    <form method="POST">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Position</th>
                                                        <th>Seat Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($positions as $posName): ?>
                                                        <tr>
                                                            <td><strong><?php echo $posName; ?></strong></td>
                                                            <td style="max-width:140px">
                                                                <input type="number" class="form-control" name="seats[<?php echo $posName; ?>]" value="<?php echo isset($cfg[$posName]) ? (int)$cfg[$posName] : 1; ?>" min="1">
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Seat Counts</button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No positions derived from aspirants yet.
                                    </div>
                                <?php endif; ?>
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
