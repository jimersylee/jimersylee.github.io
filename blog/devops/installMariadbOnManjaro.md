<!--
author: Jimersy Lee
head: 
date: 2018-09-09
title: Manjaro安装Mariadb
tags: MANJARO,MARIADB
images: 
category: devops
status: publish
summary: 在manjaro系统上安装mysql与其他系统稍有不同
-->


环境:Manjaro
```
 ██████████████████  ████████     jimersylee@jimersylee-laptop
 ██████████████████  ████████     OS: Manjaro 17.1.12 Hakoila
 ██████████████████  ████████     Kernel: x86_64 Linux 4.14.67-1-MANJARO
 ██████████████████  ████████     Uptime: 3h 12m
 ████████            ████████     Packages: 1184
 ████████  ████████  ████████     Shell: zsh 5.5.1
 ████████  ████████  ████████     Resolution: 1920x1080
 ████████  ████████  ████████     DE: GNOME 
 ████████  ████████  ████████     WM: GNOME Shell
 ████████  ████████  ████████     WM Theme: Adapta-Nokto-Maia
 ████████  ████████  ████████     GTK Theme: Adapta-Nokto-Maia [GTK2/3]
 ████████  ████████  ████████     Icon Theme: Papirus-Adapta-Maia
 ████████  ████████  ████████     Font: Noto Sans 11
 ████████  ████████  ████████     CPU: Intel Core i7-8550U @ 8x 4GHz [47.0°C]
                                  GPU: Mesa DRI Intel(R) UHD Graphics 620 (Kabylake GT2) 
                                  RAM: 6157MiB / 15928MiB

```

# 安装

```shell
sudo pacman -S mariadb
```

# 配置

```shell
#初始化
sudo mysql_install_db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
#启动
sudo systemctl start mariadb
#设置密码
mysql_secure_installation
```


