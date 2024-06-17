<?php

require_once __DIR__ . '/../helpers.php';

$email = $_POST['email'];
$password = $_POST['password'];

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

redirect('/../build-auth.php');