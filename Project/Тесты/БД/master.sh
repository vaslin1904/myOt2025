# Проверить работу MySQL
sudo systemctl status mysql
sudo mysql -e "SELECT VERSION();"
SELECT User, Host FROM mysql.user;

# Проверить статус репликации
sudo mysql -e "SHOW MASTER STATUS;"
sudo mysql -e "SHOW PROCESSLIST;"
# Проверить GTID
sudo mysql -e "SELECT @@gtid_mode;"
sudo mysql -e "SELECT @@enforce_gtid_consistency;"

# Проверить пользователей
sudo mysql -e "SELECT User, Host FROM mysql.user WHERE User='repl';"
# Проверить базу данных
sudo mysql -e "SHOW DATABASES;"
sudo mysql -e "USE wordpress; SHOW TABLES;"

# Проверить таблицы WordPress
sudo mysql -e "USE wordpress; SELECT COUNT(*) FROM wp_posts;"

SHOW MASTER STATUS;
