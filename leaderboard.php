<?php
$page_title = 'Leaderboard';
require_once 'config.php';

$result = $conn->query(
    "SELECT p.id, p.gamer_name, p.favorite_game, p.gamer_rank, p.level,
            COALESCE(SUM(s.kills),0)          AS tk,
            COALESCE(SUM(s.deaths),0)         AS td,
            COALESCE(SUM(s.wins),0)           AS tw,
            COALESCE(SUM(s.matches_played),0) AS tm,
            COALESCE(AVG(s.accuracy),0)       AS aa,
            ROUND(COALESCE(SUM(s.kills),0) + (COALESCE(SUM(s.wins),0) * 10) + COALESCE(AVG(s.accuracy),0), 1) AS score
     FROM profiles p
     LEFT JOIN statistics s ON s.profile_id = p.id
     GROUP BY p.id, p.gamer_name, p.favorite_game, p.gamer_rank, p.level
     ORDER BY score DESC"
);

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fa-solid fa-trophy" style="color:var(--yellow)"></i> <span>Leaderboard</span></div>
        <div class="page-sub">Ranked by score: Kills + (Wins &times; 10) + Avg Accuracy</div>
    </div>
</div>

<?php if ($result->num_rows === 0): ?>
<div class="empty">
    <i class="fa-solid fa-trophy" style="font-size:2.8rem;color:var(--dim);display:block;margin-bottom:12px"></i>
    <p>No data yet. Add profiles and sessions to see rankings.</p>
</div>
<?php else: ?>

<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:60px">Rank</th>
                    <th>Gamer</th>
                    <th>Game</th>
                    <th>Rank</th>
                    <th>Level</th>
                    <th>Kills</th>
                    <th>Wins</th>
                    <th>Accuracy</th>
                    <th>K/D</th>
                    <th>Score</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php $rank = 1; while ($row = $result->fetch_assoc()):
                $kd = $row['td'] > 0 ? round($row['tk'] / $row['td'], 2) : (float)$row['tk'];
                $medal_class = $rank === 1 ? 'rk-1' : ($rank === 2 ? 'rk-2' : ($rank === 3 ? 'rk-3' : 'rk-n'));
            ?>
            <tr>
                <td><div class="rank-num <?= $medal_class ?>"><?= $rank ?></div></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="avatar" style="width:38px;height:38px;font-size:0.85rem;background:<?= avatarColor($row['gamer_name']) ?>"><?= initials($row['gamer_name']) ?></div>
                        <strong><?= htmlspecialchars($row['gamer_name']) ?></strong>
                    </div>
                </td>
                <td style="color:var(--muted)"><?= htmlspecialchars($row['favorite_game']) ?></td>
                <td><span class="rank-pill"><?= htmlspecialchars($row['gamer_rank']) ?></span></td>
                <td style="color:var(--yellow)">Lv.<?= $row['level'] ?></td>
                <td style="color:var(--red);font-weight:700"><?= number_format($row['tk']) ?></td>
                <td style="color:var(--green);font-weight:700"><?= number_format($row['tw']) ?></td>
                <td style="color:var(--cyan)"><?= round($row['aa'],1) ?>%</td>
                <td style="color:var(--purple);font-weight:700"><?= $kd ?></td>
                <td><span class="score-val"><?= number_format($row['score'], 1) ?></span></td>
                <td><a href="profile_detail.php?id=<?= $row['id'] ?>" class="btn btn-cyan btn-sm"><i class="fa-solid fa-eye"></i></a></td>
            </tr>
            <?php $rank++; endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:16px;">
    <div class="card-body" style="font-size:0.84rem;color:var(--muted);">
        <strong style="color:var(--text);"><i class="fa-solid fa-circle-info" style="color:var(--purple)"></i> Score Formula:</strong>
        &nbsp; Score = Total Kills &plus; (Total Wins &times; 10) &plus; Average Accuracy
    </div>
</div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
