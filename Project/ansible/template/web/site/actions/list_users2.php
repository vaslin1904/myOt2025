<?php
require_once '../config.php';

try {
    // Список пользователей и их привилегий
    $stmt = $pdo->query(" User, Host FROM mysql.user;");
      //  SELECT
      //      CONCAT(user, '@', host) AS identity,
    //        GROUP_CONCAT(DISTINCT privilege SEPARATOR ', ') AS privileges
    //    FROM (
    //        SELECT user, host, 'SELECT' AS privilege FROM mysql.user WHERE Select_priv = 'Y'
    //        UNION SELECT user, host, 'INSERT' FROM mysql.user WHERE Insert_priv = 'Y'
    //        UNION SELECT user, host, 'UPDATE' FROM mysql.user WHERE Update_priv = 'Y'
    //        UNION SELECT user, host, 'DELETE' FROM mysql.user WHERE Delete_priv = 'Y'
    //        UNION SELECT user, host, 'CREATE' FROM mysql.user WHERE Create_priv = 'Y'
    //        UNION SELECT user, host, 'DROP' FROM mysql.user WHERE Drop_priv = 'Y'
    //        -- Можно добавить другие привилегии при необходимости
  //      ) AS privs
    //    GROUP BY user, host
    //    ORDER BY user, host;


    $users = $stmt->fetchAll();
    echo "<h3>Список пользователей и привилегий:</h3>";
    if ($users) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Пользователь</th><th>Привилегии</th></tr>";
        foreach ($users as $u) {
            echo "<tr><td><code>" . htmlspecialchars($u['identity']) . "</code></td>
                      <td>" . htmlspecialchars($u['privileges']) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Пользователей не найдено.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red'>Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
