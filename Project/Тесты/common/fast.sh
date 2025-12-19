# Проверить все серверы через Ansible
ansible all -m ping

# Проверить статус сервисов
ansible all -m shell -a "systemctl is-active mysql" --become

# Проверить доступ к БД
ansible db_master -m shell -a "mysql -e 'SELECT 1;'" --become

# Проверить доступ к веб-сайту
ansible web1 -m shell -a "curl -s http://localhost | head -5" --become
# Проверить репликацию
ansible db_slave -m shell -a "mysql -e 'SHOW SLAVE STATUS\G'" --become

# Проверить GTID
ansible db_master -m shell -a "mysql -e 'SELECT @@gtid_mode;'" --become
# Проверить Prometheus targets
ansible monitor -m shell -a "curl http://localhost:9090/api/v1/targets" --become

# Проверить Grafana
ansible monitor -m shell -a "curl http://localhost:3000" --become
