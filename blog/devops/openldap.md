<!--
author: Jimersy Lee
head: 
date: 2020-05-04
title: openldap实战
tags: LINUX,
images: 
category: devops
status: publish
summary: 当公司到一定规模,管理各种内部系统的账号逐渐成为问题,OpenLDAP就是用来解决这种问题的
-->

# 基于docker的ldap服务部署,使用LAM和openldap




## 参考项目

[docker-openldap](https://github.com/osixia/docker-openldap)

[docker-lam](https://hub.docker.com/r/ldapaccountmanager/lam)

[ldapadmin](http://www.ldapadmin.org/)

[docker-phpLDAPadmin](https://github.com/osixia/docker-phpLDAPadmin)

## 参考文章

[jenkins集成openldap](https://www.cnblogs.com/zhaojiedi1992/p/zhaojiedi_liunx_52_ldap_for_jenkins.html)


[Centos7以Docker方式运行ldap服务](https://www.58jb.com/html/ldap-run-on-centos7.html)
[jenkins整合ldap认证](https://www.58jb.com/html/121.html)


## 选型

* [docker-openldap](https://github.com/osixia/docker-openldap)
* [docker-lam](https://hub.docker.com/r/ldapaccountmanager/lam)
## 操作记录

```
#运行openldap
docker run -p 389:389 --name openldap --restart=always --env LDAP_ORGANISATION="sotemalltest" --env LDAP_DOMAIN="sotemalltest.com" --env LDAP_ADMIN_PASSWORD="redhat" --detach osixia/openldap


#运行lam

docker run -d --restart=always --name ldap-account-manager -p 81:80 \
        --link openldap:ldap-host \
        --env PHPLDAPADMIN_LDAP_HOSTS=ldap-host \
        --env PHPLDAPADMIN_HTTPS=false \
        --detach ldapaccountmanager/lam

```





## jenkins配置

```
group search filter

(& (cn={0}) (| (objectclass=groupOfNames) (objectclass=groupOfUniqueNames) (objectclass=posixGroup)))

group membership filter

(| (member={0}) (uniqueMember={0}) (memberUid={1}))
```

![jenkins配置截图](https://i.loli.net/2020/05/04/IPWGpbrYKaU2DLs.png)