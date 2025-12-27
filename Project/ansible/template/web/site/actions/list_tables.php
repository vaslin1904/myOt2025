<?php
require_once '../config.php';

// Вывод заголовка страницы
echo "<h1>Список таблиц в базе данных '{$database}'</h1>";

// Вывод списка таблиц
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_row()) {
        echo "<li>{$row[0]}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Таблицы в базе данных отсутствуют.</p>";
}

// Закрытие соединения
$conn->close();
?>
