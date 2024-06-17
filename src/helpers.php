<?php

session_start();

require_once __DIR__ . '/config.php';

function redirect(string $path)
{
    header("Location: $path");
    die();
}

function logout(): void
{
    unset($_SESSION['user']);
    redirect('/');
}

function setMessage(string $key, string $message) : void
{
    $_SESSION['message'][$key] = $message;
}

function hasMessage(string $key) : bool
{
    return isset($_SESSION['message'][$key]);
}

function getMessage(string $key)
{
    $message = $_SESSION['message'][$key] ?? '';
    unset($_SESSION['message'][$key]);
    return $message;
}

function getPDO() : PDO
{
    try {
        return new \PDO('mysql:host=' . DB_HOST . ';charset=utf8;dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    } catch (\PDOException $e) {
        die("Connection error: {$e->getMessage()}");
    }
}

function findUser(string $email) : array|bool
{
    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE `email` = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

function currentUser(): array|false
{
    $pdo = getPDO();

    if(!isset($_SESSION['user'])) {
        return false;
    }

    $userId = $_SESSION['user']['id'] ?? null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE `id` = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($user) {
        return $user;
    } else {
        // Пользователь не найден в базе данных, возможно, его удалили
        // Можно очистить сессию и вернуть false
        unset($_SESSION['user']);
        return false;
    }
}






