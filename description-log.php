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
    <title>О нас</title>
    <link rel="stylesheet" href="assets/css/description log.css">
    <link rel="stylesheet" href="assets/css/description unlog.css">
    <link rel="shortcut icon" href="assets/images/logo1%201.png">
</head>
<body>
    <header class="header">
        <a href="build-auth.php"><img src="assets/images/logo 1.svg" width="182" height="49" alt="Polymatrix Logo" class="logo"></a>
        <nav class="navigation">
            <a href="build-auth.php">Сборки</a>
        </nav>
        <?php if ($currentUser === false) { ?>
            <div class="user-controls">
                <button class="login-button">Войти</button>
                <button class="register-button">Регистрация</button>
            </div>
        <?php } else { ?>
            <div class="user-info">
                <div class="avthor-info" onclick="togglePopup(this)">
                    <button class="nickname"><?php echo $currentUser['name']?><img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'assets/images/Ellipse 5.png') ?>" class="userpic" width="30px"></button>
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
    <main>
        <h1 class="about">О нас</h1>
        <div class="container">
            <section class="about-us">
                <h1></h1>
                <p>⠀Мы рады приветствовать вас на нашем сайте. Команда <a class="poly">Polymatrix</a> - это дружный состав, который делает вашу жизнь легче.</p>
                <p>⠀Для этого наша команда создала <a class="poly"> Web-Portfolio</a>, которое сможет помочь многим людям с выбором комплектующих для их ПК.</p>
                <p>⠀Идея нашего продукта заключается в том, что пользователи, которые уже имеют опыт в сборках ПК и свои собственные сборки, могли делиться ими и рассказать о плюсах и минусах.</p>
                <p>⠀С помощью нашего проекта вы сможете выбрать тот вариант, который нужен именно вам. Надеюсь, что мы хоть как-то облегчим столь трудный выбор.</p>
                <p>Приятного пользования. <br>С уважением, команда <a class="poly">Polymatrix</a>.</br></p>
            </section>
        </div>
    </main>
    <footer>
    </footer>
    <script src="assets/scripts/description log.js"></script>
</body>
</html>
