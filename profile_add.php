<?php
$page_title = 'Add Profile';
require_once 'config.php';

$errors = [];
$v = ['gamer_name'=>'','favorite_game'=>'','gamer_rank'=>'Unranked','preferred_role'=>'','bio'=>'','level'=>1,'xp'=>0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['gamer_name']     = trim($_POST['gamer_name']     ?? '');
    $v['favorite_game']  = trim($_POST['favorite_game']  ?? '');
    $v['gamer_rank']     = trim($_POST['gamer_rank']     ?? 'Unranked');
    $v['preferred_role'] = trim($_POST['preferred_role'] ?? '');
    $v['bio']            = trim($_POST['bio']            ?? '');
    $v['level']          = max(1, (int)($_POST['level'] ?? 1));
    $v['xp']             = max(0, (int)($_POST['xp']    ?? 0));

    if ($v['gamer_name'] === '') $errors[] = 'Gamer name is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO profiles (gamer_name,favorite_game,gamer_rank,preferred_role,bio,level,xp)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->bind_param('sssssii',
            $v['gamer_name'], $v['favorite_game'], $v['gamer_rank'],
            $v['preferred_role'], $v['bio'], $v['level'], $v['xp']
        );
        if ($stmt->execute()) {
            header('Location: profiles.php?msg=added');
            exit;
        } else {
            $errors[] = 'Database error: ' . htmlspecialchars($conn->error);
        }
    }
}

$ranks = ['Unranked','Bronze','Silver','Gold','Platinum','Diamond','Master','Elite'];
require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <div class="page-title">Add <span>Profile</span></div>
        <div class="page-sub">
            <a href="profiles.php" style="color:var(--muted);text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Back to Profiles</a>
        </div>
    </div>
</div>

<?php foreach ($errors as $e): ?>
<div class="alert alert-err"><i class="fa-solid fa-triangle-exclamation"></i> <?= $e ?></div>
<?php endforeach; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form-wrap">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Gamer Name *</label>
                    <input class="form-control" type="text" name="gamer_name"
                           value="<?= htmlspecialchars($v['gamer_name']) ?>"
                           placeholder="e.g. ShadowStrike" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Favorite Game</label>
                    <input class="form-control" type="text" name="favorite_game"
                           value="<?= htmlspecialchars($v['favorite_game']) ?>"
                           placeholder="e.g. Call of Duty">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Rank</label>
                    <select class="form-control" name="gamer_rank">
                        <?php foreach ($ranks as $r): ?>
                        <option value="<?= $r ?>" <?= $v['gamer_rank']===$r ? 'selected' : '' ?>><?= $r ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Preferred Role</label>
                    <input class="form-control" type="text" name="preferred_role"
                           value="<?= htmlspecialchars($v['preferred_role']) ?>"
                           placeholder="e.g. Sniper, Support, Duelist">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Level</label>
                    <input class="form-control" type="number" name="level"
                           value="<?= $v['level'] ?>" min="1" max="9999">
                </div>
                <div class="form-group">
                    <label class="form-label">XP</label>
                    <input class="form-control" type="number" name="xp"
                           value="<?= $v['xp'] ?>" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea class="form-control" name="bio" rows="3"
                          placeholder="Short description about this gamer..."><?= htmlspecialchars($v['bio']) ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Save Profile</button>
                <a href="profiles.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
