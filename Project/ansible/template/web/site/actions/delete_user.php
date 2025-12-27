<?php
require_once '../config.php';

// Получение идентификатора пользователя из параметров
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']); // Преобразование в целое число для безопасности
} else {
    die("Неверный идентификатор пользователя.");
}

// SQL-запрос для удаления пользователя
$sql = "DELETE FROM users WHERE id = ?";

// Подготовка запроса (защита от SQL-инъекций)
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $userId); // Привязка параметра (id)

    // Выполнение запроса
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Пользователь успешно удален.";
        } else {
            echo "Пользователь с указанным ID не найден.";
        }
    } else {
        echo "Ошибка при выполнении запроса: " . $stmt->error;
    }

    // Закрытие подготовленного выражения
    $stmt->close();
} else {
    echo "Ошибка подготовки запроса: " . $conn->error;
}

// Закрытие соединения с базой данных
$conn->close();
?>
