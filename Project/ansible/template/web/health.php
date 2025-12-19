<?php
// healthz.php — простой health-check для Angie
header('Content-Type: text/plain');
$mysqli = @new mysqli('10.10.1.51', 'wpuser', 'StrongWPpass123!', 'wordpress');

if ($mysqli->connect_error) {
    http_response_code(503);
    echo "DB: DOWN\n";
} else {
    $result = $mysqli->query("SELECT 1");
    if ($result && $result->fetch_row()) {
        echo "OK\n";
    } else {
        http_response_code(503);
        echo "DB: QUERY FAIL\n";
    }
    $mysqli->close();
}
