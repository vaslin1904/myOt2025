# Проверить работу Nginx
sudo systemctl status apache2
sudo systemctl status nginx
sudo systemctl status php8.1-fpm

# Проверить порты
sudo ss -tuln | grep :80
sudo ss -tuln | grep :443
sudo ss -tuln | grep :9000
#Проверка WordPress
# Проверить доступ к сайту
curl http://localhost
curl http://localhost/wp-admin/

# Проверить файлы WordPress
ls -la /var/www/wordpress/
ls -la /var/www/wordpress/wp-config.php

# Проверить права
sudo ls -la /var/www/wordpress/wp-content/
# Проверить подключение к MySQL
mysql -h 10.10.1.51 -u wpuser -p'StrongWPpass123!' -e "SELECT 1;"

# Проверить таблицы WordPress
mysql -h 10.10.1.51 -u wpuser -p'StrongWPpass123!' -e "USE wordpress; SHOW TABLES;"
