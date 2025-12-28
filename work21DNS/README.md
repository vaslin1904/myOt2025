# Настройка DNS.Задание
- Взять стенд https://github.com/erlong15/vagrant-bind 
   * добавить еще один сервер client2
   * завести в зоне dns.lab имена:
   * web1 - смотрит на клиент1
   * web2  смотрит на клиент2
   * завести еще одну зону newdns.lab
   * завести в ней запись
   * www - смотрит на обоих клиентов
- Настроить split-dns
  * клиент1 - видит обе зоны, но в зоне dns.lab только web1
  * клиент2 видит только dns.lab
______________________________________________________________________
# Структура Playbook
![structura](https://github.com/vaslin1904/myOt2025/blob/main/work21DNS/img/Structura.png) </br>

### Порядок выполнения
1. Устанавливаем дополнительные пакеты для настройки DNS в роли dop_install: bind, bind-utils
2. Для правильной работы DNS необходимо выставить одинаковое время на всех машинах и настроить resolv.conf:
   [resolv.conf](https://github.com/vaslin1904/myOt2025/blob/main/work21DNS/ansible/template/servers-resolv.conf.j2)
3. Настройка DNS заключается в описании DNS zone, и их краткое описание в другом конфиге.
   Описание зон: [Зоны сервера](https://github.com/vaslin1904/myOt2025/tree/main/work21DNS/ansible/template/ns01)
   Проверка зоны на клиенте web1:
   ![check](https://github.com/vaslin1904/myOt2025/blob/main/work21DNS/img/client-1921685610.png)
   Проверка зоны на клиенте web2:
   ![check](https://github.com/vaslin1904/myOt2025/blob/main/work21DNS/img/client-192.168.56.11.png)
