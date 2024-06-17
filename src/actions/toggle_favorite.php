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
$buildId = $data['build_id'] ?? null;

if (!$buildId) {
    echo json_encode(['success' => false, 'error' => 'Invalid build ID']);
    exit;
}

// Check if the build is already marked as favorite
$stmt = $pdo->prepare('SELECT * FROM heart WHERE build_id = :build_id AND user_id = :user_id');
$stmt->execute([
    'build_id' => $buildId,
    'user_id' => $currentUser['id']
]);
$isFavorite = $stmt->fetch();

if ($isFavorite) {
    // Remove from favorites
    $stmt = $pdo->prepare('DELETE FROM heart WHERE build_id = :build_id AND user_id = :user_id');
    $result = $stmt->execute([
        'build_id' => $buildId,
        'user_id' => $currentUser['id']
    ]);
    $favorite = false;
} else {
    // Add to favorites
    $stmt = $pdo->prepare('INSERT INTO heart (build_id, user_id) VALUES (:build_id, :user_id)');
    $result = $stmt->execute([
        'build_id' => $buildId,
        'user_id' => $currentUser['id']
    ]);
    $favorite = true;
}

echo json_encode(['success' => true, 'favorite' => $favorite]);

