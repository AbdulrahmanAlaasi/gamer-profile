<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gamer_profile_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<!DOCTYPE html><html><head><title>DB Error</title>
    <style>body{background:#0a0a14;color:#ff4466;font-family:monospace;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
    .box{text-align:center;padding:40px;border:1px solid #ff4466;border-radius:12px;max-width:500px;}
    p{color:#8888aa;margin-top:12px;}</style></head><body>
    <div class="box"><h2>&#9888; Database Connection Failed</h2>
    <p>' . htmlspecialchars($conn->connect_error) . '</p>
    <p>Make sure XAMPP MySQL is running and import <strong>database/gamer_profile_db.sql</strong> in phpMyAdmin.</p>
    </div></body></html>');
}

$conn->set_charset('utf8mb4');

function avatarColor($name) {
    $colors = ['#6c63ff','#ff6584','#00d4ff','#00ff88','#ffaa00','#ff4466','#a855f7','#06b6d4'];
    return $colors[ord($name[0] ?? 'A') % count($colors)];
}

function initials($name) {
    $name = trim($name);
    $parts = preg_split('/\s+/', $name);
    if (count($parts) >= 2) return strtoupper(substr($parts[0],0,1) . substr($parts[1],0,1));
    return strtoupper(substr($name, 0, 2));
}

function getTier($kd, $winRate) {
    if ($kd >= 2.0 && $winRate >= 60) return ['Elite', 'tier-elite'];
    if ($kd >= 1.5 && $winRate >= 45) return ['Advanced', 'tier-advanced'];
    if ($kd >= 1.0) return ['Intermediate', 'tier-intermediate'];
    return ['Beginner', 'tier-beginner'];
}

function xpPercent($xp) {
    return min(100, ($xp % 1000) / 10);
}
?>
