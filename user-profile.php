<?php
require_once __DIR__ . '/src/helpers.php';

// Получение ID пользователя из URL
$userId = $_GET['user_id'] ?? null;

if ($userId === null) {
    redirect('build-auth.php');
}

// Fetch user information
$pdo = getPDO();
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$userStmt->execute(['user_id' => $userId]);
$profileUser = $userStmt->fetch();

if ($profileUser === false) {
    redirect('build-auth.php');
}

// Fetch builds added by the user
$buildStmt = $pdo->prepare("
    SELECT builds.*, COUNT(likes.build_id) AS likes_count
    FROM builds
    LEFT JOIN likes ON builds.buildid = likes.build_id
    WHERE builds.user_id = :user_id
    GROUP BY builds.buildid
");
$buildStmt->execute(['user_id' => $userId]);
$userBuilds = $buildStmt->fetchAll(PDO::FETCH_ASSOC);

// Получение инфы о текущем пользователе
$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="assets/css/my profile.css">
    <link rel="shortcut icon" href="assets/images/logo1%201.png">
</head>
<body>
<header class="header">
    <a href="index.php"><img src="assets/images/logo 1.svg" width="182" height="49" alt="Polymatrix Logo" class="logo"></a>
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
                <button class="nickname"><?php echo htmlspecialchars($currentUser['name']); ?> <img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'assets/images/Ellipse 5.png') ?>" class="userpic" width="45"></button>
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
            <div class="best">
                <a href="my-profile.php">Мой профиль<img src="assets/images/user.png" width="19px" class="profile"></a>
                <a href="my-builds.php">Добавить сборку<img src="assets/images/page orange.png" width="19px" class="builds"></a>
                <a href="favorite.php">Избранное<img src="assets/images/user heart.png" width="19px" class="chose"></a>
                <a href="settings.php">Настройки<img src="assets/images/user setting.png" width="19px" class="settings"></a>
                <a href="src/actions/logout.php">Выйти</a>
            </div>
        </nav>
    </aside>
    <section class="products">
        <a class="setting"><img src="assets/images/profile.png" alt="Иконка" class="setting-icon">Профиль</a>
        <form class="form">
            <div>
                <a class="desc">⠀Nickname:</a>
                <?php echo htmlspecialchars($profileUser['name']) ?>
            </div>
            <div>
                <a class="desc">⠀Почта связи:</a>
                <?php echo htmlspecialchars($profileUser['email']) ?>
            </div>
            <div>
                <a class="desc">⠀О себе:</a>
                <?php echo htmlspecialchars($profileUser['about']) ?>
            </div>
        </form>
        <div class="photo-group">
            <img src="<?= htmlspecialchars($profileUser['avatar'] ?: 'assets/images/Rectangle 47.png') ?>" class="photo-box">
        </div>
        <a class="mybuilds"><img src="assets/images/page white.png" alt="Иконка" class="mybuilds-icon">Сборки</a>
        <?php if (!empty($userBuilds)): ?>
            <?php foreach (array_reverse($userBuilds) as $build): ?>
                <div class="product-card">
                    <div class="card-section left">
                        <img src="<?= htmlspecialchars($build['picture']) ?>" alt="Изображение сборки" class="pcimage">
                    </div>
                    <div class="card-section center">
                        <div class="author-info">
                            Автор: <?= htmlspecialchars($profileUser['name']) ?>
                        </div>
                        <div class="likes">
                            Количество лайков: <?= htmlspecialchars($build['likes_count']) ?>
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
                            <a href="/current-build.php?build_id=<?= $build['buildid'] ?>" style="color: black; margin-left: 40px;">Перейти на сборку</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>У пользователя нет сборок.</p>
        <?php endif; ?>
    </section>
</main>
<script src="assets/scripts/my profile.js"></script>
</body>
</html>
