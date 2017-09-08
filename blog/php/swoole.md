<!--
author: Jimersy Lee
head: 
date: 2017-09-08
title: PHP的Swoole扩展安装与学习
tags: PHP,SWOOLE
images: 
category: php
status: publish
summary: PHP的Swoole扩展安装与学习
-->


#安装swoole
```
#直接使用pecl安装扩展
sudo pecl install swoole
#检测是否安装成功
php -m|grep swoole
#如果有swoole则安装成功,否则,在php.ini中增加扩展
#获取php.ini的绝对路径
php -i |grep php.ini
cd  path
vim php.ini
#增加扩展
extension=swoole
```


#跑测试例程
```
#创建文件
vim http_server.php

#输入代码
<?
$http = new swoole_http_server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {
    var_dump($request->get, $request->post);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();

#启动程序
php http_server.php

#看看成功没有
curl http://127.0.0.1:9501

#如果正确输出,那就启动成功啦

```

