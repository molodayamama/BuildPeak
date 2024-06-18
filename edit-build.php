<?php
require_once __DIR__ . '/src/helpers.php';

// Получение информации о текущем пользователе
$currentUser = currentUser();

if ($currentUser === false) {
    redirect('build-auth.php');
}

$pdo = getPDO();

// Получение ID сборки из URL
$build_id = $_GET['build_id'] ?? 0;

// Fetching build details
$query = $pdo->prepare("SELECT * FROM builds WHERE buildid = :build_id AND user_id = :user_id");
$query->execute(['build_id' => $build_id, 'user_id' => $currentUser['id']]);

$build = $query->fetch();

if (!$build) {
    echo "Сборка не найдена или у вас нет прав для редактирования этой сборки.";
    exit;
}

$build_type = $build['build_type'] ?? 'Неизвестный тип';

// Handling the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = $build['picture']; // Initialize with the existing image path

    // Check if a new image was uploaded
    if (isset($_FILES['buildImage']) && $_FILES['buildImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = __DIR__ . '/uploads/';
        $fileName = basename($_FILES['buildImage']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Проверка на допустимые типы файлов
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $validExtensions) && $_FILES['buildImage']['size'] <= 5000000) {
            if (move_uploaded_file($_FILES['buildImage']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
            }
        }
    }

    $buildType = $_POST['build_type'];

    $updateStmt = $pdo->prepare("UPDATE builds SET 
        price = :price,
        processor = :processor,
        videocard = :videocard,
        ram = :ram,
        motherboard = :motherboard,
        coolsys = :coolsys,
        hdd = :hdd,
        ssd = :ssd,
        power = :power,
        description = :description,
        plus1 = :plus1,
        plus2 = :plus2,
        plus3 = :plus3,
        plus4 = :plus4,
        minus1 = :minus1,
        minus2 = :minus2,
        minus3 = :minus3,
        minus4 = :minus4,
        year = :year,
        build_type = :build_type,
        picture = :picture
        WHERE buildid = :build_id AND user_id = :user_id");

    $updateStmt->execute([
        'price' => $_POST['price'],
        'processor' => $_POST['processor'],
        'videocard' => $_POST['videocard'],
        'ram' => $_POST['ram'],
        'motherboard' => $_POST['motherboard'],
        'coolsys' => $_POST['coolsys'],
        'hdd' => $_POST['hdd'],
        'ssd' => $_POST['ssd'],
        'power' => $_POST['power'],
        'description' => $_POST['description'],
        'plus1' => $_POST['plus1'],
        'plus2' => $_POST['plus2'],
        'plus3' => $_POST['plus3'],
        'plus4' => $_POST['plus4'],
        'minus1' => $_POST['minus1'],
        'minus2' => $_POST['minus2'],
        'minus3' => $_POST['minus3'],
        'minus4' => $_POST['minus4'],
        'year' => $_POST['year'],
        'build_id' => $build_id,
        'user_id' => $currentUser['id'],
        'build_type' => $buildType,
        'picture' => $imagePath
    ]);

    header("Location: current-build.php?build_id=" . $build_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование сборки</title>
    <link rel="stylesheet" href="assets/css/edit.css">
</head>
<body>
<header class="header">
    <a href="build-auth.php"><img src="assets/images/logo 1.svg" width="182" height="49" alt="Polymatrix Logo"/></a>
    <nav class="navigation">
        <a href="build-auth.php">Сборки</a>
        <a>|</a>
        <a href="description-log.php">О нас</a>
    </nav>
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
</header>
<main class="main-content">
    <aside class="sidebar">
    </aside>
    <form  action="edit-build.php?build_id=<?= $build_id ?>" method="post" enctype="multipart/form-data" class="products">
        <div class="build-main">
            <div class="build-details">
                <div class="build-name">
                    <h1>Цена: <input type="number" name="price" value="<?= htmlspecialchars($build['price']) ?>" required></h1>
                    <p><strong>Автор:</strong>  <?= htmlspecialchars($currentUser['name']) ?></p>
                    <p><strong>Год сборки: <input type="number" name="year" value="<?= htmlspecialchars($build['year']) ?>"></strong></p>
                </div>
                <div class="build-specs">
                    <ul>
                        <li class="component-block">
                            <span>Процессор:</span>
                            <textarea name="processor" required><?= htmlspecialchars($build['processor']) ?></textarea></li>
                        <li class="component-block">
                            <span>Видеокарта:</span>
                            <textarea name="videocard" required><?= htmlspecialchars($build['videocard']) ?></textarea></li>
                        <li class="component-block">
                            <span>ОЗУ:</span>
                            <textarea name="ram" required><?= htmlspecialchars($build['ram']) ?></textarea></li>
                        <li class="component-block">
                            <span>Материнская плата:</span>
                            <textarea name="motherboard" required><?= htmlspecialchars($build['motherboard']) ?></textarea></li>
                        <li class="component-block">
                            <span>Система охлаждения:</span>
                            <textarea name="coolsys"><?= htmlspecialchars($build['coolsys']) ?></textarea></li>
                        <li class="component-block">
                            <span>HDD:</span>
                            <textarea name="hdd"><?= htmlspecialchars($build['hdd']) ?></textarea></li>
                        <li class="component-block">
                            <span>SSD:</span>
                            <textarea name="ssd"><?= htmlspecialchars($build['ssd']) ?></textarea></li>
                        <li class="component-block">
                            <span>Блок питания:</span>
                            <textarea name="power"><?= htmlspecialchars($build['power']) ?></textarea></li>
                    </ul>
                </div>
                <div class="build-description">
                    <p class="desc">Описание</p>
                    <label for="desc">
                        <textarea id="desc" name="description" style="padding: 10px; resize: none" oninput="updateCharCount()" maxlength="300">
                            <?= htmlspecialchars($build['description']) ?>
                        </textarea>
                    </label>
                    <p id="charCount">0/300</p>
                </div>
                <div class="plus">
                    <h2>Плюсы сборки:</h2>
                    <ul>
                        <li>• <input type="text" name="plus1" value="<?= htmlspecialchars($build['plus1']) ?>"></li>
                        <li>• <input type="text" name="plus2" value="<?= htmlspecialchars($build['plus2']) ?>"></li>
                        <li>• <input type="text" name="plus3" value="<?= htmlspecialchars($build['plus3']) ?>"></li>
                        <li>• <input type="text" name="plus4" value="<?= htmlspecialchars($build['plus4']) ?>"></li>
                    </ul>
                    <h2>Минусы сборки:</h2>
                    <ul>
                        <li>• <input type="text" name="minus1" value="<?= htmlspecialchars($build['minus1']) ?>"></li>
                        <li>• <input type="text" name="minus2" value="<?= htmlspecialchars($build['minus2']) ?>"></li>
                        <li>• <input type="text" name="minus3" value="<?= htmlspecialchars($build['minus3']) ?>"></li>
                        <li>• <input type="text" name="minus4" value="<?= htmlspecialchars($build['minus4']) ?>"></li>
                    </ul>
                </div>
                <button type="submit" id="orange">Сохранить изменения</button>
            </div>
            <div class="product-card">
                <label for="buildImage" class="button1">+ Загрузить</label>
                <input type="file" name="buildImage" id="buildImage">
            </div>
        </div>
        <?php if (!empty($build['picture'])): ?>
            <div class="photo-group">
                <div class="square">
                    <img src="<?= htmlspecialchars($build['picture']) ?>" alt="Изображение сборки" id="imagePreview">
                </div>
            </div>
        <?php endif; ?>
        <div class="type" style="margin-left: -20rem">
            <div class="type-item">
                <input type="radio" id="game" name="build_type" value="Игровые" required <?= $build['build_type'] == 'Игровые' ? 'checked' : '' ?>>
                <label for="game" class="game">Игровые</label>
            </div>
            <div class="type-item">
                <input type="radio" id="office" name="build_type" value="Офисные" required <?= $build['build_type'] == 'Офисные' ? 'checked' : '' ?>>
                <label for="office" class="office">Офисные</label>
            </div>
        </div>
    </form>
</main>
<script src="assets/scripts/current.js"></script>
<script>
    document.getElementById('buildImage').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.src = URL.createObjectURL(file);
            imagePreview.style.display = 'block';
        }
    });
</script>
</body>
</html>
