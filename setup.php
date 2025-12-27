<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Guide - E-Voting System (JSON)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-vote-yea"></i> E-Voting System - Setup
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Setup Instructions (No Database)</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>Follow these steps to set up the E-Voting System:</h6>
                        </div>

                        <h5 class="mt-4">Step 1: Start XAMPP (Apache)</h5>
                        <ol>
                            <li>Open the XAMPP Control Panel</li>
                            <li>Start <strong>Apache</strong> (MySQL is not required)</li>
                        </ol>

                        <h5 class="mt-4">Step 2: Verify JSON Data</h5>
                        <p>The system uses JSON files in the <code>data/</code> folder:</p>
                        <ul>
                            <li><code>data/admins.json</code> — Admin accounts (id, name, pin)</li>
                            <li><code>data/voters.json</code> — Voter list (id, name, pin)</li>
                            <li><code>data/aspirants.json</code> — Candidates (id, name, pin, position, image)</li>
                            <li><code>data/positions_config.json</code> — Seat counts per position</li>
                            <li><code>data/votes.json</code> — Recorded votes (created automatically)</li>
                        </ul>

                        <h5 class="mt-4">Step 3: Login Credentials (JSON)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Role</th>
                                    <th>Name</th>
                                    <th>PIN</th>
                                </tr>
                                <tr>
                                    <td><strong>Admin</strong></td>
                                    <td>Admin User</td>
                                    <td>admin123</td>
                                </tr>
                                <tr>
                                    <td><strong>Voter</strong></td>
                                    <td>Sample Voter Name</td>
                                    <td>pinXXXX</td>
                                </tr>
                            </table>
                        </div>

                        <h5 class="mt-4">Step 4: Access the Application</h5>
                        <ol>
                            <li>Open your browser</li>
                            <li>Navigate to: <strong>http://localhost/E-VOTE</strong></li>
                            <li>You should see the home page</li>
                            <li>Click "Login" to enter the system</li>
                        </ol>

                        <h5 class="mt-4">Step 5: Manage Data</h5>
                        <p>Edit the JSON files in <code>data/</code> to manage admins, voters, and aspirants. You can also adjust seat counts in <code>data/positions_config.json</code>.</p>

                        <div class="alert alert-warning mt-4">
                            <h6 class="mb-2">⚠️ Important Notes:</h6>
                            <ul class="mb-0">
                                <li>Make sure XAMPP is running (Apache only)</li>
                                <li>No database required — data is stored in JSON files</li>
                                <li>All default PINs are for demo purposes — change them in production</li>
                                <li>Admin panel is only accessible with admin credentials</li>
                            </ul>
                        </div>

                        <div class="alert alert-success mt-4">
                            <h6 class="mb-2">✓ Features Available:</h6>
                            <ul class="mb-0">
                                <li>Admin Dashboard - Manage positions, candidates, and voters</li>
                                <li>Voter Registration and Authentication</li>
                                <li>Secure Voting Interface</li>
                                <li>Real-time Election Results</li>
                                <li>Voting Turnout Statistics</li>
                                <li>Voter Management System</li>
                                <li>Responsive Design with Bootstrap</li>
                            </ul>
                        </div>

                        <a href="index.php" class="btn btn-primary btn-lg mt-4">
                            <i class="fas fa-home"></i> Go to Home Page
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
