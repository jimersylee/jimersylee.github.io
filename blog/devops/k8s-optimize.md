<!--
author: Jimersy Lee
head: 
date: 2020-09-28
title: 关于k8s集群中的容器资源限制以及JAVA应用运行参数设置
tags: K8S,JVM
images: 
category: devops
status: publish
summary: 目前存在一些应用频繁重启的问题,查看日志,有的显示容器被驱逐(evic),这种情况一般为资源使用超过配置的限制,k8s为了整体稳定性进行服务的重启,防止一些应用层的内存泄漏问题导致整体服务稳定性下降,针对此问题,需要调研k8s集群中,容器的内存分配,以及应用是否能正确识别到分配的容器内存限制
-->


# 背景

目前存在一些应用频繁重启的问题,查看日志,有的显示容器被驱逐(evic),这种情况一般为资源使用超过配置的限制,k8s为了整体稳定性进行服务的重启,防止一些应用层的内存泄漏问题导致整体服务稳定性下降

针对此问题,需要调研k8s集群中,容器的内存分配,以及应用是否能正确识别到分配的容器内存限制

# 现状

- 目前线上的java应用使用的基础镜像为:library/openjdk8-skywalking:latest
- 此镜像的项目地址为 :middleware/docker-image-openjdk8-skywalking
- 此基础镜像包含的软件为:
    - openjdk-8u212
    - nginx
    - curl
    - vim
    - php7
    - php7-pecl-mongodb
    - skywalking
- 线上使用配置
    - 通过设置环境变量,运行脚本使用环境变量配置jvm参数实现
    - ![](https://i.loli.net/2021/04/29/nQJ4sRZp791HAhi.png)

## 本地测试结果

## 自动方案

- 不带jvm参数运行,看默认配置为多少内存,给容器设置最大内存限制为2GB,jvm设置的最大堆内存为455.50M,约为1/4最大内存,符合预期

    - docker run -m 2GB --rm [reg.xxxx.cn/library/openjdk8-skywalking:latest](http://reg.xxxx.cn/library/openjdk8-skywalking:latest) java -XshowSettings:vm -version
      VM settings:
      Max. Heap Size (Estimated): 455.50M
      Ergonomics Machine Class: server
      Using VM: OpenJDK 64-Bit Server VM

      openjdk version "1.8.0_212"
      OpenJDK Runtime Environment (IcedTea 3.12.0) (Alpine 8.212.04-r0)
      OpenJDK 64-Bit Server VM (build 25.212-b04, mixed mode)

- #### OpenJdk

  自动识别到容器限制后，OpenJdk把最大堆设置为了大概容器内存的1/4，对内存的浪费不可谓不大。

- 我们尝试将MaxRAMFraction设置手动设置为1,可以看到最大堆内存将被设置为1.78G,我觉得这样的资源利用率比较高

    - $ docker run -m 2GB --rm [reg.xxxx.cn/library/openjdk8-skywalking:latest](http://reg.xxxx.cn/library/openjdk8-skywalking:latest) java -XshowSettings:vm -XX:MaxRAMFraction=1 -version
      VM settings:
      Max. Heap Size (Estimated): 1.78G
      Ergonomics Machine Class: server
      Using VM: OpenJDK 64-Bit Server VM

      openjdk version "1.8.0_212"
      OpenJDK Runtime Environment (IcedTea 3.12.0) (Alpine 8.212.04-r0)
      OpenJDK 64-Bit Server VM (build 25.212-b04, mixed mode)

## 手动方案

- 手动设置最大堆内存,有效,能避免内存过大导致的被kill

    - $ docker run -m 2GB --rm [reg.xxxx.cn/library/openjdk8-skywalking:latest](http://reg.xxxx.cn/library/openjdk8-skywalking:latest) java -XshowSettings:vm -Xmx1800m -version
      VM settings:
      Max. Heap Size: 1.76G
      Ergonomics Machine Class: server
      Using VM: OpenJDK 64-Bit Server VM

      openjdk version "1.8.0_212"
      OpenJDK Runtime Environment (IcedTea 3.12.0) (Alpine 8.212.04-r0)
      OpenJDK 64-Bit Server VM (build 25.212-b04, mixed mode)



# 结论

1. 目前的手动设置jvm的堆内存的方案可行,缺点是为了每个应用都需要按情况配置不同的jvm参数,比较麻烦容易出错
2. 如果直接使用自动化方案,不带jvm参数,应用能够正确识别分配给容器的最大内存,但是内存使用率较低,约为25%,存在大量的浪费,因此推荐统一加上-XX:MaxRAMFraction=1参数,提高资源利用率,且方便进行资源限制更改,只需要在k8s后台进行配置就ok



## 参考文章

1. [Java程序在K8S容器部署CPU和Memory资源限制相关设置](https://yq.aliyun.com/articles/700701)
2. [容器中的JVM资源该如何被安全的限制？](https://www.kubernetes.org.cn/5005.html)
3. [容器环境的JVM内存设置最佳实践](https://www.cnblogs.com/xiaoqi/p/container-jvm.html)[Kubernetes 案例分享：如何避免 JVM 应用内存耗尽](https://www.javazhiyin.com/55733.html)
4. [容器环境的JVM内存设置最佳实践](https://www.cnblogs.com/xiaoqi/p/container-jvm.html)
