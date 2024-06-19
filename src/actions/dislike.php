<?php
require_once __DIR__ . '/../helpers.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$currentUser = currentUser();
if (!$currentUser) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['error' => 'You must be logged in to dislike a build']);
    exit;
}

$pdo = getPDO();

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$build_id = $data['build_id'] ?? null;

if (!$build_id) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(['error' => 'Invalid build ID']);
    exit;
}

// Check if user already liked the build
$stmt = $pdo->prepare('SELECT * FROM likes WHERE build_id = :build_id AND user_id = :user_id');
$stmt->execute([
    'build_id' => $build_id,
    'user_id' => $currentUser['id']
]);
$like = $stmt->fetch();

if ($like) {
    // Remove like
    $stmt = $pdo->prepare('DELETE FROM likes WHERE build_id = :build_id AND user_id = :user_id');
    $stmt->execute([
        'build_id' => $build_id,
        'user_id' => $currentUser['id']
    ]);
}

// Check if user already disliked the build
$stmt = $pdo->prepare('SELECT * FROM dislikes WHERE build_id = :build_id AND user_id = :user_id');
$stmt->execute([
    'build_id' => $build_id,
    'user_id' => $currentUser['id']
]);
$dislike = $stmt->fetch();

if ($dislike) {
    // Remove dislike
    $stmt = $pdo->prepare('DELETE FROM dislikes WHERE build_id = :build_id AND user_id = :user_id');
    $stmt->execute([
        'build_id' => $build_id,
        'user_id' => $currentUser['id']
    ]);
    echo json_encode(['disliked' => false]);
} else {
    // Dislike the build
    $stmt = $pdo->prepare('INSERT INTO dislikes (build_id, user_id) VALUES (:build_id, :user_id)');
    $stmt->execute([
        'build_id' => $build_id,
        'user_id' => $currentUser['id']
    ]);
    echo json_encode(['disliked' => true]);
}
