<?php
// Добавление новой таблицы
require_once __DIR__ . '/../wp-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table_name = $_POST['table_name'];
    $columns = $_POST['columns'];

    // Проверка имени таблицы
    if (empty($table_name) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table_name)) {
        die("Неверное имя таблицы");
    }

    // Генерация SQL
    $sql = "CREATE TABLE `$table_name` (";
    $first = true;
    foreach ($columns as $col) {
        if (!$first) $sql .= ", ";
        $first = false;
        $sql .= "`" . $col['name'] . "` " . $col['type'];
        if (isset($col['length']) && !empty($col['length'])) {
            $sql .= "(" . intval($col['length']) . ")";
        }
        if (isset($col['not_null']) && $col['not_null'] == 'on') {
            $sql .= " NOT NULL";
        }
    }
    $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->query($sql) === TRUE) {
        header("Location: index.php");
        exit;
    } else {
        echo "Ошибка: " . $mysqli->error;
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Добавить таблицу</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        input, select { margin: 5px 0; }
        .column-row { margin: 10px 0; border: 1px solid #ccc; padding: 10px; }
        .btn { padding: 8px 15px; background: #0073aa; color: white; border: none; cursor: pointer; }
        .btn:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Добавить таблицу</h1>

    <form method="post">
        <p>
            <label>Имя таблицы:</label><br>
            <input type="text" name="table_name" required>
        </p>

        <div id="columns-container">
            <div class="column-row">
                <label>Столбец 1:</label><br>
                <input type="text" name="columns[0][name]" placeholder="Имя" required><br>
                <select name="columns[0][type]">
                    <option value="VARCHAR">VARCHAR</option>
                    <option value="INT">INT</option>
                    <option value="TEXT">TEXT</option>
                    <option value="DATETIME">DATETIME</option>
                </select>
                <input type="number" name="columns[0][length]" placeholder="Длина (необязательно)">
                <input type="checkbox" name="columns[0][not_null]" value="on"> NOT NULL
            </div>
        </div>

        <button type="button" onclick="addColumn()">Добавить столбец</button>
        <button type="submit" class="btn">Создать таблицу</button>
    </form>

    <script>
        let columnCount = 1;
        function addColumn() {
            const container = document.getElementById('columns-container');
            const newRow = document.createElement('div');
            newRow.className = 'column-row';
            newRow.innerHTML = `
                <label>Столбец ${++columnCount}:</label><br>
                <input type="text" name="columns[${columnCount-1}][name]" placeholder="Имя" required><br>
                <select name="columns[${columnCount-1}][type]">
                    <option value="VARCHAR">VARCHAR</option>
                    <option value="INT">INT</option>
                    <option value="TEXT">TEXT</option>
                    <option value="DATETIME">DATETIME</option>
                </select>
                <input type="number" name="columns[${columnCount-1}][length]" placeholder="Длина (необязательно)">
                <input type="checkbox" name="columns[${columnCount-1}][not_null]" value="on"> NOT NULL
            `;
            container.appendChild(newRow);
        }
    </script>

    <p><a href="index.php">← Назад к таблицам</a></p>
</body>
</html>
