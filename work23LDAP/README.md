# Задание
1) Установить FreeIPA
2) Написать Ansible-playbook для конфигурации клиента

 LDAP (Lightweight Directory Access Protocol — легковесный протокол доступа к каталогам) —  это протокол для хранения и получения данных из каталога с иерархической структурой.
_______________________________________________________________________
# Порядок выполнения
1 Настройка ВМ перед выполнением работы: [1_customization](https://github.com/vaslin1904/myOt2025/tree/main/work23LDAP/ansible/roles/1_customization) </br>
2 Настройка /etc/hosts или /etc/resolv.conf
vi /etc/hosts

127.0.0.1   localhost localhost.localdomain 
127.0.1.1 ipa.otus.lan ipa
192.168.57.10 ipa.otus.lan ipa
3 Установка сервера FreeIPA [3_freeipa_server](https://github.com/vaslin1904/myOt2025/tree/main/work23LDAP/ansible/roles/3_freeipa_server) </br>
Создается конфиг с настройками сервера:
- name: Run IPA server install
  ansible.builtin.command: >
    ipa-server-install
    --unattended
    --domain=otus.lan
    --realm=OTUS.LAN
    --hostname=ipa.otus.lan
    --admin-password=Secret123
    --ds-password=Secret123
  args:
    creates: /etc/ipa/default.conf
4 Установка клиента FreeIPA [[4_freeipa_client](https://github.com/vaslin1904/myOt2025/tree/main/work23LDAP/ansible/roles/4_freeipa_client) </br>
Создается конфиг с настройками клиента:

- name: Run IPA client install
  ansible.builtin.command: >
    ipa-client-install
    --unattended
    --mkhomedir
    --domain=OTUS.LAN
    --server=ipa.otus.lan
    --no-ntp
    --force-join
    -p admin
    -w Secret123
  ______________________________________________________________________
## Проверка работы
#### Добавление пользователя otus-user
![add_user](https://github.com/vaslin1904/myOt2025/blob/main/work23LDAP/img/cl1_add_us_otus.png)
#### 
![ipa web](https://github.com/vaslin1904/myOt2025/blob/main/work23LDAP/img/ipa_web.png)
###
![ipa user](https://github.com/vaslin1904/myOt2025/blob/main/work23LDAP/img/ipaweb_users.png)
###
![klist](https://github.com/vaslin1904/myOt2025/blob/main/work23LDAP/img/klist_ipa.png)
  args:
    creates: /etc/ipa/default.conf
