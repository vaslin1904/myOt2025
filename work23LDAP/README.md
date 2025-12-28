# Задание
1) Установить FreeIPA
2) Написать Ansible-playbook для конфигурации клиента

 LDAP (Lightweight Directory Access Protocol — легковесный протокол доступа к каталогам) —  это протокол для хранения и получения данных из каталога с иерархической структурой.
_______________________________________________________________________
# Порядок выполнения
1 Настройка ВМ перед выполнением работы: [1_customization](https://github.com/vaslin1904/myOt2025/tree/main/work23LDAP/ansible/roles/1_customization)
2 Настройка /etc/hosts или /etc/resolv.conf
vi /etc/hosts

127.0.0.1   localhost localhost.localdomain 
127.0.1.1 ipa.otus.lan ipa
192.168.57.10 ipa.otus.lan ipa
3 Установка сервера FreeIPA [3_freeipa_server](https://github.com/vaslin1904/myOt2025/tree/main/work23LDAP/ansible/roles/3_freeipa_server)
