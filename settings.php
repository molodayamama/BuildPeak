<?php
require_once __DIR__ . '/src/helpers.php';

// Получение информации о текущем пользователе
$currentUser = currentUser();

if ($currentUser === false) {
    redirect('build-auth.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentName = $currentUser['name'];
    $currentEmail = $currentUser['email'];
    $currentAbout = $currentUser['about'] ?? '';
    $currentAvatar = $currentUser['avatar'] ?? '';

    $newName = $_POST['name'] ?: $currentName;
    $newEmail = $_POST['email'] ?: $currentEmail;
    $newAbout = $_POST['about'] ?: $currentAbout;

    // Обработка загрузки аватарки
    $avatarPath = $currentAvatar;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $targetDir = __DIR__ . '/uploads/avatars/';
        $fileName = basename($_FILES['avatar']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Проверка на допустимые типы файлов
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $validExtensions)) {
            die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Проверка размера файла (например, не более 5MB)
        if ($_FILES['avatar']['size'] > 5000000) {
            die("Sorry, your file is too large.");
        }

        // Попытка загрузки файла
        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
            die("Sorry, there was an error uploading your file.");
        }

        $avatarPath = '/uploads/avatars/' . $fileName; // Относительный путь для хранения в базе данных
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, about = :about, avatar = :avatar WHERE id = :id");
    $stmt->execute([
        'name' => $newName,
        'email' => $newEmail,
        'about' => $newAbout,
        'avatar' => $avatarPath,
        'id' => $currentUser['id'],
    ]);

    // Обновление информации в сессии
    $_SESSION['user']['name'] = $newName;
    $_SESSION['user']['email'] = $newEmail;
    $_SESSION['user']['about'] = $newAbout;
    $_SESSION['user']['avatar'] = $avatarPath;

    echo "Информация обновлена";
    // Optionally, redirect to a confirmation page or back to the settings page
    // redirect('settings.php');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки</title>
    <link rel="stylesheet" href="assets/css/settings.css">
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
        <div class="avthor-info" onclick="togglePopup(this)">
            <button class="nickname"><?= htmlspecialchars($currentUser['name']) ?> <img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'assets/images/Ellipse 5.png') ?>" class="userpic" width="30px"></button>
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
                <a href="favorite.php">Избранное<img src="assets/images/user heart.png" width="19px" class="chose"></a>
                <a href="src/actions/logout.php">Выйти</a>
            </div>
        </nav>
    </aside>
    <section class="products">
        <a class="setting"><img src="assets/images/settings.png" alt="Иконка" class="setting-icon">Настройки</a>
        <form action="settings.php" class="form" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <label for="email">⠀⠀Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($currentUser['email']) ?>">
            </div>
            <div class="input-group">
                <label for="nickname">⠀⠀Nickname</label>
                <input type="text" id="nickname" name="name" value="<?= htmlspecialchars($currentUser['name']) ?>">
            </div>
            <div class="input-group">
                <label for="about">⠀⠀О себе</label>
                <textarea id="about" name="about" class="input-group"><?= htmlspecialchars($currentUser['about']) ?></textarea>
            </div>
            <div class="input-group">
                <label for="avatar">Аватарка</label>
                <input type="file" id="button1" name="avatar">
            </div>
            <button id="orange" type="submit">Сохранить изменения</button>
            <div class="photo-group">
                <label class="photo">Фото</label>
                <img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'assets/images/Ellipse 5.png') ?>" class="photo-box" alt="Аватарка">
            </div>
        </form>
    </section>
</main>
<script src="assets/scripts/settings.js"></script>
</body>
</html>
