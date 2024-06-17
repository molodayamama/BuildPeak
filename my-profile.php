<?php
require_once __DIR__ . '/src/helpers.php';

// Получение инфы о текущем пользователе
$currentUser = currentUser();

if ($currentUser === false) {
    redirect('build-auth.php');
}

// Fetch builds added by the current user
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM builds WHERE user_id = :user_id");
$stmt->execute(['user_id' => $currentUser['id']]);
$userBuilds = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой Профиль</title>
    <link rel="stylesheet" href="assets/css/my profile.css">
</head>
<body>
<header class="header">
    <a href="index.php"><img src="assets/images/logo 1.svg" width="182" height="49" alt="Polymatrix Logo" class="logo"></a>
    <nav class="navigation">
        <a href="build-auth.php">Сборки</a>
        <a>|</a>
        <a href="description-log.php">О нас</a>
    </nav>
    
    <div class="user-info">
        <div class="avthor-info" onclick="togglePopup(this)">
            <button class="nickname"><?php echo $currentUser['name'] ?> <img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'assets/images/Ellipse 5.png') ?>" class="userpic" width="30px"></button>
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
            <a href="my-builds.php">Добавить сборку<img src="assets/images/page orange.png" width="19px" class="builds"></a>
            <a href="favorite.php">Избранное<img src="assets/images/user heart.png" width="19px" class="chose"></a>
            <a href="settings.php">Настройки<img src="assets/images/user setting.png" width="19px" class="settings"></a>
            <a href="src/actions/logout.php">Выйти</a>
            </div>
        </nav>
    </aside>
    <section class="products">
        <a class="setting"><img src="assets/images/profile.png" alt="Иконка" class="setting-icon">Мой Профиль</a>
        <form class="form">
            <div>
                <a class="desc">⠀Nickname:</a>
                <?php echo $currentUser['name'] ?>
            </div>
            <div>
                <a class="desc">⠀Почта связи:</a>
                <?php echo $currentUser['email'] ?>
            </div>
            <div>
                <a class="desc">⠀О себе:</a>
                <?php echo $currentUser['about'] ?>
            </div>
        </form>
        <div class="photo-group">
            <img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'assets/images/Rectangle 47.png') ?>" class="photo-box">
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
                            Автор: <?= htmlspecialchars($currentUser['name']) ?>
                        </div>
                        <div class="likes">
                            Количество лайков:
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
        <?php endif; ?>
    </section>
    
</main>
<script src="assets/scripts/my profile.js"></script>
</body>
</html>
