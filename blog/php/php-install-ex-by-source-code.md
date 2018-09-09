<!--
author: Jimersy Lee
head: 
date: 2015-03-10
title: PHP源码安装扩展
tags: PHP
images: 
category: php
status: publish
summary: 本文介绍了如何使用PHP从源码安装扩展
-->


###环境:CentOS7,PHP5.3.6源码安装,APACHE服务器
###以安装mbstring扩展为例
```
#切换到php源码目录中的扩展目录中的mbstring源码目录
cd /usr/src/php-5.3.6/ext/mbstring

#运行phpize
/usr/local/php/bin/phpize

#运行configure
./configure --with-php-config=/usr/local/php/bin/php-config


#编译和安装
make && make install


#开启mbstring扩展
echo ‘extension=mbstring.so' >>/usr/local/php/lib/php.ini

#重启web服务器
/usr/local/apache2/bin/apachectl restart
```