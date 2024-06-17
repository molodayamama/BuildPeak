<?php
require_once __DIR__ . '/../helpers.php';

session_start();
$currentUser = currentUser();
if (!$currentUser) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in']);
    exit;
}

$pdo = getPDO();
$data = json_decode(file_get_contents('php://input'), true);
$commentId = $data['comment_id'] ?? null;

if (!$commentId) {
    echo json_encode(['success' => false, 'error' => 'Invalid comment ID']);
    exit;
}

// Check if the comment is already disliked by the user
$stmt = $pdo->prepare('SELECT * FROM comment_dislikes WHERE comment_id = :comment_id AND user_id = :user_id');
$stmt->execute([
    'comment_id' => $commentId,
    'user_id' => $currentUser['id']
]);
$isDisliked = $stmt->fetch();

if ($isDisliked) {
    // Remove the dislike
    $stmt = $pdo->prepare('DELETE FROM comment_dislikes WHERE comment_id = :comment_id AND user_id = :user_id');
    $stmt->execute([
        'comment_id' => $commentId,
        'user_id' => $currentUser['id']
    ]);
    $disliked = false;
} else {
    // Add the dislike
    $stmt = $pdo->prepare('INSERT INTO comment_dislikes (comment_id, user_id) VALUES (:comment_id, :user_id)');
    $stmt->execute([
        'comment_id' => $commentId,
        'user_id' => $currentUser['id']
    ]);
    $disliked = true;

    // Remove any existing like
    $stmt = $pdo->prepare('DELETE FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id');
    $stmt->execute([
        'comment_id' => $commentId,
        'user_id' => $currentUser['id']
    ]);
}

echo json_encode(['success' => true, 'disliked' => $disliked]);

