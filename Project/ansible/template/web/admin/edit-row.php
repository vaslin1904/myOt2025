<?php
// Редактирование строки
require_once __DIR__ . '/../wp-config.php';

if (!isset($_GET['table']) || !isset($_GET['id'])) {
    die("Не указаны параметры");
}

$table = $_GET['table'];
$id = $_GET['id'];
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Получаем структуру таблицы
$fields = [];
$result = $mysqli->query("DESCRIBE `$table`");
while ($row = $result->fetch_assoc()) {
    $fields[] = $row;
}

// Получаем текущие данные
$row_data = [];
$result = $mysqli->query("SELECT * FROM `$table` WHERE id = $id");
if ($row = $result->fetch_assoc()) {
    $row_data = $row;
} else {
    die("Строка не найдена");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updates = [];
    $values = [];

    foreach ($fields as $field) {
        $name = $field['Field'];
        if ($name !== 'id') {
            $updates[] = "`$name` = ?";
            $values[] = $_POST[$name];
        }
    }

    $sql = "UPDATE `$table` SET " . implode(', ', $updates) . " WHERE id = ?";
    $values[] = $id;

    $stmt = $mysqli->prepare($sql);
    $types = str_repeat('s', count($values) - 1) . 'i';
    $stmt->bind_param($types, ...$values);

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
    <title>Редактировать строку</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        input, select { margin: 5px 0; width: 200px; }
        .btn { padding: 8px 15px; background: #0073aa; color: white; border: none; cursor: pointer; }
        .btn:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Редактировать строку в таблице: <?= htmlspecialchars($table) ?></h1>

    <form method="post">
        <?php foreach ($fields as $field): ?>
            <p>
                <label><?= htmlspecialchars($field['Field']) ?>:</label><br>
                <?php if ($field['Type'] == 'text'): ?>
                    <textarea name="<?= htmlspecialchars($field['Field']) ?>"><?= htmlspecialchars($row_data[$field['Field']]) ?></textarea>
                <?php else: ?>
                    <input type="text" name="<?= htmlspecialchars($field['Field']) ?>" value="<?= htmlspecialchars($row_data[$field['Field']]) ?>">
                <?php endif; ?>
            </p>
        <?php endforeach; ?>

        <button type="submit" class="btn">Сохранить изменения</button>
    </form>

    <p><a href="index.php?table=<?= urlencode($table) ?>">← Назад к таблице</a></p>
</body>
</html>
