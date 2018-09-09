<!--
author: Jimersy Lee
head: 
date: 2015-08-04
title: 使用scrapy写爬虫
tags: PYTHON,SCRAPY,CRAWLER
images: 
category: python
status: publish
summary: 学习使用python的scrapy写爬虫
-->



一开始想在windows环境下安装scrapy,无奈安装多次都失败,转向linux

linux自带python2.7 因此只需要安装scrapy模块就行

先用pip安装Scrapy 失败

于是安装easy_install

命令行 sudo apt-get install python-setuptools

sudo easy_install Scrapy

出现错误,搜索知道必须使用python的dev版本

于是  sudo apt-get install python-dev

再次 sudo easy_install Scrapy

安装成功

然后安装mongodb

sudo easy_instal pymongo
