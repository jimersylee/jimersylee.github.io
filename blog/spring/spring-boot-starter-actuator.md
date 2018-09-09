<!--
author: Jimersy Lee
head: 
date: 2017-04-29
title: actuator模块简介
tags: SPRING-CLOUD
images: 
category: spring
status: publish
summary: spring-boot-starter-actuator模块简介,此模块提供监控与采集功能,通过在pom中引用此依赖,可以方便地使用其功能
-->


此模块提供监控与采集功能,通过在pom中引用此依赖,可以方便地使用其功能
```
<dependency>
            <groupId>org.springframework.boot</groupId>
            <artifactId>spring-boot-starter-actuator</artifactId>
</dependency>
```
## 原生端点
- 应用配置类:获取应用程序中加载的应用配置,环境变量,自动化配置报告等与SpringBoot应用密切相关的配置类信息
- 度量指标类:获取应用程序运行过程中用户监控的度量指标,比如内存信息,线程池信息,HTTP请求统计等
- 操作控制类:提供了对应用的关闭操作类功能

### 应用配置类
- /autoconfig:该端点用来获取应用的自动化配置报告.包括条件匹配成功以及不成功的配置
    - positiveMatches中返回是条件匹配成功的自动化配置
    - negativeMatches中返回的是条件匹配不成功的自动化配置
    
- /beans:该端点用来获取应用上下文中创建的所有Bean
    ```
    [
        {
            "context":"application",
            "parent":null,
            "beans":[
                {
                    "bean":"helloApplication",
                    "aliases":[
    
                    ],
                    "scope":"singleton",
                    "type":"com.jimersylee.hello.HelloApplication$$EnhancerBySpringCGLIB$$d3ebe421",
                    "resource":"null",
                    "dependencies":[
    
                    ]
                },
    ```
    每个Bean中都包含了下面这些信息
    - bean:Bean的名称
    - scope:Bean的作用域
    - type:Bean的Java类型
    - resource:class文件的具体路径
    - dependencies:依赖的Bean名称
    
- /configprops:该端点用来获取应用中配置的属性信息报告,可以通过使用endpoints.configprops.enabled=false来关闭

- env:该端点与/configprops不同,它用来获取应用所有可用的环境属性报告
    ```
    {
        "profiles":[
    
        ],
        "server.ports":{
            "local.server.port":8080
        },
        "servletContextInitParams":{
    
        },
        "systemProperties":{
            "java.runtime.name":"Java(TM) SE Runtime Environment",
            "awt.useSystemAAFontSettings":"gasp",
            "sun.boot.library.path":"/opt/jdk1.8.0_144/jre/lib/amd64",
            "java.vm.version":"25.144-b01",
            "maven.multiModuleProjectDirectory":"/home/jimersylee/projects/java/spring-cloud-action/hello",
            ...
    ```

- /mappings:该端点用来返回所有Spring MVC的控制器映射关系报告.
    ```
    {
        "/webjars/**":{
            "bean":"resourceHandlerMapping"
        },
        "/**":{
            "bean":"resourceHandlerMapping"
        },
        "/**/favicon.ico":{
            "bean":"faviconHandlerMapping"
        },
        "{[/hello]}":{
            "bean":"requestMappingHandlerMapping",
            "method":"public java.lang.String com.jimersylee.hello.web.HelloController.index()"
        },
        "{[/error],produces=[text/html]}":{
            "bean":"requestMappingHandlerMapping",
            "method":"public org.springframework.web.servlet.ModelAndView org.springframework.boot.autoconfigure.web.BasicErrorController.errorHtml(javax.servlet.http.HttpServletRequest,javax.servlet.http.HttpServletResponse)"
        },
        .
        .
        .
    ```
    
- /info:该端点用来返回一些应用自定义的信息.我们可以在application.properties中通过info前缀来设置一些属性,比如:
    ```
    info.app.name=spring-boot-hello
    info.app.version=v1.0.0 
    ```
    再访问/info.获得下面的报告
    ```
    {
        "app":{
            "name":"spring-boot-hello",
            "version":"v1.0.0"
        }
    }
    ```
    
    
### 度量指标类

- /metrics:该端点用来返回当前应用的各类重要度量指标,比如内存信息,线程信息,垃圾回收信息等
    ````
    {
        "mem":565018,
        "mem.free":427032,
        "processors":4,
        "instance.uptime":1276647,
        "uptime":1287345,
        "systemload.average":1.98,
        "heap.committed":496128,
        "heap.init":176128,
        "heap.used":69095,
        "heap":2494464,
        "nonheap.committed":70608,
        "nonheap.init":2496,
        "nonheap.used":68891,
        "nonheap":0,
        ...
    ````
    - 系统信息:处理器processors,运行时间uptime和instance.uptime,系统平均负载,systemload.average
    - mem.*:内存概要信息
    - heap.*:堆内存使用情况.
    - nonheap.*:非堆内存使用情况.
    - threads.*:线程使用情况,包括线程数,守护线程数(daemon),线程峰值(peak)
    - classes.*:应用加载和卸载的类统计.
    - gc.*:垃圾收集器的详细信息.包括垃圾回收次数gc.ps_scavenge.count,垃圾回收消耗时间gc.ps_scavenge.time,标记-清除算法的次数gc.ps_marksweep.count,标记-清除算法的消耗时间,gc.ps_marksweep.time
    - httpsessions.*:Tomcat容器的会话使用情况.
    - gauge.*:HTTP请求的性能指标之一,如gauge.response.hello: 5,它表示上一次hello请求的延迟时间为5毫秒
    - counter.*:HTTP请求的性能指标之一,,它主要作为计数器来使用.如counter.status.200.hello:11代表hello请求返回的200状态的次数为11
    
    我们还可以通过/metrics/{name}接口来更细粒度地获取度量信息,如果可以通过访问/metrics/mem.free来获取当前可用内存数量
    
    
- /health:获取健康指标信息.
    有时候我们还会用到Spring Boot封装的产品进行开发,比如RocketMQ.我们需要实现org.springframework.boot.actuate.health.HealthIndicator接口来实现一个对RocketMQ的检测器类
    ```
    @Component
    public class RocketMQHealthIndicator implements HealthIndicator {
        @Override
        public Health health() {
            int errorCode=this._check();
            if(errorCode!=0){
                return Health.down().withDetail("Error Code",errorCode).build();
            }
            return Health.up().build();
        }
    
        private int _check() {
            //对监控对象的检测操作
    
            return 0;
        }
    }

    ```
    返回结果
    ```
    {
        "status": "UP",
        "rocketMQ": {
            "status": "UP"
        },
        "diskSpace": {
            "status": "UP",
            "total": 125484777472,
            "free": 65173798912,
            "threshold": 10485760
        }
    }
    ```
    
- /dump:该端点用来暴露程序运行中的线程信息

- /trace:该端点用来返回基本的HTTP跟踪信息,始终保留最近的100条请求记录


###操作类控制器
可以进行关键操作,需要通过属性来配置开启操作.在原生端点中,只提供了一个用来关闭应用的端点:/shutdown(后续引入Eureka之后,会引入更多控制端点).我们可以通过配置开启它

```
endpoint.shutdown.enable=true
```
由于开放关闭应用本身是一件非常危险的事,需要对其加入一定的保护机制,比如定制actuator的端口路径,整合Spring Security进行安全校验.
