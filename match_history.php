<?php
$page_title = 'Match History';
require_once 'config.php';

$filter_profile = (int)($_GET['profile_id'] ?? 0);
$profiles = $conn->query("SELECT id, gamer_name FROM profiles ORDER BY gamer_name");

if ($filter_profile) {
    $stmt = $conn->prepare(
        "SELECT s.*, p.gamer_name FROM statistics s
         JOIN profiles p ON p.id = s.profile_id
         WHERE s.profile_id = ?
         ORDER BY s.session_date DESC, s.id DESC"
    );
    $stmt->bind_param('i', $filter_profile);
    $stmt->execute();
    $rows = $stmt->get_result();
} else {
    $rows = $conn->query(
        "SELECT s.*, p.gamer_name FROM statistics s
         JOIN profiles p ON p.id = s.profile_id
         ORDER BY s.session_date DESC, s.id DESC"
    );
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <div class="page-title">Match <span>History</span></div>
        <div class="page-sub">All gameplay sessions in chronological order</div>
    </div>
    <a href="stat_add.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Session</a>
</div>

<form method="GET" class="filter-bar">
    <select name="profile_id" class="form-control">
        <option value="">All Gamers</option>
        <?php
        $profiles->data_seek(0);
        while ($pr = $profiles->fetch_assoc()):
        ?>
        <option value="<?= $pr['id'] ?>" <?= $filter_profile == $pr['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($pr['gamer_name']) ?>
        </option>
        <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-outline"><i class="fa-solid fa-filter"></i> Filter</button>
    <?php if ($filter_profile): ?>
    <a href="match_history.php" class="btn btn-red btn-sm"><i class="fa-solid fa-xmark"></i> Clear</a>
    <?php endif; ?>
</form>

<?php if ($rows->num_rows === 0): ?>
<div class="empty">
    <i class="fa-solid fa-clock-rotate-left" style="font-size:2.8rem;color:var(--dim);display:block;margin-bottom:12px"></i>
    <p>No sessions found for the selected filter.</p>
</div>
<?php else:
    $sessions = [];
    while ($row = $rows->fetch_assoc()) {
        $sessions[$row['session_date']][] = $row;
    }
?>

<?php foreach ($sessions as $date => $day_sessions): ?>
<div style="margin-bottom:22px;">
    <div style="font-size:0.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;display:flex;align-items:center;gap:8px;">
        <i class="fa-regular fa-calendar" style="color:var(--purple)"></i>
        <?= htmlspecialchars($date) ?>
    </div>
    <div class="card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>Gamer</th><th>Game</th><th>Kills</th><th>Deaths</th><th>K/D</th><th>Wins</th><th>Matches</th><th>Accuracy</th><th>Playtime</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($day_sessions as $s):
                    $kd = $s['deaths'] > 0 ? round($s['kills'] / $s['deaths'], 2) : $s['kills'];
                ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar" style="width:28px;height:28px;font-size:0.65rem;background:<?= avatarColor($s['gamer_name']) ?>"><?= initials($s['gamer_name']) ?></div>
                            <a href="profile_detail.php?id=<?= $s['profile_id'] ?>" style="text-decoration:none;color:var(--text)">
                                <?= htmlspecialchars($s['gamer_name']) ?>
                            </a>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($s['game_title']) ?></td>
                    <td style="color:var(--red);font-weight:700"><?= $s['kills'] ?></td>
                    <td style="color:var(--muted)"><?= $s['deaths'] ?></td>
                    <td style="color:var(--purple);font-weight:700"><?= $kd ?></td>
                    <td style="color:var(--green);font-weight:700"><?= $s['wins'] ?></td>
                    <td><?= $s['matches_played'] ?></td>
                    <td style="color:var(--cyan)"><?= $s['accuracy'] ?>%</td>
                    <td><?= $s['playtime'] ?> h</td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <a href="stat_edit.php?id=<?= $s['id'] ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-pen"></i></a>
                            <a href="stat_delete.php?id=<?= $s['id'] ?>"
                               class="btn btn-red btn-sm"
                               onclick="return confirmDelete('Delete this session?')"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
