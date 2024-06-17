<?php

require_once __DIR__ . '/../helpers.php';

// Данные из POST
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Валидация

$_SESSION['validation'] = [];

if (!empty($_SESSION['validation'])) {
    redirect('/register.php');
}

$pdo = getPDO();

$query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";

$params = [
    'name' => $name,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT)
];
$stmt = $pdo->prepare($query);

try {
    $stmt->execute($params);
} catch (\Exception $e) {
    die($e ->getMessage());
}

redirect('/login.php');