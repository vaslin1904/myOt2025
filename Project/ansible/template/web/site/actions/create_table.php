<?php
require_once '../config.php';

// Только POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не разрешён');
}

$table_name = trim($_POST['table_name'] ?? '');
$columns_raw = trim($_POST['columns'] ?? '');

$response = '';

// --- Валидация имени таблицы ---
if (empty($table_name)) {
    $response .= "<p class='error'>❌ Название таблицы обязательно.</p>";
} elseif (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]{0,63}$/', $table_name)) {
    $response .= "<p class='error'> Недопустимое имя таблицы. Используйте буквы, цифры, подчёркивание (до 64 символов).</p>";
} elseif (in_array(strtolower($table_name), ['user', 'users', 'wp_users', 'mysql', 'information_schema'])) {
    $response .= "<p class='warning'> Имя '<code>" . htmlspecialchars($table_name) . "</code>' может конфликтовать с системными таблицами.</p>";
}

// --- Генерация SQL для полей ---
$default_columns = "`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `Name` VARCHAR(100) NOT NULL, `Age` TINYINT UNSIGNED CHECK (`Age` BETWEEN 0 AND 120)";

if (!empty($columns_raw)) {
    // Разрешаем только безопасные символы в описании столбцов
    // (это не идеально, но мы НЕ используем вставку в запрос напрямую)
    if (preg_match('/[;"\'`\\\\]/', $columns_raw)) {
        $response .= "<p class='error'> Запрещённые символы в описании полей.</p>";
    } else {
        $columns_sql = $columns_raw;
    }
} else {
    $columns_sql = $default_columns;
    $response .= "<p class='info'>ℹ️ Используются поля по умолчанию: <code>id, Name, Age</code>.</p>";
}

// --- Выполнение, если нет ошибок ---
if (empty($response)) {
    try {
        // Экранируем имя таблицы вручную (PDO не поддерживает параметры для имён объектов)
        $safe_table = preg_replace('/[^a-zA-Z0-9_]/', '', $table_name);
        if ($safe_table !== $table_name) {
            throw new Exception('Некорректное имя таблицы после очистки');
        }

        $sql = "CREATE TABLE IF NOT EXISTS `{$safe_table}` ({$columns_sql})
                ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);

        $response .= "<p class='success'> Таблица <code>{$table_name}</code> успешно создана в базе <code>wordpress</code>.</p>";

        // Добавим информацию о структуре
        $stmt = $pdo->query("DESCRIBE `{$safe_table}`");
        $cols = $stmt->fetchAll();
        if ($cols) {
            $response .= "<h4>Структура таблицы:</h4><ul>";
            foreach ($cols as $col) {
                $extra = $col['Extra'] ? " <small>({$col['Extra']})</small>" : '';
                $response .= "<li><code>" . htmlspecialchars($col['Field']) . "</code> — " .
                             htmlspecialchars($col['Type']) .
                             ($col['Null'] === 'NO' ? ' NOT NULL' : '') .
                             ($col['Default'] ? " DEFAULT " . htmlspecialchars($col['Default']) : '') .
                             $extra . "</li>";
            }
            $response .= "</ul>";
        }

    } catch (PDOException $e) {
        $err = $e->getMessage();
        if (strpos($err, 'CREATE command denied') !== false) {
            $response .= "<p class='error'> У вас недостаточно прав для создания таблиц.</p>";
        } elseif (strpos($err, 'syntax') !== false || strpos($err, 'error in your SQL') !== false) {
            $response .= "<p class='error'> Синтаксическая ошибка в описании полей:</p><pre>" . htmlspecialchars($columns_sql) . "</pre>";
        } else {
            $response .= "<p class='error'> Ошибка: " . htmlspecialchars($err) . "</p>";
        }
    } catch (Exception $e) {
        $response .= "<p class='error'> Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Вывод результата
echo $response;
?>
