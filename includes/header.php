<?php
$cp = basename($_SERVER['PHP_SELF'], '.php');
function isActive($pages, $cp) {
    return in_array($cp, (array)$pages) ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>GamerVault</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-brand"><i class="fa-solid fa-bolt"></i> GamerVault</a>
        <ul class="nav-links">
            <li><a href="index.php"<?= isActive('index', $cp) ?>>Home</a></li>
            <li><a href="profiles.php"<?= isActive(['profiles','profile_detail','profile_add','profile_edit'], $cp) ?>>Profiles</a></li>
            <li><a href="statistics.php"<?= isActive(['statistics','stat_add','stat_edit'], $cp) ?>>Statistics</a></li>
            <li><a href="match_history.php"<?= isActive('match_history', $cp) ?>>Match History</a></li>
            <li><a href="dashboard.php"<?= isActive('dashboard', $cp) ?>>Dashboard</a></li>
            <li><a href="leaderboard.php"<?= isActive('leaderboard', $cp) ?>>Leaderboard</a></li>
            <li><a href="achievements.php"<?= isActive('achievements', $cp) ?>>Achievements</a></li>
        </ul>
    </div>
</nav>
<main class="main-content">
