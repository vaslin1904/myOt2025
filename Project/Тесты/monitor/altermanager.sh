# Проверить работу Alertmanager
sudo systemctl status alertmanager

# Проверить доступ к UI
curl http://localhost:9093

# Проверить правила
curl http://localhost:9093/api/v1/rules
