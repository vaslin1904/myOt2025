<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo "<p style='color:red'>Ошибка: логин и пароль обязательны.</p>";
    exit;
}

// Экранирование имени (только буквы/цифры/подчёркивания, минимум длина)
if (!preg_match('/^[a-zA-Z0-9_]{3,16}$/', $username)) {
    echo "<p style='color:red'>Недопустимый формат имени пользователя.</p>";
    exit;
}

try {
    // Создание пользователя
    $stmt = $pdo->prepare("CREATE USER ?@'%' IDENTIFIED BY ?");
    // PDO не экранирует идентификаторы (user@host), поэтому используем вручную защищённую подстановку
    $sql = "CREATE USER `" . str_replace('`', '``', $username) . "`@'%' IDENTIFIED BY " . $pdo->quote($password);
    $pdo->exec($sql);

    // Выдача минимальных прав: SELECT, SHOW VIEW (можно расширить)
    $grantSql = "GRANT SELECT, SHOW VIEW ON `wordpress`.* TO `" . str_replace('`', '``', $username) . "`@'%'";
    $pdo->exec($grantSql);

    $pdo->exec("FLUSH PRIVILEGES");

    echo "<p style='color:green'>✅ Пользователь <code>$username</code> создан и получает SELECT на базу <code>wordpress</code>.</p>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
