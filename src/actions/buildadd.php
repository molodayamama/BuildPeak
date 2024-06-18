<?php
require_once __DIR__ . '/../helpers.php';

$currentUser = currentUser();

if ($currentUser === false) {
    redirect('/login.php');
}

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = '';

    if (isset($_FILES['buildImage']) && $_FILES['buildImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = __DIR__ . '/../../uploads/';
        $fileName = basename($_FILES['buildImage']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Проверка на допустимые типы файлов
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $validExtensions)) {
            die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Проверка размера файла (например, не более 5MB)
        if ($_FILES['buildImage']['size'] > 5000000) {
            die("Sorry, your file is too large.");
        }

        // Попытка загрузки файла
        if (!move_uploaded_file($_FILES['buildImage']['tmp_name'], $targetFile)) {
            die("Sorry, there was an error uploading your file.");
        }

        $imagePath = 'uploads/' . $fileName; // Относительный путь для хранения в базе данных
    }

    // Получение данных из формы
    $price = $_POST['price'];
    $processor = $_POST['processor'];
    $videocard = $_POST['videocard'];
    $ram = $_POST['ram'];
    $motherboard = $_POST['motherboard'];
    $coolsys = $_POST['coolsys'] ?? '';
    $hdd = $_POST['hdd'] ?? '';
    $ssd = $_POST['ssd'] ?? '';
    $power = $_POST['power'] ?? '';
    $description = $_POST['description'] ?? '';
    $year = $_POST['year'] ?? '';
    $plus1 = $_POST['plus1'] ?? '';
    $plus2 = $_POST['plus2'] ?? '';
    $plus3 = $_POST['plus3'] ?? '';
    $plus4 = $_POST['plus4'] ?? '';
    $minus1 = $_POST['minus1'] ?? '';
    $minus2 = $_POST['minus2'] ?? '';
    $minus3 = $_POST['minus3'] ?? '';
    $minus4 = $_POST['minus4'] ?? '';
    $build_type = $_POST['build_type'];

    // Вставка данных в базу данных
    $stmt = $pdo->prepare("INSERT INTO builds (price, processor, videocard, ram, motherboard, coolsys, hdd, ssd, power, description, year, plus1, plus2, plus3, plus4, minus1, minus2, minus3, minus4, build_type, picture, user_id) VALUES (:price, :processor, :videocard, :ram, :motherboard, :coolsys, :hdd, :ssd, :power, :description, :year, :plus1, :plus2, :plus3, :plus4, :minus1, :minus2, :minus3, :minus4, :build_type, :picture, :user_id)");

    $stmt->execute([
        'price' => $price,
        'processor' => $processor,
        'videocard' => $videocard,
        'ram' => $ram,
        'motherboard' => $motherboard,
        'coolsys' => $coolsys,
        'hdd' => $hdd,
        'ssd' => $ssd,
        'power' => $power,
        'description' => $description,
        'year' => $year,
        'plus1' => $plus1,
        'plus2' => $plus2,
        'plus3' => $plus3,
        'plus4' => $plus4,
        'minus1' => $minus1,
        'minus2' => $minus2,
        'minus3' => $minus3,
        'minus4' => $minus4,
        'build_type' => $build_type,
        'picture' => $imagePath,
        'user_id' => $currentUser['id']
    ]);

    redirect('/build-auth.php');
} else {
    redirect('/my-builds.php');
}
