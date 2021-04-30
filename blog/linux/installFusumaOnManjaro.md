<!--
author: Jimersy Lee
head: 
date: 2018-09-12
title: Manjaro安装fusuma
tags: MANJARO，fusuma
images: 
category: linux
status: publish
summary: 让gnome桌面环境的笔记本支持多指触控手势
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


# 将用户加入输入组
```shell
sudo gpasswd -a $USER input
sudo reboot
```

# 安装ruby环境，安装fusuma包

```shell
#安装ruby
sudo pacman -S ruby
sudo gem install fusuma
#
```

# 配置

```shell
#将ruby程序目录加入路径
echo "export PATH=$PATH:/home/jimersylee/.gem/ruby/2.5.0/bin" >> ~/.profile
source ~/.profile
#人工启动
fusuma -d
#加入开机启动
/usr/share/applications 新建一个.desktop快捷方式，配置好
使用tweaks 添加 startup application 
```


