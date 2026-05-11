<?php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: profiles.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM profiles WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) { header('Location: profiles.php'); exit; }

$page_title = htmlspecialchars($p['gamer_name']);

$stmt2 = $conn->prepare(
    "SELECT COALESCE(SUM(kills),0) tk, COALESCE(SUM(deaths),0) td,
            COALESCE(SUM(wins),0) tw, COALESCE(SUM(matches_played),0) tm,
            COALESCE(AVG(accuracy),0) aa, COALESCE(SUM(playtime),0) tp,
            COUNT(*) sessions
     FROM statistics WHERE profile_id = ?"
);
$stmt2->bind_param('i', $id);
$stmt2->execute();
$s = $stmt2->get_result()->fetch_assoc();

$kd   = $s['td'] > 0 ? round($s['tk'] / $s['td'], 2) : (float)$s['tk'];
$wr   = $s['tm'] > 0 ? round(($s['tw'] / $s['tm']) * 100, 1) : 0;
$acc  = round($s['aa'], 1);
[$tier_name, $tier_class] = getTier($kd, $wr);
$xp_pct = xpPercent($p['xp']);
$xp_cur = $p['xp'] % 1000;

$stmt3 = $conn->prepare("SELECT * FROM statistics WHERE profile_id = ? ORDER BY session_date DESC LIMIT 4");
$stmt3->bind_param('i', $id);
$stmt3->execute();
$recent = $stmt3->get_result();

$stmt4 = $conn->prepare("SELECT * FROM achievements WHERE profile_id = ? ORDER BY unlocked_at DESC");
$stmt4->bind_param('i', $id);
$stmt4->execute();
$ach_res = $stmt4->get_result();

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <div class="page-title"><span><?= htmlspecialchars($p['gamer_name']) ?></span></div>
        <div class="page-sub">
            <a href="profiles.php" style="color:var(--muted);text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> All Profiles</a>
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="profile_edit.php?id=<?= $p['id'] ?>" class="btn btn-outline"><i class="fa-solid fa-pen"></i> Edit Profile</a>
        <a href="stat_add.php?profile_id=<?= $p['id'] ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Session</a>
    </div>
</div>

