<!--
author: Jimersy Lee
head: 
date: 2015-05-10
title: 设计模式之单例模式
tags: JAVA,PATTERN
images: 
category: java
status: publish
summary: 单例模式有什么用处?有些对象其实我们只需要一个,比方说:线程池,缓存,对话框,注册表,日志对象...如果制造出多个实例,就会导致许多问题产生,例如程序的行为异常,资源使用过量,数据不一致
-->

# 单例模式
## 有什么用处
有些对象其实我们只需要一个,比方说:线程池,缓存,对话框,注册表,日志对象...如果制造出多个实例,就会导致许多问题产生,例如程序的行为异常,资源使用过量,数据不一致
## 如何做
利用静态类变量,静态方法和适当的访问修饰符

## 定义
>单例模式,确保一个类只有一个实例,并提供一个全局访问点


## 注意点,多线程中使用单例
如果有多个线程同时调用getInstance(),可能会产生多个实例,那就用synchronized(同步)关键字修饰
但是同步会降低性能,实际上也就第一次getInstance()时需要考虑同步问题,之后就没有同步问题.

## 1.如果getInstance()的性能对应用程序不是很关键,就加上synchronized关键字

```
/**
 * 单例模式之懒汉模式
 * 优点:在需要实例的时候才进行第一次实例化,在资源紧缺的时候,可以减少不必要的资源消耗
 * 缺点:同步了getInstance(),会造成性能浪费
 */
public class SingletonLazy {
    /**
     * 利用一个静态变量来记录Singleton类的唯一实例
     */
    private static SingletonLazy instance;

    /**
     * 构造器声明为私有的,只有自己Singleton类才可以调用构造器
     */
    private SingletonLazy() {

    }

    /**
     * 用getInstance()实例化对象,并返回这个实例
     * 在多线程中必须使用synchronized关键字修饰
     * @return
     */
    public static synchronized SingletonLazy getInstance() {

        //懒汉模式
        //如果未被实例化,则new
        if (instance == null) {
            instance = new SingletonLazy();
        }
        //如果已经实例化,则返回实例
        return instance;
    }
}

```

## 2.如果getInstance()的性能对应用程序很关键,那就使用饿汉模式
使用饿汉模式

```
public class SingletonLazy{
    //在静态初始化器(static initialize)中创建单例.这段代码保证了线程安全(Thread Safe)
    private static SingletonLazy instance=new SingletonLazy();
    
    private SingletonLazy(){
    }
    
    public static SingletonLazy getInstance(){
        //到这里,一定存在实例了,直接使用它
        return instance;
    }


}

```

## 3.使用"双重检查锁"(double-checked locking),在getInstance()中减少使用同步
```
/**
 * 单例模式之使用"双重检查加锁"
 * 过程:在getInstance()中进行双重检查,确保一个实例
 * 优点:在getInstance()中减少同步,增强性能,可以在多线程中使用
 * 缺点:暂无
 */
public class SingletonDoubleCheckedLocking {
    /**
     * 利用一个静态变量来记录Singleton类的唯一实例
     * volatile关键字确保:当instance变量被初始化为Singleton实例时,多个线程正确地处理instance变量
     */
    private static volatile SingletonDoubleCheckedLocking instance;

    /**
     * 构造器声明为私有的,只有自己Singleton类才可以调用构造器
     */
    private SingletonDoubleCheckedLocking() {
    }

    /**
     * 用getInstance()实例化对象,并返回这个实例
     * 方法不必用synchronized关键字修饰
     * @return
     */
    public static SingletonDoubleCheckedLocking getInstance() {
        if(instance==null){//检查实例,如果不存在,则进入同步区块
            //注意:只有第一次调用getInstance()方法才彻底执行这里的代码
            synchronized (SingletonDoubleCheckedLocking.class){
                if(instance==null){//进入区块后,再检查一次,如果是null,才创建实例
                    instance=new SingletonDoubleCheckedLocking();
                }
            }
        }

        return instance;
    }
}

```


##项目地址
[java设计模式实现](https://github.com/jimersylee/DesignPattern)
如果觉得有点收获,记得在项目上点star哦!