# Изучение IPTABLES
--------------------------------
## Схема задания
<img width="619" height="477" alt="image" src="https://github.com/user-attachments/assets/3c221d8f-7331-42e5-9ead-0f6a71075233" /> </br>
## Задание 1. Реализовать knocking port
### centralRouter может попасть на ssh inetrRouter через knock скрипт
Клиент должен "пройти этапы" через нестандартные порты (8881 → 7777 → 9991 → 22), чтобы получить доступ к порту 22 </br>
В таблицу InetRouter добавляются правила проверки прохождения соединений через ssh от centralRouter </br>
*\# Создание цепочки TRAFFIC в таблице INPUT для фильтрации входящего трафика* </br>
*\# Весь трафик от 192.168.255.2 (centralRouter) направляется в цепочку TRAFFIC* </br>
-A INPUT -s 192.168.255.2 -j TRAFFIC* </br>
*\# === Цепочка TRAFFIC ===* </br>
*\# Разрешить любой ICMP-трафик (ping, ошибки сети и т.д.)* </br>
-A TRAFFIC -p icmp --icmp-type any -j ACCEPT </br>
*\# Разрешить установленные и связанные соединения* </br>
-A TRAFFIC -m state --state ESTABLISHED,RELATED -j ACCEPT </br>
*\# Защита SSH (порт 22)* </br>
*\# Если новое TCP-соединение на порт 22, и IP есть в списке SSH2 (последние 120 сек),* </br>
*\# разрешить подключение (разрешить только при повторной попытке в рамках "сессии")* </br>
-A TRAFFIC -m state --state NEW -m tcp -p tcp --dport 22 -m recent --rcheck --seconds 120 --name SSH2 -j ACCEPT </br>
*\# Если новое подключение на порт 22, но IP НЕ прошел проверку выше — удалить из списка SSH2 и отклонить* </br>
\-A TRAFFIC -m state --state NEW -m tcp -p tcp -m recent --name SSH2 --remove -j DROP </br>
*\# Логика для порта 9991 (этап 1 аутентификации)* </br>
*\# Для новых подключений на порт 9991: проверить наличие в списке SSH1* </br>
-A TRAFFIC -m state --state NEW -m tcp -p tcp --dport 9991 -m recent --rcheck --name SSH1 -j SSH-INPUTTWO </br>
*\# Если проверка не пройдена — удалить из списка SSH1 и отклонить* </br>
-A TRAFFIC -m state --state NEW -m tcp -p tcp -m recent --name SSH1 --remove -j DROP </br>
*\# Логика для порта 7777 (этап 0 аутентификации)* </br>
*\# Для новых подключений на порт 7777: проверить наличие в списке SSH0* </br>
-A TRAFFIC -m state --state NEW -m tcp -p tcp --dport 7777 -m recent --rcheck --name SSH0 -j SSH-INPUT </br>
*\# Если проверка не пройдена — удалить из списка SSH0 и отклонить* </br>
-A TRAFFIC -m state --state NEW -m tcp -p tcp -m recent --name SSH0 --remove -j DROP </br>
*\#Инициализация "сессии" для порта 8881* </br>
*\# Новое подключение на порт 8881 помечает IP в списке SSH0 и отклоняется (триггер для последующих этапов)* </br>
-A TRAFFIC -m state --state NEW -m tcp -p tcp --dport 8881 -m recent --name SSH0 --set -j DROP </br>
*\# Вспомогательные цепочки* </br>
*\# пометить IP в списке SSH1 и отклонить (следующий этап после порта 7777)* </br>
-A SSH-INPUT -m recent --name SSH1 --set -j DROP </br>
*\# пометить IP в списке SSH2 и отклонить (финальный этап после порта 9991)* </br>
-A SSH-INPUTTWO -m recent --name SSH2 --set -j DROP </br>
*\#Запрет всего остального*</br>
-A TRAFFIC -j DROP </br>
На centralRouter размещается knock script, который последовательно пытается подключиться к inetRouter через последовательность портов. </br>
_____________________________________________________________________________________________________________________________________
## Задание 2. Подключиться к порту 80 nginx (centralServer) на хосте через порт 8080 inetRouter2. Хост виден на inetRouter2.
1. Настройка NAT centralServer: </br>
*\# Пакеты, пришедшие с порта 8080 направлять на порт 80*
-A PREROUTING -p tcp --dport 8080 -j REDIRECT --to-port 80
2. Настройка NAT inetRouter2: </br>
*\# При обращении на порт 8080 происходит перенаправление на порт 80 central Server* </br>
-A PREROUTING -p tcp --dport 8080 -j DNAT --to-destination 192.168.2.3:80 </br>
*\# Если пакет пришел от cenralRouter с порта 80 то он направляется на порт 8080 inetRouter2* </br>
-A POSTROUTING -d 192.168.2.3 -p tcp -m tcp --dport 80 -j SNAT --to-source 192.168.2.2:8080 </br>
*\# Исходящие пакеты с порта 8080 inetRouter2 направляются на порт 80 centralRouter* </br>
-A OUTPUT -p tcp -d 192.168.2.2 --dport 8080 -j DNAT --to-destination 192.168.2.3:80 <\br>
3. Настройка FILTER inetRouter2: </br>
*\# Настройка (разрешение) перенаправления пакетов через inetRouter* <\br>
-A FORWARD -d 192.168.2.3 -p tcp -m multiport --dports 80,8080 -j ACCEPT <\br>
-A FORWARD -s 192.168.2.3 -p tcp --sport 80 -j ACCEPT <\br>
