<?php
require_once __DIR__ . '/src/helpers.php'
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/css/vhod.css">
</head>

<body>
<a href="index.php"><img src="assets/images/logo 1.svg" width="182" height="49" class="logo"></a>
<div class="container">
    <div class="form-container">
        <form action="src/actions/login.php" method="post">
            <h2>ДОБРО ПОЖАЛОВАТЬ</h2>
            <?php if(hasMessage('error')): ?>
                <div><?php echo getMessage('error'); ?></div>
            <?php endif; ?>
            <input type="email" placeholder="buildpeak@email.com" name="email" required>
            <input type="password" placeholder="Пароль" name="password" required>
            <label for="remember-me">
                <input type="checkbox" id="remember-me" name="remember-me"> Запомнить меня
            </label>
            <button type="submit" class="register">Вход</button>
        </form>
    </div>
    <img src="assets/images/5264250.jpg" class="pic">
</div>
</body>

</html>