<div class="detail-layout">

    <!-- LEFT: Profile Card -->
    <div>
        <div class="profile-main-card">
            <div class="card-top-strip"></div>
            <div class="card-inner">

                <div class="profile-hrow">
                    <div class="avatar-lg" style="background:<?= avatarColor($p['gamer_name']) ?>"><?= initials($p['gamer_name']) ?></div>
                    <div>
                        <div class="gname"><?= htmlspecialchars($p['gamer_name']) ?></div>
                        <div class="gsub"><i class="fa-solid fa-gamepad"></i> <?= htmlspecialchars($p['favorite_game']) ?></div>
                        <div class="grole">Agent Class: <?= htmlspecialchars($p['preferred_role']) ?></div>
                    </div>
                </div>

                <div class="level-row">
                    <span class="lv-text">LEVEL <?= $p['level'] ?></span>
                    <span class="xp-num"><?= number_format($p['xp']) ?> XP</span>
                </div>
                <div class="xp-track" style="margin-bottom:4px;">
                    <div class="xp-fill" data-width="<?= $xp_pct ?>" style="width:0%"></div>
                </div>
                <div class="xp-sub">XP: <?= $xp_cur ?> / 1000 &nbsp;&bull;&nbsp; <?= 1000 - $xp_cur ?> XP to next level</div>

                <div class="p-divider"></div>

                <div class="pstat-grid">
                    <div class="pstat-box">
                        <div class="pstat-lbl">Total Kills</div>
                        <div class="pstat-val" style="color:var(--red)"><?= number_format($s['tk']) ?></div>
                    </div>
                    <div class="pstat-box">
                        <div class="pstat-lbl">Total Wins</div>
                        <div class="pstat-val" style="color:var(--green)"><?= number_format($s['tw']) ?></div>
                    </div>
                    <div class="pstat-box">
                        <div class="pstat-lbl">Rank</div>
                        <div class="pstat-val" style="color:var(--cyan)"><?= htmlspecialchars($p['gamer_rank']) ?></div>
                    </div>
                    <div class="pstat-box">
                        <div class="pstat-lbl">Sessions</div>
                        <div class="pstat-val"><?= $s['sessions'] ?></div>
                    </div>
                </div>

                <div class="pvp-row">
                    <div class="pvp-box">
                        <div class="pvp-lbl">K/D Ratio</div>
                        <div class="pvp-val"><span class="w"><?= $kd ?></span> / <span class="l">1.0</span></div>
                    </div>
                    <div class="pvp-box">
                        <div class="pvp-lbl">Avg Accuracy</div>
                        <div class="pvp-val"><span class="w"><?= $acc ?>%</span></div>
                    </div>
                    <div class="circle-wrap">
                        <div class="circ" style="background:conic-gradient(var(--cyan) <?= $wr ?>%, rgba(255,255,255,0.06) 0%)">
                            <div class="circ-in"><?= round($wr) ?>%</div>
                        </div>
                    </div>
                </div>

                <?php if ($p['bio']): ?>
                <div class="bio-sec">
                    <div class="bio-lbl"><i class="fa-solid fa-align-left"></i> Description</div>
                    <div class="bio-txt"><?= htmlspecialchars($p['bio']) ?></div>
                </div>
                <?php endif; ?>

                <div class="p-actions">
                    <a href="profile_edit.php?id=<?= $p['id'] ?>" class="btn btn-outline" style="flex:1;justify-content:center"><i class="fa-solid fa-pen"></i> Edit</a>
                    <span class="tier <?= $tier_class ?>" style="flex:1;text-align:center;display:flex;align-items:center;justify-content:center"><?= $tier_name ?></span>
                </div>

            </div>
        </div>
    </div>

    <!-- RIGHT: Stats + Analysis -->
    <div class="profile-right">

        <div>
            <div class="card-title" style="margin-bottom:14px;">Performance Stats</div>
            <div class="stat-cards">
                <div class="scard kills">
                    <span class="scard-val"><?= number_format($s['tk']) ?></span>
                    <div class="scard-lbl">Total Kills</div>
                </div>
                <div class="scard deaths">
                    <span class="scard-val"><?= number_format($s['td']) ?></span>
                    <div class="scard-lbl">Total Deaths</div>
                </div>
                <div class="scard kd">
                    <span class="scard-val"><?= $kd ?></span>
                    <div class="scard-lbl">K/D Ratio</div>
                </div>
                <div class="scard wins">
                    <span class="scard-val"><?= number_format($s['tw']) ?></span>
                    <div class="scard-lbl">Total Wins</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-title">Performance Summary</div>
                <span class="tier <?= $tier_class ?>"><?= $tier_name ?></span>
            </div>
            <div class="card-body">
                <div class="ds-row" style="margin-bottom:18px;">
                    <div class="ds"><div class="ds-val"><?= $kd ?></div><div class="ds-lbl">K/D Ratio</div></div>
                    <div class="ds"><div class="ds-val"><?= $wr ?>%</div><div class="ds-lbl">Win Rate</div></div>
                    <div class="ds"><div class="ds-val"><?= $acc ?>%</div><div class="ds-lbl">Accuracy</div></div>
                </div>
                <div class="perf-row">
                    <div class="perf-label"><span>Win Rate</span><span><?= $wr ?>%</span></div>
                    <div class="perf-track"><div class="perf-fill" data-width="<?= min(100,$wr) ?>" style="width:0%"></div></div>
                </div>
                <div class="perf-row">
                    <div class="perf-label"><span>Accuracy</span><span><?= $acc ?>%</span></div>
                    <div class="perf-track"><div class="perf-fill" data-width="<?= min(100,$acc) ?>" style="width:0%"></div></div>
                </div>
                <div class="perf-row">
                    <div class="perf-label"><span>Total Playtime</span><span><?= round($s['tp'],1) ?> hrs</span></div>
                    <div class="perf-track"><div class="perf-fill" data-width="<?= min(100,$s['tp']/3) ?>" style="width:0%"></div></div>
                </div>
            </div>
        </div>

        <?php if ($recent->num_rows > 0): ?>
        <div class="card">
            <div class="card-head">
                <div class="card-title">Recent Sessions</div>
                <a href="match_history.php?profile_id=<?= $id ?>" class="btn btn-cyan btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr><th>Game</th><th>Kills</th><th>Deaths</th><th>Wins</th><th>Accuracy</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['game_title']) ?></td>
                        <td style="color:var(--red)"><?= $row['kills'] ?></td>
                        <td style="color:var(--muted)"><?= $row['deaths'] ?></td>
                        <td style="color:var(--green)"><?= $row['wins'] ?></td>
                        <td style="color:var(--cyan)"><?= $row['accuracy'] ?>%</td>
                        <td style="color:var(--muted)"><?= $row['session_date'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($ach_res->num_rows > 0): ?>
        <div class="card">
            <div class="card-head"><div class="card-title">Achievements</div></div>
            <div class="card-body">
                <div class="ach-grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr))">
                <?php
                $badge_fa = [
                    'First Win'    => 'fa-trophy',
                    '100 Kills'    => 'fa-skull-crossbones',
                    'Sharpshooter' => 'fa-crosshairs',
                    'Elite Player' => 'fa-crown',
                    'Team Leader'  => 'fa-users',
                ];
                while ($a = $ach_res->fetch_assoc()):
                    $fa = $badge_fa[$a['badge_name']] ?? 'fa-medal';
                ?>
                <div class="ach-card">
                    <div class="badge-icon"><i class="fa-solid <?= $fa ?>"></i></div>
                    <div>
                        <div class="ach-name"><?= htmlspecialchars($a['badge_name']) ?></div>
                        <div class="ach-date"><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($a['unlocked_at']) ?></div>
                    </div>
                </div>
                <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
