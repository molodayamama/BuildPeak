<?php
require_once __DIR__ . '/../helpers.php';

$build_id = $_GET['build_id'] ?? null;

if (!$build_id) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(['error' => 'Invalid build ID']);
    exit;
}

$pdo = getPDO();

$likesStmt = $pdo->prepare('SELECT COUNT(*) as likes FROM likes WHERE build_id = :build_id');
$likesStmt->execute(['build_id' => $build_id]);
$likes = $likesStmt->fetchColumn();

$dislikesStmt = $pdo->prepare('SELECT COUNT(*) as dislikes FROM dislikes WHERE build_id = :build_id');
$dislikesStmt->execute(['build_id' => $build_id]);
$dislikes = $dislikesStmt->fetchColumn();

echo json_encode(['likes' => $likes, 'dislikes' => $dislikes]);
