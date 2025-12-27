<?php
require_once 'includes/config.php';
$base = rtrim(SITE_URL, '/');
$target = $base ? $base . '/login.php' : 'login.php';
header('Location: ' . $target);
exit;
