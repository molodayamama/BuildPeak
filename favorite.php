<?php
require_once __DIR__ . '/src/helpers.php';

// Get current user info
$currentUser = currentUser();

if ($currentUser === false) {
    redirect('build-auth.php');
}

// Fetch liked builds by the current user
$pdo = getPDO();

// Fetch favorite builds
$stmt = $pdo->prepare('
    SELECT builds.*, users.name AS username
    FROM builds
    JOIN heart ON builds.buildid = heart.build_id
    JOIN users ON builds.user_id = users.id
    WHERE heart.user_id = :user_id
');
$stmt->execute(['user_id' => $currentUser['id']]);
$likedBuilds = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное</title>
    <link rel="stylesheet" href="assets/css/favorite.css">
    <link rel="shortcut icon" href="assets/images/logo1%201.png">
</head>
<body>
<header class="header">
    <a href="build-auth.php"><img src="assets/images/logo 1.svg" width="182" height="49" alt="Polymatrix Logo" class="logo"></a>
    <nav class="navigation">
        <a href="build-auth.php">Сборки</a>
        <a>|</a>
        <a href="description-log.php">О нас</a>
    </nav>
    <div class="user-info">
        <div class="author-info" onclick="togglePopup(this)">
            <button class="nickname"><?= htmlspecialchars($currentUser['name']) ?> <img src="assets/images/Ellipse 5.png" class="userpic" width="30px"></button>
        </div>
        <div id="popup" style="display: none;">
            <a href="my-profile.php">Мой профиль</a>
            <a href="settings.php">Настройки</a>
            <a href="favorite.php">Избранное</a>
            <p><a href="src/actions/logout.php">Выйти</a></p>
        </div>
    </div>
</header>
<main class="main-content">
    <aside class="sidebar">
        <nav class="categories">
            <div class="best">
                <a href="my-profile.php">Мой профиль<img src="assets/images/user.png" width="19px" class="profile"></a>
                <a href="my-builds.php">Добавить сборку<img src="assets/images/page orange.png" width="19px" class="builds"></a>
                <a href="settings.php">Настройки<img src="assets/images/user setting.png" width="19px" class="settings"></a>
            </div>
        </nav>
    </aside>
    <section class="products">
        <a class="favorite"><img src="assets/images/favorite.png" alt="Иконка" class="favorite-icon">Избранное</a>
        <?php if (!empty($likedBuilds)): ?>
            <?php foreach (array_reverse($likedBuilds) as $build): ?>
                <div class="product-card">
                    <div class="card-section left">
                        <img src="<?= htmlspecialchars($build['picture']) ?>" alt="Изображение сборки" class="pcimage">
                    </div>
                    <div class="card-section center">
                        <div class="author-info">
                            Автор: <a href="user-profile.php?user_id=<?= htmlspecialchars($build["user_id"])?>" style="color: black;"><?= htmlspecialchars($build["username"])?></a>
                        </div>
                    </div>
                    <div class="card-section right">
                        <div class="product-details">
                            <div class="product-title">Цена: <?= htmlspecialchars($build['price']) ?></div>
                            <h4 class="com">Комплектующие</h4>
                            <ul class="specs-list">
                                <em>
                                    <li>Процессор: <?= htmlspecialchars($build['processor']) ?></li>
                                    <li>Видеокарта: <?= htmlspecialchars($build['videocard']) ?></li>
                                    <li>ОЗУ: <?= htmlspecialchars($build['ram']) ?></li>
                                    <li>Материнская плата: <?= htmlspecialchars($build['motherboard']) ?></li>
                                </em>
                            </ul>
                            <a href="/current-build.php?build_id=<?= $build['buildid'] ?>" class="card-link">Перейти на сборку</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<script src="assets/scripts/favorite.js"></script>
</body>
</html>
