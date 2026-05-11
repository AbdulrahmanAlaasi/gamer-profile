<?php
$page_title = 'Achievements';
require_once 'config.php';

$filter_profile = (int)($_GET['profile_id'] ?? 0);
$profiles = $conn->query("SELECT id, gamer_name FROM profiles ORDER BY gamer_name");

if ($filter_profile) {
    $stmt = $conn->prepare(
        "SELECT a.*, p.gamer_name FROM achievements a
         JOIN profiles p ON p.id = a.profile_id
         WHERE a.profile_id = ?
         ORDER BY a.unlocked_at DESC"
    );
    $stmt->bind_param('i', $filter_profile);
    $stmt->execute();
    $rows = $stmt->get_result();
} else {
    $rows = $conn->query(
        "SELECT a.*, p.gamer_name FROM achievements a
         JOIN profiles p ON p.id = a.profile_id
         ORDER BY a.unlocked_at DESC"
    );
}

$badge_fa = [
    'First Win'    => 'fa-trophy',
    '100 Kills'    => 'fa-skull-crossbones',
    'Sharpshooter' => 'fa-crosshairs',
    'Elite Player' => 'fa-crown',
    'Team Leader'  => 'fa-users',
];

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fa-solid fa-medal" style="color:var(--orange)"></i> <span>Achievements</span></div>
        <div class="page-sub">Badge milestones earned by gamers</div>
    </div>
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
    <a href="achievements.php" class="btn btn-red btn-sm"><i class="fa-solid fa-xmark"></i> Clear</a>
    <?php endif; ?>
</form>

<?php if ($rows->num_rows === 0): ?>
<div class="empty">
    <i class="fa-solid fa-medal" style="font-size:2.8rem;color:var(--dim);display:block;margin-bottom:12px"></i>
    <p>No achievements found.</p>
</div>
<?php else: ?>
<div class="ach-grid">
<?php while ($a = $rows->fetch_assoc()):
    $fa = $badge_fa[$a['badge_name']] ?? 'fa-medal';
?>
<div class="ach-card">
    <div class="badge-icon"><i class="fa-solid <?= $fa ?>"></i></div>
    <div>
        <div class="ach-name"><?= htmlspecialchars($a['badge_name']) ?></div>
        <div class="ach-desc"><?= htmlspecialchars($a['description']) ?></div>
        <div class="ach-owner"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($a['gamer_name']) ?></div>
        <div class="ach-date"><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($a['unlocked_at']) ?></div>
    </div>
</div>
<?php endwhile; ?>
</div>
<?php endif; ?>

<!-- Badge legend -->
<div class="card" style="margin-top:28px;">
    <div class="card-head"><div class="card-title">Available Badges</div></div>
    <div class="card-body">
        <div class="ach-grid">
            <?php
            $all_badges = [
                ['First Win',    'fa-trophy',          'Win your very first competitive match.'],
                ['100 Kills',    'fa-skull-crossbones', 'Achieve 100 total kills across all sessions.'],
                ['Sharpshooter', 'fa-crosshairs',       'Maintain high accuracy for 5+ sessions.'],
                ['Elite Player', 'fa-crown',            'Reach the Elite performance tier.'],
                ['Team Leader',  'fa-users',            'Win 10 matches while leading your team.'],
            ];
            foreach ($all_badges as [$name, $fa, $desc]):
            ?>
            <div class="ach-card" style="opacity:0.6;">
                <div class="badge-icon"><i class="fa-solid <?= $fa ?>"></i></div>
                <div>
                    <div class="ach-name"><?= $name ?></div>
                    <div class="ach-desc"><?= $desc ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
