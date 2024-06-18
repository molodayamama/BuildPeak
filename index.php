<?php
require_once __DIR__ . '/src/helpers.php';

// Получение инфы о текущем пользователе
$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildPeak</title>
    <link href="assets/css/index.css" rel="stylesheet" >
</head>
<body>
<header class="site-header">
    <a href="index.php"><img src="assets/images/logo 1.svg" alt="Polymatrix Logo" class="logo"></a>
    <nav class="site-navigation">
        <div class="under-nav">
        <a href="build-auth.php">Сборки</a>
        <a>|</a>
        <a href="description-log.php">О нас</a>
        </div>
        <?php if ($currentUser === false) { ?>
            <div class="search-and-buttons">
                <form action="register.php" method="post">
                    <button class="profile">
                        <bold>Регистрация</bold>
                    </button>
                </form>
                <form action="login.php" method="post">
                    <button class="profile">
                        <bold>Вход</bold>
                    </button>
                </form>
            </div>
        <?php } else { ?>
            <div class="search-and-buttons">
                <form action="my-profile.php" method="post" class="profile">
                    <button class="profile">
                        <bold>Мой профиль</bold>
                    </button>
                </form>
            </div>
        <?php } ?>
    </nav>
</header>
<main>
    <section class="hero">
        <form action="build-auth.php" class="hero-content">
            <h1>Цифровое <p>ПОРТФОЛИО</p>
            </h1>
            <h2>для публикации ваших <p>сборок ПК</p>
            </h2>
            <img src="assets/images/Line 2.png" alt="line" class="vector" />
            <p class="description">Сайт предлагает всё необходимое для публикации ваших сборок персональных
                компьютеров, их характеристик, а также тестов в различных сферах</p>
            <button class="cta-button">Оставить работы</button>
        </form>
    </section>
    <img src="assets/images/pc21.png" alt="Comp" class="pc">
    <section class="stats">
        <div class="stat-item1">
                <span>
                    <p>6+</p> сборок
                </span>
        </div>
        <div class="stat-item2">
                <span>
                    <p>10+</p> тестов
                </span>
        </div>
        <div class="stat-item3">
                <span>
                    <p>5+</p> юзеров
                </span>
        </div>
    </section>
</main>
<footer>
</footer>
</body>

</html>