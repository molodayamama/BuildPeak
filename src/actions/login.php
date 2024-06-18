<?php

require_once __DIR__ . '/../helpers.php';

$email = $_POST['email'];
$password = $_POST['password'];
$rememberMe = isset($_POST['remember-me']);

$user = findUser($email);

if (!$user) {
    setMessage('error', "Пользователь {$email} не найден");
    redirect('/login.php');
}

if (!password_verify($password, $user['password'])) {
    setMessage('error', "Неверный пароль");
    redirect('/login.php');
}

$_SESSION['user']['id'] = $user['id'];

if ($rememberMe) {
    // Установите cookie на 30 дней
    setcookie('remember_me', $user['id'], time() + (86400 * 30), "/");
}

redirect('/../build-auth.php');
