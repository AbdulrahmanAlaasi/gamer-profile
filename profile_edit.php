<?php
$page_title = 'Edit Profile';
require_once 'config.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id) { header('Location: profiles.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM profiles WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) { header('Location: profiles.php'); exit; }

$errors = [];
$v = $p;

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
            "UPDATE profiles
             SET gamer_name=?, favorite_game=?, gamer_rank=?, preferred_role=?, bio=?, level=?, xp=?
             WHERE id=?"
        );
        $stmt->bind_param('sssssiii',
            $v['gamer_name'], $v['favorite_game'], $v['gamer_rank'],
            $v['preferred_role'], $v['bio'], $v['level'], $v['xp'], $id
        );
        if ($stmt->execute()) {
            header('Location: profiles.php?msg=updated');
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
        <div class="page-title">Edit <span><?= htmlspecialchars($p['gamer_name']) ?></span></div>
        <div class="page-sub">
            <a href="profile_detail.php?id=<?= $id ?>" style="color:var(--muted);text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Back to Profile</a>
        </div>
    </div>
</div>

<?php foreach ($errors as $e): ?>
<div class="alert alert-err"><i class="fa-solid fa-triangle-exclamation"></i> <?= $e ?></div>
<?php endforeach; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="form-wrap">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Gamer Name *</label>
                    <input class="form-control" type="text" name="gamer_name"
                           value="<?= htmlspecialchars($v['gamer_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Favorite Game</label>
                    <input class="form-control" type="text" name="favorite_game"
                           value="<?= htmlspecialchars($v['favorite_game']) ?>">
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
                           value="<?= htmlspecialchars($v['preferred_role']) ?>">
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
                <textarea class="form-control" name="bio" rows="3"><?= htmlspecialchars($v['bio']) ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Update Profile</button>
                <a href="profile_detail.php?id=<?= $id ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
