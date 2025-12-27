<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не разрешён');
}

$table_name = trim($_POST['table_name'] ?? '');

$response = '';

// --- Валидация имени таблицы ---
if (empty($table_name)) {
    $response .= "<p class='error'>Название таблицы обязательно.</p>";
} elseif (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]{0,63}$/', $table_name)) {
    $response .= "<p class='error'>Недопустимое имя таблицы. Допустимы: буквы, цифры, подчёркивание (от 1 до 64 символов).</p>";
} elseif (in_array(strtolower($table_name), [
    'wp_users', 'wp_posts', 'wp_options', 'wp_comments',
    'users', 'user', 'mysql', 'information_schema', 'performance_schema'
])) {
    $response .= "<p class='danger'>⚠️ <strong>Опасно!</strong> Таблица '<code>" . htmlspecialchars($table_name) . "</code>' может быть системной. Удаление запрещено.</p>";
}

// --- Проверка существования таблицы (только в базе wordpress) ---
if (empty($response)) {
    try {
        // Проверяем, существует ли таблица в базе `wordpress`
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables
                                WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute([$table_name]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            $response .= "<p class='warning'>Таблица <code>" . htmlspecialchars($table_name) . "</code> не найдена в базе <code>wordpress</code>.</p>";
        }
    } catch (PDOException $e) {
        $response .= "<p class='error'>❌ Ошибка проверки: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// --- Удаление таблицы ---
if (empty($response)) {
    try {
        // Безопасное экранирование имени таблицы (PDO не поддерживает параметры для DROP)
        $safe_table = preg_replace('/[^a-zA-Z0-9_]/', '', $table_name);
        if ($safe_table !== $table_name) {
            throw new Exception('Некорректное имя таблицы');
        }

        $pdo->exec("DROP TABLE `{$safe_table}`");

        $response .= "<p class='success'>✅ Таблица <code>{$table_name}</code> успешно удалена из базы <code>wordpress</code>.</p>";

        // Доп: покажем оставшиеся таблицы (необязательно)
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        if ($tables) {
            $response .= "<p>Оставшиеся таблицы:</p><ul>";
            foreach ($tables as $t) {
                $response .= "<li><code>" . htmlspecialchars($t) . "</code></li>";
            }
            $response .= "</ul>";
        } else {
            $response .= "<p>В базе больше нет таблиц.</p>";
        }

    } catch (PDOException $e) {
        $err = $e->getMessage();
        if (strpos($err, 'DROP command denied') !== false) {
            $response .= "<p class='error'>❌ У вас недостаточно прав для удаления таблиц.</p>";
        } elseif (strpos($err, 'doesn\'t exist') !== false) {
            $response .= "<p class='warning'>Таблица <code>" . htmlspecialchars($table_name) . "</code> уже удалена.</p>";
        } else {
            $response .= "<p class='error'>❌ Ошибка: " . htmlspecialchars($err) . "</p>";
        }
    } catch (Exception $e) {
        $response .= "<p class='error'>❌ Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo $response;
?>
