<?php
$page_title = 'Edit Session';
require_once 'config.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id) { header('Location: statistics.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM statistics WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { header('Location: statistics.php'); exit; }

$errors = [];
$v = $row;

$profiles = $conn->query("SELECT id, gamer_name FROM profiles ORDER BY gamer_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['profile_id']     = (int)($_POST['profile_id']        ?? 0);
    $v['game_title']     = trim($_POST['game_title']          ?? '');
    $v['kills']          = max(0, (int)($_POST['kills']       ?? 0));
    $v['deaths']         = max(0, (int)($_POST['deaths']      ?? 0));
    $v['wins']           = max(0, (int)($_POST['wins']        ?? 0));
    $v['matches_played'] = max(1, (int)($_POST['matches_played'] ?? 1));
    $v['accuracy']       = min(100, max(0, (float)($_POST['accuracy'] ?? 0)));
    $v['playtime']       = max(0, (float)($_POST['playtime']  ?? 0));
    $v['session_date']   = $_POST['session_date'] ?? date('Y-m-d');

    if (!$v['profile_id'])  $errors[] = 'Please select a gamer.';
    if (!$v['game_title'])  $errors[] = 'Game title is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE statistics
             SET profile_id=?, game_title=?, kills=?, deaths=?, wins=?,
                 matches_played=?, accuracy=?, playtime=?, session_date=?
             WHERE id=?"
        );
        $stmt->bind_param('isiiiiddsi',
            $v['profile_id'], $v['game_title'],
            $v['kills'], $v['deaths'], $v['wins'], $v['matches_played'],
            $v['accuracy'], $v['playtime'], $v['session_date'], $id
        );
        if ($stmt->execute()) {
            header('Location: statistics.php?msg=updated');
            exit;
        } else {
            $errors[] = 'Database error: ' . htmlspecialchars($conn->error);
        }
    }
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <div class="page-title">Edit <span>Session</span></div>
        <div class="page-sub">
            <a href="statistics.php" style="color:var(--muted);text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Back to Statistics</a>
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
                    <label class="form-label">Gamer *</label>
                    <select class="form-control" name="profile_id" required>
                        <option value="">-- Select Gamer --</option>
                        <?php while ($pr = $profiles->fetch_assoc()): ?>
                        <option value="<?= $pr['id'] ?>" <?= $v['profile_id'] == $pr['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pr['gamer_name']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Game Title *</label>
                    <input class="form-control" type="text" name="game_title"
                           value="<?= htmlspecialchars($v['game_title']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kills</label>
                    <input class="form-control" type="number" name="kills" value="<?= $v['kills'] ?>" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Deaths</label>
                    <input class="form-control" type="number" name="deaths" value="<?= $v['deaths'] ?>" min="0">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Wins</label>
                    <input class="form-control" type="number" name="wins" value="<?= $v['wins'] ?>" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Matches Played</label>
                    <input class="form-control" type="number" name="matches_played" value="<?= $v['matches_played'] ?>" min="1">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Accuracy (%)</label>
                    <input class="form-control" type="number" name="accuracy"
                           value="<?= $v['accuracy'] ?>" min="0" max="100" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Playtime (hours)</label>
                    <input class="form-control" type="number" name="playtime"
                           value="<?= $v['playtime'] ?>" min="0" step="0.1">
                </div>
            </div>
            <div class="form-group" style="max-width:300px">
                <label class="form-label">Session Date *</label>
                <input class="form-control" type="date" name="session_date"
                       value="<?= htmlspecialchars($v['session_date']) ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Update Session</button>
                <a href="statistics.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
