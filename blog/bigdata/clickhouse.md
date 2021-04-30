<!--
author: Jimersy Lee
head: 
date: 2020-09-22
title: 跨数据库实例以及复杂聚合查询数据分析方案调研
tags: MYSQL,CLICKHOUSE
images: 
category: bigdata
status: publish
summary: 目前BI需求繁多,且都是些复杂联表查询,很容易产生数据库性能问题,影响线上用户, 目前架构如下,虽然能够满足现在的需求,但面对未来更大的数据量,更加复杂的统计需求,将只能通过添加只读实例和升级配置解决

 
-->


## 背景

1. 目前BI需求繁多,且都是些复杂联表查询,很容易产生数据库性能问题,影响线上用户, 目前架构如下,虽然能够满足现在的需求,但面对未来更大的数据量,更加复杂的统计需求,将只能通过添加只读实例和升级配置解决
2. 用户基础信息与交易信息不在同一个实例,数据分析需要联合表查询得到数据,目前先查用户id再去用户信息表取存在诸多不便,需要寻求更方便的形式
3. 针对用户行为漏斗分析等场景,MySQL的支持很差,基本查不动,需要更合适的数据库

## 需求

1. 支持更复杂的聚合查询,更快的查询速度
2. 不影响用户体验
3. 更少的成本

## 方案

**方案A:metabase,使用matebase构建目前的bi系统,数据源自RDS,当进行复杂查询的时候存在性能问题,会影响生产环境**


演进版本

