packer {
  required_plugins {
    yandex = {
      version = ">= 1.1.3"
      source  = "github.com/hashicorp/yandex"
    }
  }
}
source "virtualbox-vm" "dns" {
  // Имя ВМ в VirtualBox Manager — должно существовать и быть выключено
  vm_name = "Project_dnsSrv_1765652521339_73473"

  // Не запускать headless — можно видеть процесс (для отладки)
  headless = false

  // Время ожидания запуска (увеличьте, если ВМ медленно стартует)
  ssh_timeout = "5m"

  // SSH-доступ для выполнения скриптов
  ssh_username = "vagrant"
  ssh_password = ""  # отключаем пароль
  ssh_private_key_file = ".vagrant/machines/dnsSrv/virtualbox/private_key"
  ssh_agent_auth = false
 

  // После сборки — выключить ВМ
  shutdown_command = "echo 'vagrant' | sudo -S shutdown -P now"
  shutdown_timeout = "5m"

  // Куда сохранить .box
//  output_directory = "/home/us/myOt2025/Project/box-tmp"
}

build {
  name = "dns-box"

  sources = ["source.virtualbox-vm.dns"]

  // Упаковка в .box
  post-processor "vagrant" {
    compression_level = 9
    output = "dns.box"
  }
}
