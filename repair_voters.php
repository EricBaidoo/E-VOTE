<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Data Repair Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .container {
            max-width: 900px;
        }
        .tool-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }
        .tool-card:hover {
            transform: translateY(-5px);
        }
        .tool-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .btn-tool {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            margin-top: 15px;
        }
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 40px;
            font-weight: 700;
        }
        .step-badge {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-tools"></i> Voter Data Repair Center</h1>
        
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle"></i> Problem Detected</h5>
            <p><strong>Voters with email/phone in the database CANNOT login</strong></p>
            <p>The issue: Email addresses have line breaks, whitespace, or special characters that prevent login matching.</p>
            <p><strong>Example:</strong> Email in database: <code>user@gmail<br>.com</code> (has line break) → User types: <code>user@gmail.com</code> → Login fails!</p>
            <p><strong>Follow the steps below to fix:</strong></p>
        </div>

        <!-- Step 1: Diagnose -->
        <div class="tool-card">
            <span class="step-badge">STEP 1</span>
            <div class="text-center">
                <i class="fas fa-search tool-icon text-info"></i>
                <h3>Diagnose Issues</h3>
                <p><strong>Find voters who HAVE email/phone but can't login</strong></p>
                <ul class="text-start">
                    <li>Detect emails with line breaks/whitespace</li>
                    <li>Find invalid email formats</li>
                    <li>Show cleaned versions vs. database</li>
                </ul>
                <a href="diagnose_voters.php" class="btn btn-info btn-tool">
                    <i class="fas fa-search"></i> Find Login Issues
                </a>
            </div>
        </div>

        <!-- Step 2: Quick Fix -->
        <div class="tool-card">
            <span class="step-badge">STEP 2</span>
            <div class="text-center">
                <i class="fas fa-magic tool-icon text-success"></i>
                <h3>Auto-Fix Data</h3>
                <p><strong>Clean all email/phone data for login matching</strong></p>
                <ul class="text-start">
                    <li>Remove ALL whitespace and line breaks from emails</li>
                    <li>Remove invisible characters</li>
                    <li>Clean phone numbers for matching</li>
                </ul>
                <a href="fix_voters.php" class="btn btn-success btn-tool">
                    <i class="fas fa-magic"></i> Clean All Email/Phone
                </a>
            </div>
        </div>

        <!-- Step 3: Re-import (if needed) -->
        <div class="tool-card">
            <span class="step-badge">STEP 3 (if needed)</span>
            <div class="text-center">
                <i class="fas fa-file-import tool-icon text-primary"></i>
                <h3>Re-import from CSV</h3>
                <p>Re-import voter data with proper encoding and parsing</p>
                <ul class="text-start">
                    <li>Handle line breaks in emails</li>
                    <li>Fix encoding issues</li>
                    <li>Update all voter records</li>
                </ul>
                <a href="reimport_voters.php" class="btn btn-primary btn-tool">
                    <i class="fas fa-file-import"></i> Re-import Voters
                </a>
            </div>
        </div>

        <!-- Additional Tools -->
        <div class="tool-card bg-light">
            <h4><i class="fas fa-link"></i> Additional Tools</h4>
            <div class="row mt-3">
                <div class="col-md-6 mb-2">
                    <a href="test_login_matching.php" class="btn btn-outline-success w-100">
                        <i class="fas fa-vial"></i> Test Login Matching
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="admin/voters.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-users"></i> View Voters List
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="cleanup_voter_names.php" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-broom"></i> Clean Voter Names
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="admin/dashboard.php" class="btn btn-outline-info w-100">
                        <i class="fas fa-chart-line"></i> Admin Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="tool-card">
            <h4><i class="fas fa-info-circle"></i> How to Use</h4>
            <ol>
                <li><strong>Find Issues</strong> - Click "Find Login Issues" to see voters who have email/phone but can't login</li>
                <li><strong>Clean Data</strong> - Click "Clean All Email/Phone" to remove line breaks and whitespace</li>
                <li><strong>Verify</strong> - Run diagnostic again to confirm all issues are fixed</li>
                <li><strong>Test Login</strong> - Have a voter test their login</li>
            </ol>
            <div class="alert alert-success mt-3">
                <strong>What gets fixed:</strong> 
                <ul class="mb-0">
                    <li>Emails with line breaks: <code>user@gmail\n.com</code> → <code>user@gmail.com</code></li>
                    <li>Emails with spaces: <code>user @ gmail.com</code> → <code>user@gmail.com</code></li>
                    <li>Phones with spaces: <code>123 456 7890</code> → <code>1234567890</code></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
