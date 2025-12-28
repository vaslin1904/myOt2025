## Задание

Базу развернуть на мастере и настроить так, чтобы реплицировались таблицы:


| bookmaker          |
| competition        |
 market              |
| odds               |
| outcome
___________________________________________________________
GTID репликация неработает на стенде из-за версий пакетов.
![gtid](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/1_nogtid.png)
____________________________________________________________
В Playbook Ansible выполняются не все задачи.
с помощью ансибл настроено:
- установка Базы данных на db-master [1_install_mySql.yml](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/ansible/roles/db/tasks/1_install_mySql.yml)
- размещение пароля root для базы [/root/.my.cnf](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/ansible/template/db/.my.cnf) </br>
  копирование конфига базы [/etc/mysql/conf.d/mysql.cnf](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/ansible/template/db/mysqld.cnf.j2)</br>
  и дампа тестовой базы bet.dmp на db-master.
- На db-master создание базы bet и настройка mysql [3_set_dbMySql.yml](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/ansible/roles/db/tasks/3_set_dbMySql.yml)
- Копирование созданного вручную dump базы bet master.sql на bd-slave [4_add_master.sql.yml](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/ansible/roles/db/tasks/4_add_master.sql.yml)
- Создание пользователя repl на db-master для репликации [5_db_master.yml](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/ansible/roles/db/tasks/5_db_master.yml)
  ![3](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/3%20us%20repl.png)
- Запуск репликации [8_db_slave.yml](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/ansible/roles/db/tasks/8_db_slave.yml)
- ___________________________________________________________________________________________________________
  ## Вручном режиме на db-master выполнены следующие шаги
  - в режиме mysql: **SELECT @@server_id;**
    ![1](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/1_nogtid.png)
  - Загрузка под root дампа тестовой базы bet: **mysql -uroot -p -D bet < /vagrant/bet.dmp**
    В режиме Mysql Убедились в загрузке дампа для базы bet: **mysql> USE bet; mysql> SHOW TABLES;**
    ![2](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/2dmp%20bet.png)
- Дампим базу для последующго залива на слейв и игнорируем таблицу по заданию под root:
**mysqldump --all-databases --triggers --routines --master-data\****
**--ignore-table=bet.events_on_demand --ignore-table=bet.v_same_event -uroot -p > master.sql**
  ![4](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/4_dump%20for%20slave.png)
- На db-slave смотрим id
  ![5](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/5_slave%20id.png)
- Меняем статус базы db-slave read_only на write
  ![6](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/6%20mysqld%20slave.png)
- На db-slave в режиме mysql Заливаем дамп мастера и убеждаемся что база есть и она без лишних таблиц:
**mysql> SOURCE /mnt/master.sql**
**mysql> SHOW DATABASES LIKE 'bet';**
  ![7](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/7%20slave%20bet.png)
  ![9](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/9%20tables%20on%20slave.png)
- Для репликации узнаем лог и позицию, так как автоматическая репликация не работает
  ![8](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/8%20master%20status.png)
  ____________________________
  ## Репликация выполняется
  ![10](https://github.com/vaslin1904/myOt2025/blob/main/work25MySql/img/10%20slave%20on.png)
  
