<?php
require_once __DIR__ . '/src/helpers.php';

// Получение инфы о текущем пользователе
$currentUser = currentUser();

// Получение поискового запроса
$query = $_GET['query'] ?? '';
$priceMin = $_GET['price_min'] ?? '';
$priceMax = $_GET['price_max'] ?? '';
$buildType = $_GET['build_type'] ?? '';

$query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
$priceMin = htmlspecialchars($priceMin, ENT_QUOTES, 'UTF-8');
$priceMax = htmlspecialchars($priceMax, ENT_QUOTES, 'UTF-8');
$buildType = htmlspecialchars($buildType, ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сборки</title>
    <link rel="stylesheet" href="assets/css/reg.css">
    <link rel="shortcut icon" href="assets/images/logo1%201.png">
</head>
<body>
<header class="header">
    <a href="build-auth.php"><img src="assets/images/logo 1.svg" alt="Polymatrix Logo" class="logo"></a>
    <nav class="navigation">
        <a href="description-log.php">О нас</a>
    </nav>
    <?php if ($currentUser === false) { ?>
        <div class="user-info">
            <div class="user-controls">
                <form action="login.php">
                    <button class="login-button">Войти</button>
                </form>
                <form action="register.php">
                    <button class="register-button">Регистрация</button>
                </form>
            </div>
        </div>
    <?php } else { ?>
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
    <?php } ?>
</header>
<main class="main-content">
    <aside class="sidebar">
        <nav class="categories">
            <form action="index.php">
                <button class="lead">Главная</button>
            </form>
            <div>
                <div class="name">Сборки</div>
            </div>
        </nav>
    </aside>
    <section class="products">
        <form action="build-auth.php" method="get" class="search-panel">
            <label><input type="text" id="search-box" name="query" placeholder="поиск..." value="<?= htmlspecialchars($query) ?>"></label>
            <button id="filter-toggle" class="filter-toggle" type="button">
                <img src="assets/images/Filter.png" alt="filter">
            </button>
            <div id="filters" class="filters" style="display: none;">
                <button class="quick-filter" type="button" onclick="applyBuildTypeFilter('Игровые')">Игровые</button>
                <button class="quick-filter" type="button" onclick="applyBuildTypeFilter('Офисные')">Офисные</button>
                <input type="hidden" id="build-type" name="build_type" value="<?= htmlspecialchars($buildType) ?>">
                <div class="filter-inputs">
                    <input type="number" id="price-min" name="price_min" placeholder="Цена: от" value="<?= htmlspecialchars($priceMin) ?>">
                    <input type="number" id="price-max" name="price_max" placeholder="Цена: до" value="<?= htmlspecialchars($priceMax) ?>">
                </div>
                <button type="submit" class="apply-filters">Применить</button>
            </div>
            <a href="build-auth.php" class="delete">
                <img src="assets/images/Filter-none.svg" width="13px">Сбросить фильтры
            </a>
        </form>
        <?php
        // Fetch and display builds
        $conn = getPDO();
        $sql = "
                SELECT builds.*, users.id as user_id, users.name AS username,
                (SELECT COUNT(*) FROM likes WHERE likes.build_id = builds.buildid) as likes_count,
                (SELECT COUNT(*) FROM dislikes WHERE dislikes.build_id = builds.buildid) as dislikes_count
                FROM builds
                JOIN users ON builds.user_id = users.id
            ";

        $conditions = [];
        $params = [];

        // If there's a search query, modify the SQL query to include a WHERE clause
        if (!empty($query)) {
            $conditions[] = "(builds.processor LIKE :query OR builds.videocard LIKE :query OR builds.ram LIKE :query OR builds.motherboard LIKE :query)";
            $params[':query'] = '%' . $query . '%';
        }

        if (!empty($priceMin)) {
            $conditions[] = "builds.price >= :price_min";
            $params[':price_min'] = $priceMin;
        }

        if (!empty($priceMax)) {
            $conditions[] = "builds.price <= :price_max";
            $params[':price_max'] = $priceMax;
        }

        if (!empty($buildType)) {
            $conditions[] = "builds.build_type = :build_type";
            $params[':build_type'] = $buildType;
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $conn->prepare($sql);

        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        $stmt->execute();
        $builds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $builds = array_reverse($builds);

        if (!empty($builds)) {
            // Output build data
            foreach ($builds as $row) {
                $liked = false;
                $disliked = false;
                $favorite = false;

                if ($currentUser) {
                    $stmt = $conn->prepare('SELECT * FROM likes WHERE build_id = :build_id AND user_id = :user_id');
                    $stmt->execute(['build_id' => $row['buildid'], 'user_id' => $currentUser['id']]);
                    $liked = $stmt->fetch() !== false;

                    $stmt = $conn->prepare('SELECT * FROM dislikes WHERE build_id = :build_id AND user_id = :user_id');
                    $stmt->execute(['build_id' => $row['buildid'], 'user_id' => $currentUser['id']]);
                    $disliked = $stmt->fetch() !== false;

                    $stmt = $conn->prepare('SELECT * FROM heart WHERE build_id = :build_id AND user_id = :user_id');
                    $stmt->execute(['build_id' => $row['buildid'], 'user_id' => $currentUser['id']]);
                    $favorite = $stmt->fetch() !== false;
                }

                echo '<div class="product-card">
                    <div class="card-section left">
                        <img src="' . htmlspecialchars($row['picture']) . '" alt="Изображение сборки" class="pcimage">
                    </div>
                    <div class="card-section center">
                        <div class="author-info">
                            Автор: <a href="user-profile.php?user_id=' . htmlspecialchars($row["user_id"]) . '" style="color: black;">' . htmlspecialchars($row["username"]) . '</a>
                        </div>
                        <span class="thumb-up">
                            <img src="assets/images/' . ($liked ? 'blacked-up.png' : 'Thumb Like.svg') . '"
                             alt="Like" class="likeButton" data-build-id="' . htmlspecialchars($row['buildid']) . '" 
                             onclick="toggleLikeDislike(this,' . htmlspecialchars($row['buildid']) . ',\'like\')">
                             <span class="counter" id="build-likes-count-' . htmlspecialchars($row['buildid']) . '">' . htmlspecialchars($row['likes_count']) .'</span>
                        </span>
                        <span class="thumb-down">
                            <img src="assets/images/' . ($disliked ? 'blacked-down.png' : 'Thumb Like (1).svg') . '"
                             class="unlikeButton" data-build-id="' . htmlspecialchars($row['buildid']) . '" 
                             onclick="toggleLikeDislike(this,' . htmlspecialchars($row['buildid']) . ',\'dislike\')">
                             <span class="counter-1" id="build-dislikes-count-' . htmlspecialchars($row['buildid']) . '">' . htmlspecialchars($row['dislikes_count']) .'</span>
                        </span>
                    </div>
                    <div class="card-section right">
                        <div class="product-details">
                            <div class="product-title">Цена: ' . htmlspecialchars($row["price"]) . '</div>
                            <h4 class="com">Комплектующие</h4>
                            <ul class="specs-list">
                                <em>
                                    <li>Процессор: ' . htmlspecialchars($row["processor"]) . '</li>
                                    <li>Видеокарта: ' . htmlspecialchars($row["videocard"]) . '</li>
                                    <li>ОЗУ: ' . htmlspecialchars($row["ram"]) . '</li>
                                    <li>Материнская плата: ' . htmlspecialchars($row["motherboard"]) . '</li>
                                </em>
                            </ul>
                            <a href="/current-build.php?build_id=' . $row['buildid'] . '" class="card-link">Перейти на сборку</a>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo "Нет сборок для отображения.";
        }
        ?>
    </section>
</main>
<script src="assets/scripts/build-auth.js"></script>
<script>
    function applyBuildTypeFilter(type) {
        document.getElementById('build-type').value = type;
        document.querySelector('.search-panel').submit();
    }
    function toggleLikeDislike(element, buildId, action) {
        fetch(`/src/actions/${action}.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ build_id: buildId })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const likeButton = document.querySelector(`.likeButton[data-build-id="${buildId}"]`);
                const dislikeButton = document.querySelector(`.unlikeButton[data-build-id="${buildId}"]`);

                if (action === 'like') {
                    element.src = data.liked ? 'assets/images/blacked-up.png' : 'assets/images/Thumb Like.png';
                    if (data.liked) {
                        dislikeButton.src = 'assets/images/Thumb Like (1).png';
                    }
                } else if (action === 'dislike') {
                    element.src = data.disliked ? 'assets/images/blacked-down.png' : 'assets/images/Thumb Like (1).png';
                    if (data.disliked) {
                        likeButton.src = 'assets/images/Thumb Like.png';
                    }
                }

                updateBuildCounts(buildId);
            })
            .catch(error => console.error('Error:', error));
    }
    // Функция для обновления количества лайков и дизлайков для сборок
</script>
</body>
</html>
