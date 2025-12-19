<?php
// Главная страница админа
require_once __DIR__ . '/../wp-config.php';

// Подключение к БД
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Получение всех таблиц
$tables = [];
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Админ - Таблицы</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        a { color: #0073aa; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { display: inline-block; padding: 5px 10px; background: #0073aa; color: white; text-decoration: none; border-radius: 3px; }
        .btn:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Админ - Таблицы</h1>

    <p><a href="add-table.php" class="btn">Добавить таблицу</a></p>

    <?php foreach ($tables as $table): ?>
        <h2><?= htmlspecialchars($table) ?></h2>
        <p>
            <a href="add-row.php?table=<?= urlencode($table) ?>" class="btn">Добавить строку</a>
            <a href="?table=<?= urlencode($table) ?>" class="btn">Просмотреть</a>
        </p>

        <?php
        if (isset($_GET['table']) && $_GET['table'] == $table) {
            $result = $mysqli->query("SELECT * FROM `$table`");
            if ($result && $result->num_rows > 0) {
                echo "<table>";
                echo "<tr>";
                while ($field = $result->fetch_field()) {
                    echo "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                echo "<th>Действия</th></tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $cell) {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    echo "<td>";
                    echo "<a href='edit-row.php?table=" . urlencode($table) . "&id=" . urlencode($row['id']) . "'>Редактировать</a> ";
                    echo "<a href='delete-row.php?table=" . urlencode($table) . "&id=" . urlencode($row['id']) . "' onclick='return confirm(\"Удалить?\");'>Удалить</a>";
                    echo "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Нет данных</p>";
            }
        }
        ?>
    <?php endforeach; ?>

    <hr>
    <p><a href="../">← Вернуться на сайт</a></p>
</body>
</html>
