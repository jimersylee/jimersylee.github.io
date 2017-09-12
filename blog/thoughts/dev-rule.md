<!--
author: Jimersy Lee
head: 
date: 2017-07-22
title: 我眼中规范的开发流程
tags: DEV,RULE
images: 
category: thoughts
status: publish
summary: 所谓没有规矩,不成方圆,就算是再小的团队,工作也需要规范的流程.
-->




##基于SVN的信息存储
- 存放开发文档
- 存放测试用例
- 存放三方对接文档
- 存放同事的学习记录与分享
- 存放会议纪要


##测试

###完善的测试流程

- 产品出文档测试即开始编写测试用例
- 测试版本开发完成开始测试
- 使用bugout进行记录与跟踪bug
- 开发处理bug,在bugout上提交
- 测试再次测试直到通过



#构建
- 使用jenkin持续构建,单元测试,Sonar代码分析
- 每个项目至少3个分支,dev,master,release分支
- 测试在dev分支,预发布在master分支,线上使用release分支
- dev分支自动提交构建

#平台架构
分布式的微服务架构

#统一开发环境与工具
后端开发统一Jetbrains全家桶
接口调试使用Postman
文档使用showdoc

