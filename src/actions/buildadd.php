<?php

require_once __DIR__ . '/../helpers.php';

$user_id = $_SESSION['user']['id'];

$price = $_POST['price'];
$processor = $_POST['processor'];
$videocard = $_POST['videocard'];
$ram = $_POST['ram'];
$motherboard = $_POST['motherboard'];
$coolsys = $_POST['coolsys'];
$hdd = $_POST['hdd'];
$ssd = $_POST['ssd'];
$power = $_POST['power'];
$description= $_POST['description'];
$year = $_POST['year'];
$plus1 = $_POST['plus1'];
$plus2 = $_POST['plus2'];
$plus3 = $_POST['plus3'];
$plus4 = $_POST['plus4'];
$minus1 = $_POST['minus1'];
$minus2 = $_POST['minus2'];
$minus3 = $_POST['minus3'];
$minus4 = $_POST['minus4'];
$build_type = $_POST['build_type'];

$pdo = getPDO();

// Обработка загрузки изображения
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

// Добавление данных сборки в базу данных
$query = "INSERT INTO builds (price, processor, videocard, ram, motherboard,
                    coolsys, hdd, ssd, power, description,
                    plus1, plus2, plus3, plus4, minus1, minus2, minus3, minus4, year, user_id, picture, build_type) 
VALUES (:price, :processor, :videocard, :ram, :motherboard,
        :coolsys, :hdd, :ssd, :power, :description,
        :plus1, :plus2, :plus3, :plus4, :minus1, :minus2, :minus3, :minus4, :year, :user_id, :picture, :build_type)";

$params = [
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
    'plus1' => $plus1,
    'plus2' => $plus2,
    'plus3' => $plus3,
    'plus4' => $plus4,
    'minus1' => $minus1,
    'minus2' => $minus2,
    'minus3' => $minus3,
    'minus4' => $minus4,
    'year' => $year,
    'user_id' => $user_id,
    'picture' => $imagePath,
    'build_type' => $build_type
];

$stmt = $pdo->prepare($query);

try {
    $stmt->execute($params);
} catch (\Exception $e) {
    die($e->getMessage());
}

// Перенаправление после успешного добавления сборки
redirect('/../build-auth.php');

