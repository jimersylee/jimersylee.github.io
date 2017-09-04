<!--
author: Jimersy Lee
head: 
date: 2016-08-05
title: 为域名配置免费SSL证书
tags: NGINX,SSL
images: 
category: devops
status: publish
summary: 本文在CenOS主机上,使用腾讯云主机,以及阿里云域名服务,完成Nginx上指定的域名的ssl配置
-->


#Nginx配置ssl
- 腾讯云申请证书
    ![申请证书](http://jimersyleetest.qiniudn.com/%E7%94%B3%E8%AF%B7%E8%AF%81%E4%B9%A6.png)
    
    ![选择验证方式](http://jimersyleetest.qiniudn.com/%E9%AA%8C%E8%AF%81%E8%AF%81%E4%B9%A6.png)
    ![获得验证内容](http://jimersyleetest.qiniudn.com/%E6%B7%BB%E5%8A%A0%E8%AE%B0%E5%BD%95.png)
    
- 阿里云域名解析验证证书
    ![解析](http://jimersyleetest.qiniudn.com/%E8%A7%A3%E6%9E%90%E8%AE%BE%E7%BD%AE.png)
- 安装证书
    1. 下载证书
    2. 上传配置证书
        ```
        #将本地的jenkins.jimersylee.com.zip上传至jimersylee.com这个机子上的/data/ssl_cert目录
        scp jenkins.jimersylee.com.zip root@jimersylee.com:/data/ssl_cert
        #登录主机解压文件
        ssh root@jimersylee.com
        cd /data/ssl_cert
        unzip jenkins.jimersylee.com.zip
        #各种web服务器的证书就解压完成了,然后去配置Nginx
        [root@VM_77_132_centos ssl_cert]# tree
        .
        ├── Apache
        │   ├── 1_root_bundle.crt
        │   ├── 2_blog.jimersylee.com.crt
        │   ├── 2_jenkins.jimersylee.com.crt
        │   ├── 2_jimersylee.com.crt
        │   ├── 3_blog.jimersylee.com.key
        │   ├── 3_jenkins.jimersylee.com.key
        │   └── 3_jimersylee.com.key
        ├── blog.jimersylee.com.cert.zip
        ├── IIS
        │   ├── blog.jimersylee.com.pfx
        │   ├── jenkins.jimersylee.com.pfx
        │   ├── jimersylee.com.pfx
        │   └── keystorePass.txt
        ├── jenkins.jimersylee.com.zip
        ├── jimersylee.com.cert.zip
        ├── Nginx
        │   ├── 1_blog.jimersylee.com_bundle.crt
        │   ├── 1_jenkins.jimersylee.com_bundle.crt
        │   ├── 1_jimersylee.com_bundle.crt
        │   ├── 2_blog.jimersylee.com.key
        │   ├── 2_jenkins.jimersylee.com.key
        │   └── 2_jimersylee.com.key
        └── Tomcat
            ├── jenkins.jimersylee.com.jks
            └── keystorePass.txt
      
        ```
- nginx配置
```
#创建配置文件
vim /etc/nginx/conf.d/jenkins.conf

#写入以下内容

#配置443端口
server {
        listen       443;
        server_name  jenkins.jimersylee.com;
        ssl on;
        ssl_certificate /data/ssl_cert/Nginx/1_jenkins.jimersylee.com_bundle.crt;
        ssl_certificate_key /data/ssl_cert/Nginx/2_jenkins.jimersylee.com.key;
        ssl_session_timeout 5m;
        ssl_protocols TLSv1 TLSv1.1 TLSv1.2; #按照这个协议配置
        ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:HIGH:!aNULL:!MD5:!RC4:!DHE;#按照这个套件配置
        ssl_prefer_server_ciphers on;
        root /data/java_app/tomcat9/webapps;

        index index.html index.htm index.php;
        location / {
           try_files $uri @jenkins;
        }
        location @jenkins {
           internal;
           proxy_pass http://127.0.0.1:8080;
                                                          }

        access_log /data/logs/jenkins/jenkins.log main;
        }
#转发80的访问到jenkins
server {
    listen 80;
    server_name jenkins.jimersylee.com;
    rewrite ^ https://$server_name$request_uri? permanent;
    }

```
- 重启Nginx生效
    ```
    nginx -s stop  #停止
    nginx -t  #测试Nginx配置是否正确
    nginx  #启动Nginx
    ```
