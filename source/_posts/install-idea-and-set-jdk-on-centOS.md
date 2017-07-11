---
title: install idea and set jdk on centOS
date: 2017-06-29 10:09:17
tags: 
- idea 
- centos
---

# 卸载CentOS中自带openjdk

CentOS自带openjdk，可以先用Java –version检测是否存在jdk版本。如果存在，最好在安装Oracle的jdk之前最好卸载，可以使用如下指令

```
yum -y remove java java-1.4.2-gcj-compat-1.4.2.0-40jpp.115
yum -y remove java java-1.6.0-openjdk-1.6.0.0-1.7.b09.el5
```
# 安装JDK

先从oracle官网下载对应的JDK版本 http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html 
本人选择的是Linux x86 .tar.gz版本 
下载完成后在/usr 中创建java目录，用来存放跟java有关的文件。然后把下载jdk压缩包的文件解压到该路径下

```
tar –xzvf jdk jdk-8u45-linux-i586.tar.gz
```

顺便重命名一下，方便以后写路径

```
mv jdk1.8.0_45 jdk1.8
```

# 配置环境变量

```
vim /etc/profile
```
然后添加环境变量

```
export JAVA_HOME=/usr/java/jdk1.8 
export CLASSPATH=.:$JAVA_HOME/jre/lib/rt.jar:$JAVA_HOME/lib/dt.jar:$JAVA_HOME/lib/tools.jar 
export PATH=$PATH:$JAVA_HOME/bin

```
让其立刻生效

```
source /etc/profile
```

然后可以执行 java –version 看其版本号是否为1.8，如果有结果了且为1.8，至此jdk安装成

# 安装idea

到https://www.jetbrains.com/idea 下载对应版本的文件，然后将其解压。然后进入到解压后文件夹的bin目录下执行

```
./idea.sh
```
将进入安装程序，因为该程序为gui界面的程序，所以不能在command界面下安装。 
切记，在安装前必须配置好JAVA环境变量。

# 安装tomcat

从http://tomcat.apache.org/下载需要的版本，下载完后将其解压到/usr/java中，然后进入到bin目录下执行

```
./startup.sh
```
然后在浏览器访问127.0.0.1:8080，如果显示tomcat的管理页面，就表示安装成功了。-
