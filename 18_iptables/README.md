# Изучение IPTABLES
--------------------------------
## Схема задания
<img width="619" height="477" alt="image" src="https://github.com/user-attachments/assets/3c221d8f-7331-42e5-9ead-0f6a71075233" /> </br>
## Задание 1. Реализовать knocking port
### centralRouter может попасть на ssh inetrRouter через knock скрипт
Клиент должен "пройти этапы" через нестандартные порты (8881 → 7777 → 9991 → 22), чтобы получить доступ к порту 22
В таблицу InetRouter добавляются правила проверки прохождения соединений через ssh от centralRouter
\# Создание цепочки TRAFFIC в таблице INPUT для фильтрации входящего трафика
\# Весь трафик от 192.168.255.2 (centralRouter) направляется в цепочку TRAFFIC
-A INPUT -s 192.168.255.2 -j TRAFFIC
\# === Цепочка TRAFFIC ===
\# Разрешить любой ICMP-трафик (ping, ошибки сети и т.д.)
-A TRAFFIC -p icmp --icmp-type any -j ACCEPT
\# Разрешить установленные и связанные соединения
-A TRAFFIC -m state --state ESTABLISHED,RELATED -j ACCEPT
\# === Защита SSH (порт 22) ===
\# Если новое TCP-соединение на порт 22, и IP есть в списке SSH2 (последние 120 сек),
\# разрешить подключение (разрешить только при повторной попытке в рамках "сессии")
-A TRAFFIC -m state --state NEW -m tcp -p tcp --dport 22 -m recent --rcheck --seconds 120 --name SSH2 -j ACCEPT
\# Если новое подключение на порт 22, но IP НЕ прошел проверку выше — удалить из списка SSH2 и отклонить
\-A TRAFFIC -m state --state NEW -m tcp -p tcp -m recent --name SSH2 --remove -j DROP
\# === Логика для порта 9991 (этап 1 аутентификации) ===
\# Для новых подключений на порт 9991: проверить наличие в списке SSH1
-A TRAFFIC -m state --state NEW -m tcp -p tcp --dport 9991 -m recent --rcheck --name SSH1 -j SSH-INPUTTWO
\# Если проверка не пройдена — удалить из списка SSH1 и отклонить
-A TRAFFIC -m state --state NEW -m tcp -p tcp -m recent --name SSH1 --remove -j DROP
\# === Логика для порта 7777 (этап 0 аутентификации) ===
\# Для новых подключений на порт 7777: проверить наличие в списке SSH0
-A TRAFFIC -m state --state NEW -m tcp -p tcp --dport 7777 -m recent --rcheck --name SSH0 -j SSH-INPUT
\# Если проверка не пройдена — удалить из списка SSH0 и отклонить
-A TRAFFIC -m state --state NEW -m tcp -p tcp -m recent --name SSH0 --remove -j DROP
\# === Инициализация "сессии" для порта 8881 ===
\# Новое подключение на порт 8881 помечает IP в списке SSH0 и отклоняется (триггер для последующих этапов)
-A TRAFFIC -m state --state NEW -m tcp -p tcp --dport 8881 -m recent --name SSH0 --set -j DROP
\# === Вспомогательные цепочки ===
\# SSH-INPUT: пометить IP в списке SSH1 и отклонить (следующий этап после порта 7777)
-A SSH-INPUT -m recent --name SSH1 --set -j DROP
\# SSH-INPUTTWO: пометить IP в списке SSH2 и отклонить (финальный этап после порта 9991)
-A SSH-INPUTTWO -m recent --name SSH2 --set -j DROP
\# === Запрет всего остального ===
-A TRAFFIC -j DROP
На centralRouter размещается knock script, который последовательно пытается подключиться к inetRouter через последовательность портов.
_____________________________________________________________________________________________________________________________________
## Задание 2. Подключиться к nginx (centralServer) на хосте через порт 8080. Хост виден inetRouter2.
