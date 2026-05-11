<?php
$page_title = 'Gamer Profiles';
require_once 'config.php';

$flash = '';
$flash_type = '';
if (!empty($_GET['msg'])) {
    $map = [
        'added'   => ['Profile added successfully!', 'ok'],
        'updated' => ['Profile updated successfully!', 'ok'],
        'deleted' => ['Profile deleted.', 'ok'],
    ];
    if (isset($map[$_GET['msg']])) [$flash, $flash_type] = $map[$_GET['msg']];
}

$result = $conn->query(
    "SELECT p.*,
        COALESCE(SUM(s.kills), 0) AS total_kills,
        COALESCE(SUM(s.wins),  0) AS total_wins,
        COUNT(s.id)               AS sessions
     FROM profiles p
     LEFT JOIN statistics s ON s.profile_id = p.id
     GROUP BY p.id
     ORDER BY p.id ASC"
);

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <div class="page-title">Gamer <span>Profiles</span></div>
        <div class="page-sub">All registered gamer profiles &mdash; view, edit, or delete</div>
    </div>
    <a href="profile_add.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Profile</a>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash_type ?>"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<?php if ($result->num_rows === 0): ?>
<div class="empty">
    <span class="ei"><i class="fa-solid fa-user" style="font-size:2.8rem;color:var(--dim)"></i></span>
    <p style="margin-top:12px">No profiles yet.</p>
    <a href="profile_add.php" class="btn btn-primary" style="margin-top:14px"><i class="fa-solid fa-plus"></i> Add First Profile</a>
</div>
<?php else: ?>
<div class="profiles-grid">
<?php while ($p = $result->fetch_assoc()): ?>
    <div class="pcard">
        <div class="pcard-top">
            <div class="avatar" style="background:<?= avatarColor($p['gamer_name']) ?>"><?= initials($p['gamer_name']) ?></div>
            <div>
                <div class="pcard-name"><?= htmlspecialchars($p['gamer_name']) ?></div>
                <div class="rank-pill"><?= htmlspecialchars($p['gamer_rank']) ?></div>
            </div>
        </div>
        <div class="pcard-stats">
            <div class="mini-stat">
                <span class="mini-val"><?= $p['level'] ?></span>
                <span class="mini-lbl">Level</span>
            </div>
            <div class="mini-stat">
                <span class="mini-val"><?= number_format($p['total_kills']) ?></span>
                <span class="mini-lbl">Kills</span>
            </div>
            <div class="mini-stat">
                <span class="mini-val"><?= number_format($p['total_wins']) ?></span>
                <span class="mini-lbl">Wins</span>
            </div>
        </div>
        <div class="pcard-meta">
            <i class="fa-solid fa-gamepad"></i> <?= htmlspecialchars($p['favorite_game']) ?>
            &nbsp;&bull;&nbsp;
            <i class="fa-solid fa-shield-halved"></i> <?= htmlspecialchars($p['preferred_role']) ?>
        </div>
        <div class="pcard-actions">
            <a href="profile_detail.php?id=<?= $p['id'] ?>" class="btn btn-cyan btn-sm"><i class="fa-solid fa-eye"></i> View</a>
            <a href="profile_edit.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
            <a href="profile_delete.php?id=<?= $p['id'] ?>"
               class="btn btn-red btn-sm"
               onclick="return confirmDelete('Delete profile &quot;<?= htmlspecialchars(addslashes($p['gamer_name'])) ?>&quot;? All related stats will also be deleted.')">
               <i class="fa-solid fa-trash"></i> Delete
            </a>
        </div>
    </div>
<?php endwhile; ?>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
