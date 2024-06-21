<?php
require_once __DIR__ . '/../helpers.php';

$currentUser = currentUser();
if (!$currentUser) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in']);
    exit;
}

$pdo = getPDO();
$commentId = $_GET['comment_id'] ?? null;

if (!$commentId) {
    echo json_encode(['success' => false, 'error' => 'Invalid comment ID']);
    exit;
}

// Get like and dislike counts
$likeStmt = $pdo->prepare('SELECT COUNT(*) as likeCount FROM comment_likes WHERE comment_id = :comment_id');
$likeStmt->execute(['comment_id' => $commentId]);
$likeCount = $likeStmt->fetch(PDO::FETCH_ASSOC)['likeCount'];

$dislikeStmt = $pdo->prepare('SELECT COUNT(*) as dislikeCount FROM comment_dislikes WHERE comment_id = :comment_id');
$dislikeStmt->execute(['comment_id' => $commentId]);
$dislikeCount = $dislikeStmt->fetch(PDO::FETCH_ASSOC)['dislikeCount'];

echo json_encode(['success' => true, 'likeCount' => $likeCount, 'dislikeCount' => $dislikeCount]);

