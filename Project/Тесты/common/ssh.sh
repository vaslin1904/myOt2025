# Проверить SSH с хоста
ssh -J vagrant@192.168.56.10 vagrant@10.10.1.10

# Проверить доступ к внутренним ВМ
ssh -J vagrant@192.168.56.10 vagrant@10.10.1.31
ssh -J vagrant@192.168.56.10 vagrant@10.10.1.51
