# Проверить маршруты
ip route

# Проверить соединения
ping -c 3 10.10.1.10
ping -c 3 10.10.1.51
ping -c 3 10.10.1.52
ping -c 3 10.10.1.31
ping -c 3 10.10.1.32

# Проверить порты
nc -zv 10.10.1.10 22
nc -zv 10.10.1.51 3306
nc -zv 10.10.1.31 80
nc -zv 10.10.1.90 3000
# Проверить nftables
sudo nft list ruleset

# Проверить iptables
sudo iptables -L -n -v

# Проверить конкретные правила
sudo nft list chain inet filter input
sudo nft list chain inet filter forward
# Проверить логи MySQL
sudo tail -50 /var/log/mysql/error.log

# Проверить логи Angie
sudo tail -50 /var/log/angie/error.log
sudo tail -50 /var/log/angie/access.log

# Проверить логи Prometheus
sudo tail -50 /var/log/prometheus/prometheus.log

# Проверить логи Grafana
sudo tail -50 /var/log/grafana/grafana.log
# Проверить все сервисы
sudo systemctl list-units --type=service --state=running

# Проверить конкретные сервисы
sudo systemctl status mysql
sudo systemctl status angie
sudo systemctl status prometheus
sudo systemctl status grafana-server
sudo systemctl status alertmanager
sudo systemctl status mysqld_exporter
sudo systemctl status node_exporter
# Проверить последние бэкапы
find /var/backups/mysql/ -name "*.sql*" -mtime -1 -ls

# Проверить размер бэкапов
du -sh /var/backups/mysql/

# Проверить целостность бэкапа
gunzip -t /var/backups/mysql/wordpress_*.sql.gz
