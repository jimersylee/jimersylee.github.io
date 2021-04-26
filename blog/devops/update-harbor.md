<!--
author: Jimersy Lee
head: 
date: 2020-06-03
title: harbor升级安装
tags: LINUX,HARBOR,DEVOPS
images:
category: devops
status: publish
summary: 软件还是经常升级的,否则怎么使用最新的功能
-->


# harbor升级安装



## 前期准备
*  安装docker,略去不表
*  安装docker-compose，略去不表

## 安装harbor2.0.1

下载离线安装包
[github下载地址](https://github.com/goharbor/harbor/releases)

[2.0.1离线安装包下载地址](https://github.com/goharbor/harbor/releases/download/v2.0.1/harbor-offline-installer-v2.0.1.tgz)

```
cd /opt
wget https://github.com/goharbor/harbor/releases/download/v2.0.1/harbor-offline-installer-v2.0.1.tgz
tar -xvf harbor-offline-installer-v2.0.1.tgz
cd harbor
cp harbor.yml.tmpl harbor.yml
mkdir ssl
# 把ssl证书文件放在这,分别为reg.jimersylee.cn.key,reg.jimersylee.cn.pem
cd ..
vim harbor.yml
# 修改以下选项
hostname: reg.jimersylee.cn
https:
  # https port for harbor, default is 443
  port: 443
  # The path of cert and key files for nginx
  certificate: /opt/harbor/ssl/reg.jimersylee.cn.pem
  private_key: /opt/harbor/ssl/reg.jimersylee.cn.key

#数据库密码和管理后台管理员密码

#保存文件

# 运行准备脚本
./prepare

./install.sh


#ok


```


## 收尾
* 把原来的仓库的帐号建一下，jimersylee jimersylee888
* 上传基础镜像