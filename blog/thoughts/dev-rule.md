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




##基于自建SVN的信息存储
- 存放开发文档
- 存放测试用例
- 存放三方对接文档
- 存放同事的学习记录与分享
- 存放会议纪要

##基于git的开发流程
- 代码全部托管于git服务上
- 完善的分支规范,区分开发,测试,生产分支
- 基于gitflow的开发流程,区分feature,bugfix,hotfix等分支创建规则




##完善的测试流程

- 产品出文档测试即开始编写测试用例
- 测试版本开发完成开始测试
- 使用bugout进行记录与跟踪bug
- 开发处理bug,在bugout上提交
- 测试再次测试直到通过



#基于持续构建工具的构建流程
- 使用jenkin持续构建,单元测试,Sonar代码分析
- 每个项目至少3个分支,dev,master,release分支
- 测试在dev分支,预发布在master分支,线上使用release分支
- dev分支自动提交构建

#平台架构
- 分布式的微服务架构
- 保证单机宕机对整体服务没有影响,做到自动切换主备

#统一开发环境与工具
- 后端开发统一Jetbrains全家桶
- 接口调试使用Postman
- 文档使用showdoc
- 代码管理使用git
- 统一使用邮件沟通

