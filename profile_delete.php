<?php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $conn->prepare("DELETE FROM profiles WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

header('Location: profiles.php?msg=deleted');
exit;
