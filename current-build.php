<?php
require_once __DIR__ . '/src/helpers.php';

// Получение информации о текущем пользователе
$currentUser = currentUser();

$pdo = getPDO();

// Получение ID сборки из URL
$build_id = $_GET['build_id'] ?? 0;

// Fetching build details
$query = $pdo->prepare("SELECT builds.*, users.name AS author_name, builds.user_id AS author_id, COUNT(likes.build_id) AS likes_count
FROM builds
LEFT JOIN users ON builds.user_id = users.id
LEFT JOIN likes ON builds.buildid = likes.build_id
WHERE builds.buildid = :build_id
GROUP BY builds.buildid");
$query->execute(['build_id' => $build_id]);

$build = $query->fetch();

if (!$build) {
    echo "Сборка не найдена.";
    exit;
}

$buildType = $build['build_type'] ?? 'Неизвестный тип';

// Handling delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_build'])) {
    if ($currentUser && $currentUser['id'] == $build['author_id']) {
        // Delete associated likes
        $deleteLikesStmt = $pdo->prepare("DELETE FROM likes WHERE build_id = :build_id");
        $deleteLikesStmt->execute(['build_id' => $build_id]);

        // Delete associated dislikes
        $deleteDislikesStmt = $pdo->prepare("DELETE FROM dislikes WHERE build_id = :build_id");
        $deleteDislikesStmt->execute(['build_id' => $build_id]);

        // Delete associated comments
        $deleteCommentsStmt = $pdo->prepare("DELETE FROM comments WHERE build_id = :build_id");
        $deleteCommentsStmt->execute(['build_id' => $build_id]);

        // Delete associated hearts
        $deleteHeartsStmt = $pdo->prepare("DELETE FROM heart WHERE build_id = :build_id");
        $deleteHeartsStmt->execute(['build_id' => $build_id]);

        // Delete the build
        $deleteStmt = $pdo->prepare("DELETE FROM builds WHERE buildid = :build_id");
        $deleteStmt->execute(['build_id' => $build_id]);

        header("Location: build-auth.php");
        exit;
    } else {
        echo "У вас нет прав для удаления этой сборки.";
        exit;
    }
}

// Handling new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $commentText = $_POST['comment_text'];
    $stmt = $pdo->prepare("INSERT INTO comments (build_id, user_id, comment_text) VALUES (:build_id, :user_id, :comment_text)");
    $stmt->execute([
        'build_id' => $build_id,
        'user_id' => $currentUser['id'],
        'comment_text' => $commentText
    ]);
// Redirect to the same page to avoid form resubmission
    header("Location: current-build.php?build_id=" . $build_id);
    exit;
}

// Fetching comments for the build
$commentsQuery = $pdo->prepare("
    SELECT comments.*, users.name AS author_name, users.avatar AS author_avatar,
           (SELECT COUNT(*) FROM comment_likes WHERE comment_likes.comment_id = comments.comment_id) AS like_count,
           (SELECT COUNT(*) FROM comment_dislikes WHERE comment_dislikes.comment_id = comments.comment_id) AS dislike_count
    FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE comments.build_id = :build_id
    ORDER BY comments.created_at DESC
");
$commentsQuery->execute(['build_id' => $build_id]);
$comments = $commentsQuery->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сборка</title>
    <link rel="stylesheet" href="assets/css/current.css">
</head>
<body>
<header class="header">
    <a href="build-auth.php"><img src="assets/images/logo 1.svg" alt="Polymatrix Logo" class="logo"></a>
    <nav class="navigation">
        <a href="build-auth.php">Сборки</a>
        <a>|</a>
        <a href="description-log.php">О нас</a>
    </nav>
    <?php if ($currentUser === false) { ?>
        <div class="user-info">
            <div class="user-controls">
                <form action="login.php">
                    <button class="login-button">Войти</button>
                </form>
                <form action="register.php">
                    <button class="register-button">Регистрация</button>
                </form>
            </div>
        </div>
    <?php } else { ?>
        <div class="user-info">
            <div class="avthor-info" onclick="togglePopup(this)">
                <button class="nickname"><?php echo $currentUser['name']; ?> <img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'assets/images/Ellipse 5.png') ?>" class="userpic" width="45"></button>
            </div>
            <div id="popup" style="display: none;">
                <a href="my-profile.php">Мой профиль</a>
                <a href="settings.php">Настройки</a>
                <a href="favorite.php">Избранное</a>
                <p><a href="src/actions/logout.php">Выйти</a></p>
            </div>
        </div>
    <?php } ?>
