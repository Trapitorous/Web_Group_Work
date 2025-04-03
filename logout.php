<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>