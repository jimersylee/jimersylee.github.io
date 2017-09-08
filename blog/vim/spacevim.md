<!--
author: Jimersy Lee
head: 
date: 2015-08-03
title: spacevim安装与使用
tags: VIM,SPACEVIM
images: 
category: 
status: publish
summary: 学习使用vim的一个扩展项目,spacevim,打造更好的vim编程环境
-->


#安装
- 安装spacevim的前提是安装好vim,一般linux系统自带
```
#redhat系列
sudo yum install vim / sudo dnf install vim
#debian系列
sudo apt install vim 
#或者
sudo apt-get install vim 

```
- 安装spacevim
```
curl -sLf https://spacevim.org/install.sh | bash
```

- 启动spacevim,等待下载插件
```
vim test.txt
```
- 使用

```
#打开目录 
vim path
#右边就会出现目录,移动光标到目录上,按回车键,就能进入目录;移动到文件上,按回车键就能打开文件;按退格键能返回上级目录
#切换工程目录区与编辑的文件
<ctrl+tab>
#创建文件
在工程区移动到文件夹下,按<shift+n>,下方提示栏就会出现提示,输入文件名,回车,创建文件
#删除文件
在工程区移动到文件夹下,按<dd>,下方提示栏就会出现提示,是否要删除,输入yes删除


