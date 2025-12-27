<?php
// Quick Setup (JSON-only): No database required.
// This page explains environment requirements and JSON data configuration.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting System - Quick Setup (JSON)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 50px auto;
        }
        .step {
            padding: 15px;
            margin: 15px 0;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            border-radius: 5px;
        }
        .step.completed {
            background-color: #d4edda;
            border-left-color: #28a745;
        }
        code {
            background-color: #f4f4f4;
            padding: 5px 10px;
            border-radius: 3px;
            display: block;
            margin: 10px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand mb-0 h1">E-Voting System - Setup Wizard (JSON)</span>
        </div>
    </nav>

    <div class="setup-container">
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">No Database Needed</h4>
            <p>This system uses JSON files for data storage. You only need PHP and Apache running.</p>
        </div>

        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">E-Voting System Setup Guide (JSON)</h5>
            </div>
            <div class="card-body">
                <h5>Welcome! Let's set up your E-Voting System (JSON)</h5>

                <div class="step">
                    <h6 class="text-primary">Step 1: Verify Hosting is Running</h6>
                    <p>Ensure your hosting web server (Apache/Nginx) is active. MySQL is not required.</p>
                </div>

                <div class="step">
                    <h6 class="text-primary">Step 2: Prepare JSON Data</h6>
                    <p>Confirm files exist in <code>data/</code>:</p>
                    <ul>
                        <li><code>admins.json</code> — admin list (id, name, pin)</li>
                        <li><code>voters.json</code> — voter list (id, name, pin)</li>
                        <li><code>aspirants.json</code> — candidates (id, name, pin, position, image)</li>
                        <li><code>positions_config.json</code> — seat counts per position</li>
                        <li><code>votes.json</code> — will be created automatically when voting starts</li>
                    </ul>
                </div>

                <div class="step">
                    <h6 class="text-primary">Step 3: Login</h6>
                    <p>Use Name + PIN to login:</p>
                    <ul>
                        <li><strong>Admin:</strong> Name: <code>Admin User</code>, PIN: <code>admin123</code></li>
                        <li><strong>Voter:</strong> Name: from <code>voters.json</code>, PIN: corresponding <code>pinXXXX</code></li>
                    </ul>
                </div>

                <div class="step">
                    <h6 class="text-primary">Step 4: Access the System</h6>
                    <p>Once setup is complete, you can access:</p>
                    <ul>
                        <li><strong>Home:</strong> https://your-domain.com/</li>
                        <li><strong>Login:</strong> https://your-domain.com/login.php</li>
                        <li><strong>Admin:</strong> https://your-domain.com/admin/dashboard.php</li>
                        <li><strong>Voter:</strong> https://your-domain.com/voter/dashboard.php</li>
                    </ul>
                </div>

                <div class="alert alert-info mt-4">
                    <h6>Default Access</h6>
                    <p>Admin: <code>Admin User</code> / <code>admin123</code>. Voters: use entries from <code>data/voters.json</code> with their PINs.</p>
                </div>

                <div class="alert alert-warning mt-3">
                    <strong>⚠️ Important:</strong>
                    <ul class="mb-0">
                        <li>Change default PINs in production</li>
                        <li>Keep backups of your <code>data/</code> folder</li>
                        <li>For help, see the README.md file</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Go to Home
                    </a>
                    <a href="setup.php" class="btn btn-secondary">
                        <i class="fas fa-book"></i> Detailed Setup Guide (JSON)
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-lg mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Component</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td><strong>PHP Version</strong></td>
                        <td><?php echo phpversion(); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Session Support</strong></td>
                        <td><?php echo session_status() === PHP_SESSION_ACTIVE ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-warning">Inactive</span>'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Server</strong></td>
                        <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Installation Path</strong></td>
                        <td><?php echo realpath('.'); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 E-Voting System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
