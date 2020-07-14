<!--
author: Jimersy Lee
head: 
date: 2020-06-04
title: linux环境安装打印机驱动
tags: LINUX,HARBOR,DEVOPS,MANJARO
images:
category: devops
status: publish
summary: 为了彻底摆脱windows,这次把linux打印机驱动搞定了
-->

# linux-printer linux环境安装打印机驱动


# 软件及系统
* OS:manjaro 20.0.3 Kernel: x86_64 Linux 5.4.44-1-MANJARO
*  Manage printing(cups) 2.3.3-1
*  打印机:震旦AD289s 
*  驱动程序:[Konica Minolta bizhub 287 Driver](https://www.freeprinterdriverdownload.org/konica-minolta-bizhub-287-driver-download-links/)  [linux64驱动](https://www.freeprinterdriverdownload.org/download/konica-minolta-bizhub-287/IT5BWPPDLinux_900010006MU.zip)


# 操作步骤
 
 启动manage printing,打开浏览器 localhost:631
![首页](https://raw.githubusercontent.com/jimersylee/imagehosting/master/1594768169_20200629112920963_2008341084.png)
点击 Add printer
![选择协议](https://raw.githubusercontent.com/jimersylee/imagehosting/master/1594768171_20200629113044826_1770484846.png)

打印机地址为
![设置地址](https://raw.githubusercontent.com/jimersylee/imagehosting/master/1594768174_20200629113157596_1308672203.png)

下一步随便填些信息
![选择驱动](https://raw.githubusercontent.com/jimersylee/imagehosting/master/1594768176_20200629113600526_83762745.png)

点击choose file按钮,然后找到下载下来的驱动程序
![选择驱动](https://raw.githubusercontent.com/jimersylee/imagehosting/master/1594768177_20200629113705881_2130477949.png)

选择A4呗

![设置默认选项](https://raw.githubusercontent.com/jimersylee/imagehosting/master/1594768179_20200629113845808_831801316.png)


接下来就能在wps等软件中找到打印机了