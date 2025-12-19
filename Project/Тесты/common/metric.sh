# Проверить метрики MySQL
curl "http://localhost:9090/api/v1/query?query=mysql_up"

# Проверить метрики системы
curl "http://localhost:9090/api/v1/query?query=node_cpu_seconds_total"

# Проверить метрики WordPress
curl "http://localhost:9090/api/v1/query?query=wordpress_up"

# Проверить доступ к дашбордам
curl -u admin:StrongGrafanaPass123! http://localhost:3000/api/dashboards/db

# Проверить конкретный дашборд
curl -u admin:StrongGrafanaPass123! http://localhost:3000/api/dashboards/uid/mysql-overview
