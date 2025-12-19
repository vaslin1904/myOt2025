# Проверить mysqld_exporter
curl http://localhost:9104/metrics | head -20

# Проверить node_exporter
curl http://localhost:9100/metrics | head -20