![](https://i.loli.net/2021/04/28/BchyPNeQnKa86Mu.png)



## 备选方案

**B:使用阿里云DLA服务**

阿里云DLA ,介绍 https://help.aliyun.com/document_detail/70378.html

跨库查询支持 https://help.aliyun.com/document_detail/107698.html

知乎专栏:https://www.zhihu.com/column/data-lake-analytics

**测试过程**

1：尝试云原生数据湖的联合查询多个MySql实例，通过https://help.aliyun.com/document_detail/107698.html?spm=a2c4g.11186623.6.837.4d7974f4GsP1S0

可以实现不同数据库的多表联合查询，但是咨询工单反馈，查询过程仍再原数据库中执行，在小数量的数据库查询中影响不大，但是在查询量较大较频繁时，无法

保证原数据库的性能。于是尝试其他方法。



2：尝试创建云原生数据湖分析中的T+1多库合并建仓，将数据库导入到DLA数据湖中，通过自己新创建的数据量很小的数据库导入，可以实现，并且不会占用大量性能。

但针对test库的多库合并建仓时，因为性能原因造成较大的影响。并且询问阿里云工单之后反馈，多库合并建仓再第一次入湖之后，再每一次的更新数据时是全量重新同步，意味着

每天进行同步都会遭遇很大的性能影响，并且数据存在一天延迟于是尝试其他方法。

优点

1. 如果使用数据库联合查询方案,可以直接跨库查询
2. 支持OSS直接上传离线数据文件进行分析

缺点

1. 虽然可以实现跨库查询,但是占用的还是生产库的计算资源,当需要复杂查询的时候可能影响用户体验
2. T+1方案存在数据延迟,且每天都是全量同步,在同步时对生产库有性能影响





**C:使用clickhouse订阅多个mysql,实现数据聚合**

**![](https://i.loli.net/2021/04/28/SPXoGlwL8mUbrxg.png)

**

**方案优势**

1. clickhouse在推出了支持MySQL实时复制后,可以很方便的订阅binlog实现clickhouse的数据库更新同步,实现准实时的数据分析
2. clickhouse数据库和用户使用的mysql数据库是完全隔离的,不会互相影响
3. 无论是直接购买还是自建,相对其他大数据方案,成本较低
4. 其他公司应用较多,携程,有赞等公司有在线上环境使用,参考资料较多
5. 针对用户行为漏斗,留存等场景,有专有函数支持计算
6. metabase支持clickhouse数据库,原来的使用习惯不用改变,还是可以通过bi看数据

**测试过程**

ClickHouse，通过购买建立ClickHouse集群，通过购买DTS数据同步，将RDS数据库中的数据导入到ClickHouse集群中，可以实现不同数据的不同表导入到同一个数据库中，实现快速查询。

ClickHouse ，并且可以实时更新数据。 现ClickHouse配置为4核8G配置 1000G 属于按量付费，约1.5RMB/小时，30RMB/天，如需长期使用建议转为按月付费约600RMB/月， DTS数据同步

现购买两个每个约0.5RMB/小时，两个共计20RMB/天，如需长期使用建议按月付费每个400RMB/月

**初始成本1400/月,后续可以视业务规模扩容升配置**

**线上业务性能对比**

实例配置对比 mysql5.7 16核64G内存   clickhouse 20.3 4核8G

查询速度对比:



| MySQL语法                                                    | clickhouse语法                                               | mysql   | clickhouse | 性能差异 |
| :----------------------------------------------------------- | :----------------------------------------------------------- | :------ | :--------- | :------- |
| select COUNT( *) FROM trade_order_asset                      | select COUNT( *) FROM trade_order_asset                      | 4000ms  | 4ms        | 1000倍   |
| mysql: select COUNT(*) from steam_trade_db.trade_product_sell where create_time >=unix_timestamp('2021-03-01 00:00:00') | select COUNT(*) from steam_trade_db.trade_product_sell where create_time >=toUnixTimestamp('2021-03-01 00:00:00') | 34374ms | 45ms       | 1001倍   |
| -- 次月交易留存：指在上月有过交易的用户，在当月再次有交易行为，不包含系统账号 SELECT count(DISTINCT l.user_id) as 月交易人数,count(DISTINCT r.user_id) as 上月留存交易用户 FROM (SELECT DISTINCT buyer_user_id as user_id             FROM trade_order_asset force index(IDX_CTIME)             WHERE buyer_platform_id = 2 AND status = 10 AND is_import = 0                 AND create_time >= UNIX_TIMESTAMP('2021-02-01')                 AND create_time < UNIX_TIMESTAMP('2021-03-01')                 UNION             SELECT DISTINCT seller_user_id as user_id             FROM trade_order_asset force index(IDX_CTIME)             WHERE seller_platform_id = 2 AND status = 10 AND is_import = 0                 AND create_time >= UNIX_TIMESTAMP('2021-02-01')                 AND create_time < UNIX_TIMESTAMP('2021-03-01')             ) l LEFT JOIN (    SELECT DISTINCT buyer_user_id as user_id                         FROM trade_order_asset force index(IDX_CTIME)                         WHERE buyer_platform_id = 2 AND status = 10 AND is_import = 0                             AND create_time >= UNIX_TIMESTAMP('2021-01-01')                             AND create_time < UNIX_TIMESTAMP('2021-02-01')                             UNION                         SELECT DISTINCT seller_user_id as user_id                         FROM trade_order_asset force index(IDX_CTIME)                         WHERE seller_platform_id = 2 AND status = 10 AND is_import = 0                             AND create_time >= UNIX_TIMESTAMP('2021-01-01')                             AND create_time < UNIX_TIMESTAMP('2021-02-01')                     ) r ON l.user_id = r.user_id LEFT JOIN trade_user c ON l.user_id = [c.id](http://c.id/) WHERE c.third_user_id not in (555136127) ; | -- 次月交易留存：指在上月有过交易的用户，在当月再次有交易行为，不包含系统账号 SELECT count(DISTINCT l.user_id) as trade_num,count(DISTINCT r.user_id) as live_num FROM (SELECT DISTINCT buyer_user_id as user_id             FROM trade_order_asset              WHERE buyer_platform_id = 2 AND status = 10 AND is_import = 0                 AND create_time >= toUnixTimestamp('2021-02-01 00:00:00')                 AND create_time < toUnixTimestamp('2021-03-01 00:00:00')                 UNION ALL             SELECT DISTINCT seller_user_id as user_id             FROM trade_order_asset              WHERE seller_platform_id = 2 AND status = 10 AND is_import = 0                 AND create_time >= toUnixTimestamp('2021-02-01 00:00:00')                 AND create_time < toUnixTimestamp('2021-03-01 00:00:00')             ) l LEFT JOIN (    SELECT DISTINCT buyer_user_id as user_id                         FROM trade_order_asset                          WHERE buyer_platform_id = 2 AND status = 10 AND is_import = 0                             AND create_time >= toUnixTimestamp('2021-01-01 00:00:00')                             AND create_time < toUnixTimestamp('2021-02-01 00:00:00')                             UNION ALL                         SELECT DISTINCT seller_user_id as user_id                         FROM trade_order_asset                          WHERE seller_platform_id = 2 AND status = 10 AND is_import = 0                             AND create_time >= toUnixTimestamp('2021-01-01 00:00:00')                             AND create_time < toUnixTimestamp('2021-02-01 00:00:00')                     ) r ON l.user_id = toInt64(r.user_id) LEFT JOIN trade_user c ON l.user_id = toInt64([c.id](http://c.id/)) WHERE c.third_user_id not in (555136127) ; | 16908ms | 2080ms     | 8倍      |
|                                                              |                                                              |         |            |          |



## 调研过程

1：尝试云原生数据湖的联合查询多个MySql实例，通过https://help.aliyun.com/document_detail/107698.html?spm=a2c4g.11186623.6.837.4d7974f4GsP1S0

可以实现不同数据库的多表联合查询，但是咨询工单反馈，查询过程仍再原数据库中执行，在小数量的数据库查询中影响不大，但是在查询量较大较频繁时，无法

保证原数据库的性能。于是尝试其他方法。



2：尝试创建云原生数据湖分析中的T+1多库合并建仓，将数据库导入到DLA数据湖中，通过自己新创建的数据量很小的数据库导入，可以实现，并且不会占用大量性能。

但针对test库的多库合并建仓时，因为性能原因造成较大的影响。并且询问阿里云工单之后反馈，多库合并建仓再第一次入湖之后，再每一次的更新数据时是全量重新同步，意味着

每天进行同步都会遭遇很大的性能影响，并且数据存在一天延迟于是尝试其他方法。



3：ClickHouse，通过购买建立ClickHouse集群，通过购买DTS数据同步，将RDS数据库中的数据导入到ClickHouse集群中，可以实现不同数据的不同表导入到同一个数据库中，实现

快速查询。在单表通过执行 select COUNT( *) FROM trade_order_asset  统计语句ClickHouse中查询完成需要约3-4ms，在原数据库中查询，约3000-4000ms，ClickHouse性能领先。

ClickHouse ，并且可以实时更新数据。 现ClickHouse配置为4核8G配置 1000G 属于按量付费，约1.5RMB/小时，30RMB/天，如需长期使用建议转为按月付费约600RMB/月， DTS数据同步

现购买两个每个约0.5RMB/小时，两个共计20RMB/天，如需长期使用建议按月付费每个400RMB/月



参考资料

[ClickHouse王炸功能即将来袭?](https://mp.weixin.qq.com/s/CxvWmgjywHLFraGF9F1ROw)

[ClickHouse和他的朋友们（9）MySQL实时复制与实现](https://bohutang.me/2020/07/26/clickhouse-and-friends-mysql-replication/#性能测试)

[Clickhouse中文文档](https://clickhouse.tech/docs/zh/)

clickhouse,mysql性能对比:https://blog.csdn.net/bluetjs/article/details/80539847

clickhouse,postgresql对比:https://www.codenong.com/cs109208273/

[ClickHouse实战留存、路径、漏斗、session](https://my.oschina.net/u/4658124/blog/4804625)

[ClickHouse 在有赞的实践之路](https://tech.youzan.com/clickhouse-zai-you-zan-de-shi-jian-zhi-lu/)

[趣头条基于Flink+ClickHouse的实时数据分析平台](https://cloud.tencent.com/developer/news/650772)
