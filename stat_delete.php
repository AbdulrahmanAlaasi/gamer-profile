<?php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $conn->prepare("DELETE FROM statistics WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

header('Location: statistics.php?msg=deleted');
exit;
