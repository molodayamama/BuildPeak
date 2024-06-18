<?php
require_once __DIR__ . '/src/helpers.php';

// Получение инфы о текущем пользователе
$currentUser = currentUser();

if ($currentUser === false) {
    redirect('build-auth.php');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сборки</title>
    <link rel="stylesheet" href="assets/css/my-built.css">
</head>
<body>
<header class="header">
    <a href="build-auth.php"><img src="assets/images/logo 1.svg" alt="Polymatrix Logo" width="182" height="49" class="logo"></a>
    <nav class="navigation">
        <a href="build-auth.php">Сборки</a>
        <a>|</a>
        <a href="description-log.php">О нас</a>
    </nav>
    <div class="user-info">
        <div class="avthor-info" onclick="togglePopup(this)">
            <button class="nickname"><?=$currentUser['name']?> <img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'assets/images/Ellipse 5.png') ?>" class="userpic" width="30px"></button>
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
        </nav>
    </aside>
    <form action="src/actions/buildadd.php" method="post" enctype="multipart/form-data" class="products">
        <div class="build-main">
            <div class="build-details">
                <div class="build-name">
                    <h1>Цена: <label>
                            <input type="number" name="price" required>
                        </label>
                    </h1>
                    <p><strong>Автор:</strong> <?= htmlspecialchars($currentUser['name']) ?></p>
                    <p><strong>Год сборки: <label>
                                <input type="text" name="year">
                            </label>
                        </strong>
                    </p>
                </div>
                <div class="build-specs">
                    <ul>
                        <li class="component-block">
                            <span>Процессор:</span>
                            <label>
                                <textarea name="processor" maxlength="35" required></textarea>
                            </label>
                        <li class="component-block">
                            <span>Видеокарта:</span>
                            <label>
                                <textarea name="videocard" maxlength="35" required></textarea>
                            </label>
                        <li class="component-block">
                            <span>ОЗУ:</span>
                            <label>
                                <textarea name="ram" maxlength="35" required></textarea>
                            </label>
                        <li class="component-block">
                            <span>Материнская плата:</span>
                            <label>
                                <textarea name="motherboard" maxlength="35" required></textarea>
                            </label>
                        <li class="component-block">
                            <span>Система охлаждения:</span>
                            <label>
                                <textarea name="coolsys" maxlength="35"></textarea>
                            </label>
                        <li class="component-block">
                            <span>HDD:</span>
                            <label>
                                <textarea name="hdd" maxlength="35"></textarea>
                            </label>
                        <li class="component-block">
                            <span>SSD:</span>
                            <label>
                                <textarea name="ssd" maxlength="35"></textarea>
                            </label>
                        <li class="component-block">
                            <span>Блок питания:</span>
                            <label>
                                <textarea name="power" maxlength="35"></textarea>
                            </label>
                    </ul>
                </div>
                <div class="build-description">
                    <p class="desc">Описание</p>
                    <label for="desc">
                        <textarea id="desc" name="description"
                                  style="padding: 10px; resize: none" oninput="updateCharCount()" maxlength="300"></textarea>
                    </label>
                    <p id="charCount">0/300</p>
                </div>
                <div class="plus">
                    <h2>Плюсы сборки:</h2>
                    <ul>
                        <li>• <input type="text" maxlength="22" name="plus1"></li>
                        <li>• <input type="text" maxlength="22" name="plus2"></li>
                        <li>• <input type="text" maxlength="22" name="plus3"></li>
                        <li>• <input type="text" maxlength="22" name="plus4"></li>
                    </ul>
                    <h2>Минусы сборки:</h2>
                    <ul>
                        <li>• <input type="text" maxlength="22" name="minus1"></li>
                        <li>• <input type="text" maxlength="22" name="minus2"></li>
                        <li>• <input type="text" maxlength="22" name="minus3"></li>
                        <li>• <input type="text" maxlength="22" name="minus4"></li>
                    </ul>
                </div>
            </div>
            <div class="product-card">
                <label for="buildImage" class="button1">+ Загрузить</label>
                <input type="file" name="buildImage" id="buildImage">
                <button class="button2" type="submit">Опубликовать</button>
            </div>
        </div>
        <div class="photo-group">
            <div class="square">
                <img id="imagePreview" src="#" alt="Ваше изображение" style="display: none;">
            </div>
        </div>
        <div class="type">
            <div class="type-item">
                <input type="radio" id="game" name="build_type" value="Игровые" required>
                <label for="game" class="game">Игровые</label>
            </div>
            <div class="type-item">
                <input type="radio" id="office" name="build_type" value="Офисные" required>
                <label for="office" class="office">Офисные</label>
            </div>
        </div>
    </form>
</main>
<script src="assets/scripts/current-build.js"></script>
</body>
</html>
