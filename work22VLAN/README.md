# Задание
в Office1 в тестовой подсети появляется сервера с доп интерфейсами и адресами
в internal сети testLAN: 
- testClient1 - 10.10.10.254
- testClient2 - 10.10.10.254
- testServer1- 10.10.10.1 
- testServer2- 10.10.10.1
Равести вланами:
testClient1 <-> testServer1
testClient2 <-> testServer2
Между centralRouter и inetRouter "пробросить" 2 линка (общая inernal сеть)  </br>
и объединить их в бонд, проверить работу c отключением интерфейсов
__________________________________________________________________________
![схема](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/img/shema.png)
1 Устанавливаем необходимые утилиты ( [/roles/1_dop_install](https://github.com/vaslin1904/myOt2025/tree/main/work22VLAN/ansible/roles/1_dop_install)
2 На хосте testClient1, testServer1 создаем файл [/etc/sysconfig/network-scripts/ifcfg-vlan1](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/ansible/template/ifcfg-vlan1.j2)
![ping Client1-Server1](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/img/ping%20testClient1%20to%20testServer1.png)
3 На хосте testClient2, testServer2 создаем файл [50-cloud-init.yaml](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/ansible/template/50-cloud-init.yaml.j2)
![ping Client2-Server2](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/img/ping%20testClient2%20to%20testServer2.png)
________________________________________________________________________________
### Настройка LACP между хостами inetRouter и centralRouter
Bond интерфейс будет работать через порты eth1 и eth2. 
1 Настраиваем интерфейсы на inetRouter и centralRouter eth1 [/etc/sysconfig/network-scripts/ifcfg-eth1](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/ansible/template/ifcfg-eth1)
и eth2 [/etc/sysconfig/network-scripts/ifcfg-eth2](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/ansible/template/ifcfg-eth2)
2 После настройки интерфейсов eth1 и eth2 настраиваем bond-интерфейс [/etc/sysconfig/network-scripts/ifcfg-bond0](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/ansible/template/ifcfg-bond0.j2)
#### Проверяем работу интерфейса:
![check](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/img/ping%20inetRouter%20to%20centralRouter.png)
Не отменяя ping подключаемся к хосту centralRouter и выключаем там интерфейс eth1: 
**[root@centralRouter ~]# ip link set down eth1**
После данного действия ping не должен пропасть, так как трафик пойдёт по-другому порту.
![down eth1](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/img/trafic%20eth1(down)_2%20centralRouter.png)
![down eth2](https://github.com/vaslin1904/myOt2025/blob/main/work22VLAN/img/trafic%20eth1_2%20centralRouter.png)
