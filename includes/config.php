<?php
// Configuration File
define('SITE_TITLE', 'AMOSA GLOBAL ELECTION');
// Leave empty to use relative paths on any host (e.g., Hostinger)
define('SITE_URL', '');
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Admin credentials (for initial setup)
define('ADMIN_DEFAULT_USERNAME', 'admin');
define('ADMIN_DEFAULT_PASSWORD', 'admin123');

// Voting settings
define('VOTING_ACTIVE', true);
define('ALLOW_VOTER_REGISTRATION', true);
define('REQUIRE_EMAIL_VERIFICATION', false);

// Security
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Email Configuration
define('EMAIL_FROM', 'noreply@evoting.com');
define('EMAIL_HOST', 'localhost');
?>
