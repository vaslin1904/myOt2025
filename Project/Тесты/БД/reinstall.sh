# Остановить MySQL
sudo systemctl stop mysql

# Удалить пакеты
sudo apt remove --purge percona-server-server percona-server-client

# Удалить данные
sudo rm -rf /var/lib/mysql
sudo rm -rf /var/log/mysql

# Установить заново
sudo percona-release setup ps84
sudo apt install percona-server-server

# Инициализировать базу данных
sudo systemctl start mysql
