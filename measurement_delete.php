<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_manager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM measurements WHERE id = ?');
    $stmt->execute([(int) ($_POST['id'] ?? 0)]);
}

header('Location: measurements.php?deleted=1');
exit;
