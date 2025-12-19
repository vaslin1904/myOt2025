# Проверить работу Prometheus
sudo systemctl status prometheus

# Проверить доступ к UI
curl http://localhost:9090

# Проверить targets
curl http://localhost:9090/api/v1/targets
