# Проверить работу Grafana
sudo systemctl status grafana-server

# Проверить доступ к UI
curl http://localhost:3000

# Проверить дашборды
curl http://localhost:3000/api/dashboards/db
