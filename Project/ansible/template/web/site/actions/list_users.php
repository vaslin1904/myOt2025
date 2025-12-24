<?php
require_once '../config.php';

try {
    // 1. Получаем список пользователей с доступом к БД 'wordpress'
    // Ищем:
    //   - в mysql.db (базоспецифичные привилегии)
    //   - в mysql.user (глобальные привилегии, но фильтруем по наличию хотя бы USAGE + доступ к wordpress)
    $usersStmt = $pdo->query("
        SELECT DISTINCT User, Host
        FROM (
            -- Пользователи с привилегиями на wordpress.*
            SELECT User, Host FROM mysql.db WHERE Db = 'wordpress'
            UNION
            -- Пользователи с глобальными привилегиями (и не анонимные)
            SELECT User, Host FROM mysql.user
            WHERE User != ''
              AND (Select_priv = 'Y' OR Insert_priv = 'Y' OR Create_priv = 'Y' OR Grant_priv = 'Y')
        ) AS candidates
        ORDER BY User, Host;
    ");

    $users = $usersStmt->fetchAll();

    if (empty($users)) {
        echo "<p>Нет пользователей с доступом к базе <code>wordpress</code>.</p>";
        exit;
    }

    echo "<h3>Пользователи с доступом к базе <code>wordpress</code>:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
    echo "<thead><tr>
            <th style='background:#f1f5f9'>Пользователь</th>
            <th style='background:#f1f5f9'>Привилегии на <code>wordpress</code></th>
          </tr></thead>";
    echo "<tbody>";

    foreach ($users as $u) {
        $user = $u['User'];
        $host = $u['Host'];
        $identity = htmlspecialchars("$user@$host");

        // Получаем GRANT-запросы для этого пользователя
        try {
            $grantsStmt = $pdo->prepare("SHOW GRANTS FOR ?@?");
            // Экранируем вручную (PDO не поддерживает параметры в SHOW GRANTS)
            $safeUser = str_replace('`', '``', $user);
            $safeHost = str_replace('`', '``', $host);
            $grants = $pdo->query("SHOW GRANTS FOR `$safeUser`@`$safeHost`")->fetchAll(PDO::FETCH_COLUMN);

            // Фильтруем только те GRANT-ы, что касаются 'wordpress'
            $wpGrants = [];
            foreach ($grants as $grant) {
                // Пример строки: GRANT SELECT, INSERT ON `wordpress`.* TO ...
                if (preg_match('/ON\s+`?wordpress`?\.\*|ON\s+\*\.`?wordpress`?/', $grant)) {
                    // Извлекаем только часть с привилегиями
                    if (preg_match('/GRANT\s+([A-Z,\s]+)\s+ON/', $grant, $m)) {
                        $privs = array_map('trim', explode(',', $m[1]));
                        $wpGrants = array_merge($wpGrants, $privs);
                    }
                }
                // Также ловим глобальные привилегии (ON *.*), если пользователь имеет доступ
                if (strpos($grant, 'ON *.*') !== false && strpos($grant, 'USAGE') === false) {
                    if (preg_match('/GRANT\s+([A-Z,\s]+)\s+ON/', $grant, $m)) {
                        $privs = array_map('trim', explode(',', $m[1]));
                        $wpGrants = array_merge($wpGrants, $privs);
                    }
                }
            }

            $wpGrants = array_unique(array_filter($wpGrants));
            $privList = $wpGrants ? implode(', ', $wpGrants) : '—';

        } catch (PDOException $e) {
            $privList = "<span style='color:#e74c3c'>Нет доступа / ошибка</span>";
        }

        echo "<tr>
                <td><code>$identity</code></td>
                <td>$privList</td>
              </tr>";
    }

    echo "</tbody></table>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
