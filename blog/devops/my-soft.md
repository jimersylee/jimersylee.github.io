<!--
author: Jimersy Lee
head: 
date: 2016-07-05
title: 我的Linux桌面环境配置与必备软件
tags: LINUX
images: 
category: devops
status: publish
summary: 每次装系统,都需要必备的软件,记录下来防止不时之需.
-->

#Idea
主题 Monokai_2
字体 YaHei Consolas Hybrid
#phpstorm
#sublime


#jdk
```
设置环境变量
vim /etc/profile

JAVA_HOME=/opt/jdk1.8.0_141
PATH=$JAVA_HOME/bin:$PATH
CLASSPATH=.:$JAVA_HOME/lib/dt.jar:$JAVA_HOME/lib/tools.jar
export JAVA_HOME
export PATH
export CLASSPATH

source  /etc/profile
```

#mysql workbench
```
apt install mysql-workbench

```


#maven
java包管理软件

#shadowsocks,必备梯子
```
sudo apt install shadowsocks-qt5
```

#tomcat

#redis-desktop-manager
redis跨平台客户端
[下载地址](http://download.csdn.net/download/shuai825644975/9854471)



#配置lamp环境
```
安装xammp
到/opt
将bin路径加入path

vim /etc/profile

export $PTAH=$PATH:/opt/lampp/bin
这样就可以直接使用pecl来安装扩展了
pecl search redis
pecl install redis

```


#nodejs npm
```
apt install  nodejs
apt install npm
设置淘宝镜像
npm config set registry https://registry.npm.taobao.org

```

#编译环境组件
```
sudo apt install build-essential
```


#Nginx
```
编译安装nginx
下载源码
tar zxvf nginx-x.tar
cd nginx
./configure \
--prefix=/usr \
--sbin-path=/usr/sbin/nginx \
--conf-path=/etc/nginx/nginx.conf \
--error-log-path=/var/log/nginx/error.log \
--pid-path=/var/run/nginx/nginx.pid \
--user=jimersylee \
--group=jimersylee \
--with-http_ssl_module \
--with-http_flv_module \
--with-http_gzip_static_module \
--http-log-path=/var/log/nginx/access.log \
--http-client-body-temp-path=/var/tmp/nginx/client \
--http-proxy-temp-path=/var/tmp/nginx/proxy \
--http-fastcgi-temp-path=/var/tmp/nginx/fcgi \
--with-http_stub_status_module
```