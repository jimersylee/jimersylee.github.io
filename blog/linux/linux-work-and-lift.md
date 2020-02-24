<!--
author: Jimersy Lee
head: 
date: 2020-02-24
title: 如何在linux下工作与生活
tags: LINUX
images: 
category: linux
status: publish
summary: 我是如何在linux下工作和生活的,查看本文即可揭晓
-->

# 如何在linux下工作与生活

## 修改历史

- v1.0.0 第一次提交

## 目录

0. 发行版选择

1. 工作
2. 生活娱乐
3. 网上冲浪
4. 系统管理
5. 图形图像
6. 不得不用的windows

0.发行版的选择

在试用了ubuntu,centos,deepin,forada,elementOS,manjaro等众多发行版后,我选择了manjaro,manjaro基于ArchLinux,相比ArchLinux的从头开始配置一个操作系统,manjaro提供了开箱即用的易于使用的体验.

此处logo图片

0.1 发行版的安装

我选择的是U盘安装的方式,贴上我目前的设备配置,笔记本:小米笔记本Pro,i7,16G内存,256GB固态,下面为具体的配置

 jimersylee@jimersy-laptop
 OS: Manjaro 19.0 Kyria
 Kernel: x86_64 Linux 4.19.102-1-MANJARO
  Uptime: 7d 14h 28m
  Packages: 1491
  Shell: zsh 5.7.1
  Resolution: 1920x1080
  DE: GNOME 3.34.4
  WM: Mutter
  WM Theme: Mojave-light
  GTK Theme: Mojave-light [GTK2/3]
  Icon Theme: Numix-Light
  Font: JetBrains Mono 11
  Disk: 169G / 579G (31%)
  CPU: Intel Core i7-8550U @ 8x 4GHz [44.0°C]
  GPU: GeForce MX150
  RAM: 4985MiB / 15924MiB

  U盘安装使用的rufus(https://rufus.ie/)刻录镜像,BIOS设置U盘启动,然后启动安装,一路默认设置就可以

安装之后就是设置,因为我加了一块固态,所以要开机设置自动挂载硬盘

todo:挂载过程

虽然为16GB内存,但是多开几个IDE,多开几个浏览器标签,还是存在内存吃紧的情况,因此我设置了8G的交换分区

todo:交换分区设置

分区设置,系统安装在根目录,将另一块硬盘挂载在/home/用户名 下,这样,就算重装系统,个人的配置文件不会丢失,挂载上即可用



0.2 发行版的升级

manjaro使用的是pacman进行包管理,除此之外,还可以安装yay进行软件管理,有时pacman找不到的软件,使用yay可以找到

内核可以使用可视化软件,manjaro settings manager进行升级,我目前使用的4.19.102-1,之前我升级到5.4.18,但是发现存在开机,关机慢的问题,因此我又回滚到了4.19,manjaro好就好在可以自由切换内核,方便作死,

0.3 安装驱动

驱动问题没有怎么遇到,基本安装后就各项设备运行正常,除了指纹识别模块不灵敏,其他的硬件都工作良好.

目前我使用核心显卡,没有使用独显,个人觉得没有必要使用,原因如下:笔记本的显卡性能鸡肋,也跑不动什么深度学习,我也不会使用笔记本玩什么游戏大作,且禁用独显后,发热量少,静音,且续航提高,让我比较满意,因此,我使用集显很开心.

0.4 桌面环境

个人还是喜欢比较现代的桌面环境的,在体验了kde,wm,gnome等,最后选择了gnome,虽然gnome比较耗内存,但是现在内存白菜价,也不那么吃紧,何必安装xfc等桌面呢?比如树莓派我就不会选择gnome,哈哈.


