<!--
author: Jimersy Lee
head: 
date: 2014-01-01
title: CentOS7安装Jenkins
tags: CENTOS,JENKINS
images: 
category: devops
status: publish
summary: 本文展示了如何在CentOS上通过yum安装jenkins,而不是使用war包,以及tomcat容器启动jenkins
-->


环境:CentOS7

1.下载安装包
在https://jenkins.io/download/找到相应的包下载地址
```
wget https://pkg.jenkins.io/redhat/jenkins-2.76-1.1.noarch.rpm
```


2.安装包
```
sudo rpm -i jenkins-2.76-1.1.noarch.rpm
```


3.启动jenkins
```
sudo service jenkins start
```


4.配置nginx
```
vim /etc/nginx/conf.d/jenkins.conf
server {
 2         listen       443;
 3         server_name  jenkins.jimersylee.com;
 4         ssl on;
 5         ssl_certificate /data/ssl_cert/Nginx/1_jimersylee.com_bundle.crt;
 6         ssl_certificate_key /data/ssl_cert/Nginx/2_jimersylee.com.key;
 7         ssl_session_timeout 5m;
 8         ssl_protocols TLSv1 TLSv1.1 TLSv1.2; #按照这个协议配置
 9         ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:HIGH:!aNULL:!MD5:!RC4:!DHE;#按照这个套件配置
10         ssl_prefer_server_ciphers on;
11        # root /data/www/www;

12
13         index index.html index.htm index.php;
14         location /
15         {
16             proxy_pass http://127.0.0.1:8080;
17         }
18
19         access_log /data/logs/jenkins/jenkins.log main;
20         }
21
22 server {
23     listen 80;
24     server_name jenkins.jimersylee.com;
25     rewrite ^ https://$server_name$request_uri? permanent;
26     }

```

5.访问https://jenkins.jimersylee.com/
发现没有账号密码,修改jenkins为不需要账号密码
```
vim /var/lib/jenkins/config.xml
<useSecurity>true</useSecurity>修改为false
重启jenkins
sudo service jenkins restart

```

6.访问https://jenkins.jimersylee.com/
系统管理->Configure Global Security 勾选启用安全,勾选使用jenkins专有数据库

7.因为要使用某个特定账号如dev去执行shell,因此将jenkins的默认启动账号jenkins修改为dev

8.如何修改运行jenkins进程的linux帐号？

   - 找到jenkins的配置文件，一般是/etc/sysconfig/jenkins

   - 修改下面的参数为相应的用户，比如JENKINS_USER="dev"
   ```
   ## Type:        string
   ## Default:     "jenkins"
   ## ServiceRestart: jenkins
   #
   # Unix user account that runs the Jenkins daemon
   # Be careful when you change this, as you need to update
   # permissions of $JENKINS_HOME and /var/log/jenkins.
   #
   JENKINS_USER="dev"
   ```
   - 修改下来文件或目录的权限
   
  ```
  chown dev:dev file
  chown -R admin:admin directory
  /var/lib/jenkins/
  /var/log/jenkins/
  /var/cache/jenkins/
  /usr/lib/jenkins/jenkins.war
  /etc/sysconfig/jenkins
  ```
   - 重启jenkins：service jenkins restart
