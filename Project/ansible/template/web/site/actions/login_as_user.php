<?php
// login_as_user.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo "<p style='color:red'>Логин и пароль обязательны.</p>";
    exit;
}

// Ограничиваем подключение только к базе wordpress и только для SELECT (проверяется на уровне MySQL)
try {
    $pdoUser = new PDO(
        "mysql:host=10.10.1.51;dbname=wordpress;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Получаем список таблиц
    $tables = $pdoUser->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Таблицы в базе <code>wordpress</code> (под пользователем <code>" . htmlspecialchars($username) . "</code>):</h3>";

    if ($tables) {
        echo "<ul>";
        foreach ($tables as $t) {
            echo "<li><code>" . htmlspecialchars($t) . "</code></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Таблиц нет.</p>";
    }

} catch (PDOException $e) {
    $msg = $e->getMessage();
    if (strpos($msg, 'Access denied') !== false) {
        echo "<p style='color:red'>❌ Ошибка входа: неверный логин/пароль или недостаточно прав.</p>";
    } else {
        echo "<p style='color:orange'>⚠️ Ошибка: " . htmlspecialchars($msg) . "</p>";
    }
}
?>
