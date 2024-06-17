<?php
require_once __DIR__ . '/../helpers.php';

header('Content-Type: application/json');

$pdo = getPDO();
$build_id = $_GET['build_id'] ?? null;

if (!$build_id) {
    echo json_encode(['error' => 'Invalid build ID']);
    exit;
}

$stmt = $pdo->prepare('SELECT 
    (SELECT COUNT(*) FROM likes WHERE build_id = :build_id) as likes,
    (SELECT COUNT(*) FROM dislikes WHERE build_id = :build_id) as dislikes
');
$stmt->execute(['build_id' => $build_id]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($result);

