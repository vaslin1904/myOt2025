# Проверить работу bind9
sudo systemctl status bind9
sudo systemctl status named

# Проверить DNS-запросы
dig @127.0.0.1 linuxpro.my
dig @127.0.0.1 web1.dmz.linuxpro.my
dig @127.0.0.1 db-master.dmz.linuxpro.my

# Проверить DNS-зоны
sudo named-checkzone linuxpro.my /etc/bind/zones/db.linuxpro.my
sudo named-checkconf
