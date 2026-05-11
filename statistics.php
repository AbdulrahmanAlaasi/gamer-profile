<?php
$page_title = 'Statistics';
require_once 'config.php';

$flash = '';
$flash_type = '';
if (!empty($_GET['msg'])) {
    $map = [
        'added'   => ['Session added successfully!', 'ok'],
        'updated' => ['Session updated successfully!', 'ok'],
        'deleted' => ['Session deleted.', 'ok'],
    ];
    if (isset($map[$_GET['msg']])) [$flash, $flash_type] = $map[$_GET['msg']];
}

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
        <div class="page-title">Game <span>Statistics</span></div>
        <div class="page-sub">Manage gameplay sessions &mdash; Add, View, Edit, Delete</div>
    </div>
    <a href="stat_add.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Session</a>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash_type ?>"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<form method="GET" class="filter-bar">
    <select name="profile_id" class="form-control">
        <option value="">All Profiles</option>
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
    <a href="statistics.php" class="btn btn-red btn-sm"><i class="fa-solid fa-xmark"></i> Clear</a>
    <?php endif; ?>
</form>

<?php if ($rows->num_rows === 0): ?>
<div class="empty">
    <i class="fa-solid fa-chart-bar" style="font-size:2.8rem;color:var(--dim);display:block;margin-bottom:12px"></i>
    <p>No sessions found.</p>
    <a href="stat_add.php" class="btn btn-primary" style="margin-top:14px"><i class="fa-solid fa-plus"></i> Add First Session</a>
</div>
<?php else: ?>
<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gamer</th>
                    <th>Game</th>
                    <th>Kills</th>
                    <th>Deaths</th>
                    <th>Wins</th>
                    <th>Matches</th>
                    <th>Accuracy</th>
                    <th>Playtime (h)</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($row = $rows->fetch_assoc()): ?>
            <tr>
                <td style="color:var(--dim)"><?= $i++ ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="avatar" style="width:30px;height:30px;font-size:0.7rem;background:<?= avatarColor($row['gamer_name']) ?>"><?= initials($row['gamer_name']) ?></div>
                        <a href="profile_detail.php?id=<?= $row['profile_id'] ?>" style="text-decoration:none;color:var(--text)">
                            <?= htmlspecialchars($row['gamer_name']) ?>
                        </a>
                    </div>
                </td>
                <td><?= htmlspecialchars($row['game_title']) ?></td>
                <td style="color:var(--red);font-weight:700"><?= $row['kills'] ?></td>
                <td style="color:var(--muted)"><?= $row['deaths'] ?></td>
                <td style="color:var(--green);font-weight:700"><?= $row['wins'] ?></td>
                <td><?= $row['matches_played'] ?></td>
                <td style="color:var(--cyan)"><?= $row['accuracy'] ?>%</td>
                <td><?= $row['playtime'] ?></td>
                <td style="color:var(--muted)"><?= $row['session_date'] ?></td>
                <td>
                    <div style="display:flex;gap:5px;">
                        <a href="stat_edit.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <a href="stat_delete.php?id=<?= $row['id'] ?>"
                           class="btn btn-red btn-sm"
                           onclick="return confirmDelete('Delete this session?')"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
