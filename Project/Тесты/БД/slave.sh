# Проверить статус репликации
sudo mysql -e "SHOW SLAVE STATUS\G"

# Проверить, что репликация работает
sudo mysql -e "SHOW PROCESSLIST;"
sudo mysql -e "SELECT Seconds_Behind_Master FROM INFORMATION_SCHEMA.REPLICA_STATUS;"
# Проверить базу данных
sudo mysql -e "SHOW DATABASES;"
sudo mysql -e "USE wordpress; SHOW TABLES;"

# Проверить чтение из БД
sudo mysql -e "USE wordpress; SELECT COUNT(*) FROM wp_posts;"

# Проверить read_only режим
sudo mysql -e "SELECT @@read_only;"