</header>
<main class="main-content">
    <aside class="sidebar">
        <nav class="categories">
        </nav>
    </aside>
    <section class="products">
        <div class="build-main">
            <div class="build-details">
                <div class="build-name">
                    <h1>Цена:<textarea readonly id="numericTextArea" maxlength="7"><?= htmlspecialchars($build['price']) ?></textarea></h1>
                    <p><strong>Автор:</strong>
                        <a style="color: black" href="user-profile.php?user_id=<?= htmlspecialchars($build['author_id']) ?>">
                        <?= htmlspecialchars($build['author_name']) ?></p></a>
                    <p><strong>Количество Лайков:</strong>
                        <?= htmlspecialchars($build['likes_count']) ?></p>
                    <p><strong>Год сборки:</strong> <textarea readonly id="numericTextArea" maxlength="4"><?= htmlspecialchars($build['year']) ?></textarea></p>
                </div>
                <div class="build-specs">
                    <ul>
                        <li class="component-block">
                            <span>Процессор:</span>
                            <textarea readonly><?= htmlspecialchars($build['processor']) ?></textarea></li>
                        <li class="component-block">
                            <span>Видеокарта:</span>
                            <textarea readonly><?= htmlspecialchars($build['videocard']) ?></textarea></li>
                        <li class="component-block">
                            <span>ОЗУ:</span>
                            <textarea readonly><?= htmlspecialchars($build['ram']) ?></textarea></li>
                        <li class="component-block">
                            <span>Материнская плата:</span>
                            <textarea readonly><?= htmlspecialchars($build['motherboard']) ?></textarea></li>
                        <li class="component-block">
                            <span>Система охлаждения:</span>
                            <textarea readonly><?= htmlspecialchars($build['coolsys']) ?></textarea></li>
                        <li class="component-block">
                            <span>HDD:</span>
                            <textarea readonly><?= htmlspecialchars($build['hdd']) ?></textarea></li>
                        <li class="component-block">
                            <span>SSD:</span>
                            <textarea readonly><?= htmlspecialchars($build['ssd']) ?></textarea></li>
                        <li class="component-block">
                            <span>Блок питания:</span>
                            <textarea readonly><?= htmlspecialchars($build['power']) ?></textarea></li>
                    </ul>
                </div>
                <div class="build-description">
                    <p class="desc">Описание</p>
                    <label for="desc">
                        <textarea readonly id="desc"><?= htmlspecialchars($build['description']) ?></textarea>
                    </label>
                    <p id="charCount">0/300</p>
                </div>
                <div class="plus">
                    <h2>Плюсы сборки:</h2>
                    <ul>
                        <li>• <?= htmlspecialchars($build['plus1']) ?></li>
                        <li>• <?= htmlspecialchars($build['plus2']) ?></li>
                        <li>• <?= htmlspecialchars($build['plus3']) ?></li>
                        <li>• <?= htmlspecialchars($build['plus4']) ?></li>
                    </ul>
                    <h2>Минусы сборки:</h2>
                    <ul>
                        <li>• <?= htmlspecialchars($build['minus1']) ?></li>
                        <li>• <?= htmlspecialchars($build['minus2']) ?></li>
                        <li>• <?= htmlspecialchars($build['minus3']) ?></li>
                        <li>• <?= htmlspecialchars($build['minus4']) ?></li>
                    </ul>
                </div>
                <div class="product-card">
                    <span class="heart-icon">
                        <img src="assets/images/heart.svg" width="40px" class="heart"
                             onclick="toggleHeart(this, <?= htmlspecialchars($build['buildid']) ?>)">
                    </span>
                    <span class="thumb-up">
                        <img src="assets/images/Thumb Like.svg" alt="Like" class="likeButton"
                             onclick="toggleLike(this, <?= htmlspecialchars($build['buildid']) ?>)">
                    </span>
                    <span class="thumb-down">
                        <img src="assets/images/Thumb Like (1).svg" class="unlikeButton"
                             onclick="toggleUNLike(this, <?= htmlspecialchars($build['buildid']) ?>)">
                    </span>
                </div>
                <div class="comments-section">
                    <h2 id="commentCount"><?= count($comments) ?> комментариев</h2>
                    <div class="comment-input-area">
                        <?php if ($currentUser): ?>
                            <form action="current-build.php?build_id=<?= htmlspecialchars($build_id) ?>" method="post">
                                <textarea id="commentInput" name="comment_text"
                                          placeholder="Введите комментарий" maxlength="300" required></textarea>
                                <button type="submit">Добавить комментарий</button>
                            </form>
                        <?php else: ?>
                            <p>Пожалуйста, <a href="login.php">войдите</a>, чтобы оставлять комментарии.</p>
                        <?php endif; ?>
                    </div>
                    <div class="comments-wrapper">
                        <ul id="commentsList">
                            <?php foreach ($comments as $comment): ?>
                                <li>
                                    <img src="<?= htmlspecialchars($comment['author_avatar'] ?: 'assets/images/Ellipse 5.png') ?>" class="userpic-comment" width="30px">
                                    <div>
                                        <strong><?= htmlspecialchars($comment['author_name']) ?></strong>
                                        <p><?= htmlspecialchars($comment['comment_text']) ?></p>
                                        <div class="comment-actions">
                                            <span class="thumb-up">
                                                <img src="assets/images/Thumb Like.svg" alt="Like" class="commentLikeButton"
                                                     onclick="toggleLike(this, <?= htmlspecialchars($comment['comment_id']) ?>)"
                                                     data-comment-id="<?= htmlspecialchars($comment['comment_id']) ?>" width="25px">
                                                <span class="like-count" id="like-count-<?= htmlspecialchars($comment['comment_id']) ?>">
                                                    <?= htmlspecialchars($comment['like_count']) ?>
                                                </span>
                                            </span>
                                            <span class="thumb-down">
                                                <img src="assets/images/Thumb Like (1).svg" class="commentunLikeButton"
                                                     onclick="toggleUNLike(this, <?= htmlspecialchars($comment['comment_id']) ?>)"
                                                     data-comment-id="<?= htmlspecialchars($comment['comment_id']) ?>" width="25px">
                                                <span class="dislike-count" id="dislike-count-<?= htmlspecialchars($comment['comment_id']) ?>">
                                                    <?= htmlspecialchars($comment['dislike_count']) ?>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($build['picture'])): ?>
            <div class="photo-group">
                <div class="square">
                    <img src="<?= htmlspecialchars($build['picture']) ?>" alt="Изображение сборки">
                </div>
            </div>
        <?php endif; ?>
        <?php if ($currentUser && $currentUser['id'] == $build['author_id']): ?>
            <div class="edit">
                <div class="edit-item">
                    <a id="orange" href="edit-build.php?build_id=<?= $build_id ?>" class="edit-button">
                        <img src="assets/images/Pen.svg" width="29px">Редактировать
                    </a>
                </div>
                <form class="edit-item" action="current-build.php?build_id=<?= $build_id ?>" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить эту сборку?');">
                    <input type="hidden" name="delete_build" value="1">
                    <button id="red" type="submit" class="delete-button">
                        <img src="assets/images/Bucket.svg" width="27px">Удалить
                    </button>
                </form>
            </div>
        <?php endif; ?>
        <div class="type">
            <?php if ($buildType == 'Игровые'): ?>
                <div class="type-item" style="margin-left: -10rem">
                    <span class="game">Игровые</span>
                </div>
            <?php elseif ($buildType == 'Офисные'): ?>
                <div class="type-item">
                    <span class="game">Офисные</span>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<script src="assets/scripts/current.js"></script>
</body>
</html>
