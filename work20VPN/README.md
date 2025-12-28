#### Описание домашнего задания
Настроить VPN между двумя ВМ в tun/tap режимах, замерить скорость в туннелях, сделать вывод об отличающихся показателях
Поднять RAS на базе OpenVPN с клиентскими сертификатами, подключиться с локальной машины на ВМ
---------------------------------------------------------------------------------
Playbook ansible имеет следующую структуру
![Структура работы](img/structura.png)
Для выполнения работы с помощью Vagrant поднимаются две виртуальные машины на базе "ubuntu/jammy64":
- :server => {:net => ["192.168.56.3", 2, "255.255.255.0"],},
- :client=> {:net =>["192.168.56.4",  2, "255.255.255.0"],},
Тунели tun/tup создаются на базе пакета openvpn с помощью конфигурационных файлов:
-![server.conf](https://github.com/vaslin1904/myOt2025/blob/main/work20VPN/ansible/template/server.conf.j2)
-![client.conf](https://github.com/vaslin1904/myOt2025/blob/main/work20VPN/ansible/template/client.conf.j2)
Запуск тонеля происходит с помощью созданного systemctl service:
  [openvpn@.service](https://github.com/vaslin1904/myOt2025/blob/main/work20VPN/ansible/template/openvpn%40.service)
