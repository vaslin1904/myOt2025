<?php
// Удаление строки
require_once __DIR__ . '/../wp-config.php';

if (!isset($_GET['table']) || !isset($_GET['id'])) {
    die("Не указаны параметры");
}

$table = $_GET['table'];
$id = $_GET['id'];

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$sql = "DELETE FROM `$table` WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?table=" . urlencode($table));
} else {
    echo "Ошибка удаления: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?>
