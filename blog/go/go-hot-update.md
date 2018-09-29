<!--
author: Jimersy Lee
head: 
date: 2018-09-30
title: go-web程序的热更新
tags: GO
images: 
category: go
status: publish
summary: 一直编译累死人啊，该偷懒就得偷懒
-->


当使用go开发web程序时，修改点代码就得编译，虽然编译速度很快，但是也累啊，想起java的spring-boot有热更新插件，
php根本都不需要重启，go怎么可以落后。

一顿搜索后，找到了[gin](https://github.com/codegangsta/gin)和[fresh](https://github.com/pilu/fresh),都挺好用的

## gin
```shell
cd path/to/app
gin run main.go

```

## fresh
```shell
cd path/to/app
fresh
```


懒人有懒福～