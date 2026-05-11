<?php
$page_title = 'Home';
require_once 'config.php';
require_once 'includes/header.php';
?>

<div class="hero">
    <div class="hero-tag">SWE 322 &ndash; Advanced Web Programming</div>
    <h1>Gamer <span class="hl">Profile</span> &amp;<br>Performance Analysis</h1>
    <p>Track gameplay stats, analyze your performance, compete on the leaderboard, and earn achievement badges.</p>
    <div class="hero-btns">
        <a href="profiles.php"   class="btn btn-primary"><i class="fa-solid fa-user"></i> View Profiles</a>
        <a href="leaderboard.php" class="btn btn-outline"><i class="fa-solid fa-trophy"></i> Leaderboard</a>
        <a href="dashboard.php"  class="btn btn-cyan"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    </div>
</div>

<div class="feature-grid">
    <a href="profiles.php" class="feat-card">
        <div class="feat-icon"><i class="fa-solid fa-user"></i></div>
        <div class="feat-name">Gamer Profiles</div>
        <div class="feat-desc">Create and manage profiles with rank, role, level, XP, and bio.</div>
    </a>
    <a href="statistics.php" class="feat-card">
        <div class="feat-icon"><i class="fa-solid fa-chart-bar"></i></div>
        <div class="feat-name">Statistics</div>
        <div class="feat-desc">Full CRUD for gameplay sessions: kills, deaths, wins, accuracy, playtime.</div>
    </a>
    <a href="match_history.php" class="feat-card">
        <div class="feat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <div class="feat-name">Match History</div>
        <div class="feat-desc">Browse all sessions in chronological order with profile filter.</div>
    </a>
    <a href="dashboard.php" class="feat-card">
        <div class="feat-icon"><i class="fa-solid fa-gauge-high"></i></div>
        <div class="feat-name">Dashboard</div>
        <div class="feat-desc">Automated K/D ratio, win rate, performance tier, and improvement tips.</div>
    </a>
    <a href="leaderboard.php" class="feat-card">
        <div class="feat-icon"><i class="fa-solid fa-trophy"></i></div>
        <div class="feat-name">Leaderboard</div>
        <div class="feat-desc">See who ranks at the top based on composite score.</div>
    </a>
    <a href="achievements.php" class="feat-card">
        <div class="feat-icon"><i class="fa-solid fa-medal"></i></div>
        <div class="feat-name">Achievements</div>
        <div class="feat-desc">Badge system for milestones: First Win, 100 Kills, Elite Player, and more.</div>
    </a>
</div>

<?php require_once 'includes/footer.php'; ?>
