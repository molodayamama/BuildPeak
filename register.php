<?php
require_once __DIR__ . '/src/helpers.php'
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
    <a href="index.php"><img src="/assets/images/logo 1.svg" width="182" height="49" class="logo"></a>
    <div class="container">
        <div class="form-container">
            <form action="src/actions/register.php" method="post">
                <h2>ДОБРО ПОЖАЛОВАТЬ</h2>
                <input type="text" placeholder="Имя" name="name" required>
                <input type="email" placeholder="Email" name="email" required>
                <input type="password" placeholder="Пароль" name="password" required>
                <label for="remember-me">
                    <input type="checkbox" id="remember-me" name="remember-me"> Запомнить меня
                </label>
                <button type="submit" class="register">Зарегистрироваться</button>
            </form>
            <div class="akk">
                <p>Уже есть аккаунт? <a href="login.php" class="in">Войти</a></p>
            </div>
        </div>
        <img src="/assets/images/3796072.jpg" class="pic" />
    </div>
</body>
</html>
