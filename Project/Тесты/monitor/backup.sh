# Проверить директорию бэкапов
ls -la /var/backups/mysql/

# Проверить последний бэкап
ls -lt /var/backups/mysql/ | head -5

# Проверить содержимое бэкапа
zcat /var/backups/mysql/wordpress_*.sql.gz | head -10
# Проверить скрипт
cat /usr/local/bin/backup-db.sh

# Запустить вручную
/usr/local/bin/backup-db.sh
# Проверить cron задачи
crontab -l

# Проверить логи cron
grep CRON /var/log/syslog | tail -10
