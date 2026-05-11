<?php
$page_title = 'Performance Dashboard';
require_once 'config.php';

$result = $conn->query(
    "SELECT p.id, p.gamer_name, p.favorite_game, p.level,
            COALESCE(SUM(s.kills),0)          AS tk,
            COALESCE(SUM(s.deaths),0)         AS td,
            COALESCE(SUM(s.wins),0)           AS tw,
            COALESCE(SUM(s.matches_played),0) AS tm,
            COALESCE(AVG(s.accuracy),0)       AS aa,
            COALESCE(SUM(s.playtime),0)       AS tp,
            COUNT(s.id)                       AS sessions
     FROM profiles p
     LEFT JOIN statistics s ON s.profile_id = p.id
     GROUP BY p.id, p.gamer_name, p.favorite_game, p.level
     ORDER BY tk DESC"
);

$profiles = [];
while ($row = $result->fetch_assoc()) {
    $kd  = $row['td'] > 0 ? round($row['tk'] / $row['td'], 2) : (float)$row['tk'];
    $wr  = $row['tm'] > 0 ? round(($row['tw'] / $row['tm']) * 100, 1) : 0;
    $acc = round($row['aa'], 1);
    [$tier, $tier_class] = getTier($kd, $wr);
    $profiles[] = array_merge($row, [
        'kd' => $kd, 'wr' => $wr, 'acc' => $acc,
        'tier' => $tier, 'tier_class' => $tier_class,
    ]);
}

function getTips($tier) {
    if ($tier === 'Elite') return [
        'Focus on consistency &mdash; maintain your K/D across all sessions.',
        'Consider leading your team to improve overall coordination.',
        'Keep your accuracy above 70% to stay at the top.',
    ];
    if ($tier === 'Advanced') return [
        'Push your win rate past 60% to reach Elite tier.',
        'Work on accuracy &mdash; aim for above 75% per session.',
        'Play more coordinated matches to boost your win rate.',
    ];
    if ($tier === 'Intermediate') return [
        'Improve your K/D by playing a safer, less risky style.',
        'Practice aim training drills to raise accuracy.',
        'Focus on team objectives, not just kill count.',
    ];
    return [
        'Start with aim training exercises to build core skills.',
        'Watch replays of your sessions to identify mistakes.',
        'Focus on surviving longer rather than chasing kills.',
        'Play with experienced teammates to learn faster.',
    ];
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <div class="page-title">Performance <span>Dashboard</span></div>
        <div class="page-sub">Automated analysis for all gamers</div>
    </div>
</div>

<?php if (empty($profiles)): ?>
<div class="empty">
    <i class="fa-solid fa-gauge-high" style="font-size:2.8rem;color:var(--dim);display:block;margin-bottom:12px"></i>
    <p>No data yet. <a href="profile_add.php" style="color:var(--purple)">Add profiles</a> and <a href="stat_add.php" style="color:var(--purple)">log sessions</a> first.</p>
</div>
<?php else: ?>

<div class="dash-grid">
<?php foreach ($profiles as $p): ?>
<div class="card">
    <div class="card-head" style="flex-wrap:wrap;gap:8px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="avatar" style="width:40px;height:40px;font-size:0.9rem;background:<?= avatarColor($p['gamer_name']) ?>"><?= initials($p['gamer_name']) ?></div>
            <div>
                <div style="font-weight:700;font-size:0.95rem;"><?= htmlspecialchars($p['gamer_name']) ?></div>
                <div style="font-size:0.75rem;color:var(--muted);"><i class="fa-solid fa-gamepad"></i> <?= htmlspecialchars($p['favorite_game']) ?></div>
            </div>
        </div>
        <span class="tier <?= $p['tier_class'] ?>"><?= $p['tier'] ?></span>
    </div>
    <div class="card-body">
        <div class="ds-row">
            <div class="ds">
                <div class="ds-val" style="color:var(--purple)"><?= $p['kd'] ?></div>
                <div class="ds-lbl">K/D</div>
            </div>
            <div class="ds">
                <div class="ds-val" style="color:var(--green)"><?= $p['wr'] ?>%</div>
                <div class="ds-lbl">Win Rate</div>
            </div>
            <div class="ds">
                <div class="ds-val" style="color:var(--cyan)"><?= $p['acc'] ?>%</div>
                <div class="ds-lbl">Accuracy</div>
            </div>
        </div>
        <div class="perf-row">
            <div class="perf-label"><span>Win Rate</span><span><?= $p['wr'] ?>%</span></div>
            <div class="perf-track"><div class="perf-fill" data-width="<?= min(100,$p['wr']) ?>" style="width:0%"></div></div>
        </div>
        <div class="perf-row">
            <div class="perf-label"><span>Accuracy</span><span><?= $p['acc'] ?>%</span></div>
            <div class="perf-track"><div class="perf-fill" data-width="<?= min(100,$p['acc']) ?>" style="width:0%"></div></div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:12px 0;font-size:0.82rem;color:var(--muted);">
            <div><i class="fa-solid fa-skull-crossbones" style="color:var(--red)"></i> Kills: <strong style="color:var(--red)"><?= number_format($p['tk']) ?></strong></div>
            <div><i class="fa-solid fa-flag" style="color:var(--green)"></i> Wins: <strong style="color:var(--green)"><?= number_format($p['tw']) ?></strong></div>
            <div><i class="fa-regular fa-clock" style="color:var(--text)"></i> Playtime: <strong style="color:var(--text)"><?= round($p['tp'],1) ?> h</strong></div>
            <div><i class="fa-solid fa-gamepad" style="color:var(--text)"></i> Sessions: <strong style="color:var(--text)"><?= $p['sessions'] ?></strong></div>
        </div>
        <div style="border-top:1px solid var(--border);padding-top:12px;margin-top:4px;">
            <div style="font-size:0.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">
                <i class="fa-solid fa-lightbulb" style="color:var(--yellow)"></i> Improvement Tips
            </div>
            <ul class="tip-list">
            <?php foreach (getTips($p['tier']) as $tip): ?>
                <li><i class="fa-solid fa-angle-right" style="color:var(--purple);font-size:0.7rem;flex-shrink:0"></i><?= $tip ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <a href="profile_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" style="margin-top:12px;width:100%;justify-content:center">
            <i class="fa-solid fa-eye"></i> View Full Profile
        </a>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
