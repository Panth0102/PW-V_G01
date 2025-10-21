<?php
/**
 * Header Include File
 * Common header elements for all pages
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include configuration
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SkillSwap - Modern Learning Platform for Skill Exchange">
    <meta name="keywords" content="skills, learning, exchange, education, community">
    <meta name="author" content="SkillSwap Team">
    
    <!-- Mac-specific meta tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SkillSwap">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/Logo/Dark_Mode_Logo.png">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>
</head>
<body class="<?php echo isset($body_class) ? $body_class : 'theme-dark'; ?>">
