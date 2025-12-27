<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/json_utils.php';

require_login('admin');

$admin = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$message = '';
$error = '';

// JSON-backed list of positions and aspirants
$positions = derive_positions_from_aspirants();
$asp_by_pos = group_aspirants_by_position();

// Handle form submission
// This page is now read-only; candidates are defined by data/aspirants.json

// Get all candidates with position names
// Build a flat list for table display
$candidates = [];
foreach ($asp_by_pos as $pos => $list) {
    foreach ($list as $cand) {
        $candidates[] = [
            'position_name' => $pos,
            'candidate_name' => $cand['name'],
            'image' => isset($cand['image']) ? $cand['image'] : '',
            'id' => $cand['id']
        ];
    }
}

// Sort candidates by custom position order
$position_order = [
    'President' => 1,
    'Vice' => 2,
    'Vice President' => 2,
    'Secretary' => 3,
    'Treasurer' => 4,
    'Organizer' => 5,
    'Male Executive Member' => 6,
    'Female Executive Member' => 7,
    'Executive Members' => 8,
    'Executive Member' => 8,
    'Executive' => 8
];

usort($candidates, function($a, $b) use ($position_order) {
    $aOrder = $position_order[$a['position_name']] ?? 999;
    $bOrder = $position_order[$b['position_name']] ?? 999;
    if ($aOrder !== $bOrder) {
        return $aOrder - $bOrder;
    }
    return strcasecmp($a['candidate_name'], $b['candidate_name']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates - <?php echo SITE_TITLE; ?></title>
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
                        <a class="nav-link active" href="candidates.php">Candidates</a>
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
                    <h2 class="mb-1"><i class="fas fa-user-tie me-2"></i>Manage Candidates</h2>
                    <p class="text-muted">Read-only from aspirants.json</p>
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
                                <h5 class="mb-3">Candidates Source</h5>
                                <div class="alert alert-info mb-0">
                                    <p class="mb-0"><i class="fas fa-database me-2"></i>This list is read from <code>data/aspirants.json</code>. To update candidates, edit that file (id, name, pin, position, image).</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card card-elevated">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>All Candidates</h5>
                            </div>
                            <div class="card-body">
                                <?php if (count($candidates) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Position</th>
                                                    <th>Candidate</th>
                                                    <th>Image</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($candidates as $candidate): ?>
                                                    <tr>
                                                        <td><strong><?php echo $candidate['position_name']; ?></strong></td>
                                                        <td><?php echo $candidate['candidate_name']; ?></td>
                                                        <td>
                                                            <?php if (!empty($candidate['image'])): ?>
                                                                <img class="img-fluid rounded-circle" src="../<?php echo $candidate['image']; ?>" alt="<?php echo $candidate['candidate_name']; ?>" style="max-height:56px"/>
                                                            <?php else: ?>
                                                                <span class="text-muted">No image</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No candidates found yet.
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
