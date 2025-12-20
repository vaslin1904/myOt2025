<?php
// Добавление строки в таблицу
require_once __DIR__ . '/../wp-config.php';

if (!isset($_GET['table'])) {
    die("Не указана таблица");
}

$table = $_GET['table'];
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Получаем структуру таблицы
$fields = [];
$result = $mysqli->query("DESCRIBE `$table`");
while ($row = $result->fetch_assoc()) {
    $fields[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $values = [];
    foreach ($fields as $field) {
        $name = $field['Field'];
        $values[$name] = $_POST[$name];
    }

    // Подготовка SQL
    $sql = "INSERT INTO `$table` (";
    $sql .= implode(', ', array_map(fn($f) => "`{$f['Field']}`", $fields));
    $sql .= ") VALUES (";
    $sql .= implode(', ', array_fill(0, count($fields), '?'));
    $sql .= ")";

    $stmt = $mysqli->prepare($sql);
    $types = str_repeat('s', count($fields)); // Все поля - строки
    $stmt->bind_param($types, ...array_values($values));

    if ($stmt->execute()) {
        header("Location: index.php?table=" . urlencode($table));
        exit;
    } else {
        echo "Ошибка: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Добавить строку</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        input, select { margin: 5px 0; width: 200px; }
        .btn { padding: 8px 15px; background: #0073aa; color: white; border: none; cursor: pointer; }
        .btn:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Добавить строку в таблицу: <?= htmlspecialchars($table) ?></h1>

    <form method="post">
        <?php foreach ($fields as $field): ?>
            <p>
                <label><?= htmlspecialchars($field['Field']) ?>:</label><br>
                <?php if ($field['Type'] == 'text'): ?>
                    <textarea name="<?= htmlspecialchars($field['Field']) ?>"></textarea>
                <?php else: ?>
                    <input type="text" name="<?= htmlspecialchars($field['Field']) ?>">
                <?php endif; ?>
            </p>
        <?php endforeach; ?>

        <button type="submit" class="btn">Добавить строку</button>
    </form>

    <p><a href="index.php?table=<?= urlencode($table) ?>">← Назад к таблице</a></p>
</body>
</html>
