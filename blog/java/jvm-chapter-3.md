<!--
author: Jimersy Lee
head: 
date: 2016-08-20
title: <深入理解Java虚拟机>学习笔记-第三章
tags: JAVA,JVM
images: 
category: java
status: publish
summary: 
-->

#第三章 垃圾收集器与内存分配策略
项目地址: [http://github.com/jimersylee/javaStudy]('http://github.com/jimersylee/javaStudy')

##3.1概述
垃圾收集(Garbage Collection,GC),大部分人都把这项技术当做Java语言的伴生产物,事实上,1960年诞生于MIT的Lisp蚕食第一门真正使用内存动态分配和垃圾收集技术的语言

GC的3件事情
- 那些内存需要回收
- 什么时候回收
- 如何回收

目前GC的技术已经相当成熟,为什么我们要了解
- 当需要排查各种内存溢出,内存泄露问题时
- 当垃圾收集成为系统达到更高并发量瓶颈时

##3.2对象已死吗
Java堆中存在对象实例,垃圾收集器在对堆进行回收前,第一件事就是要确定哪些对象还"活着",哪些已经"死去"

##3.2.1 引用计数算法
给对象中添加一个引用计数器:每当有一个地方引用它时,计数器加1;当引用失效时,计数器减1;任何时刻计数器为0的对象就是不可能再被使用的

引用计数算法(Reference Counting)的实现简单,判定效率也高,例如FlashPlayer,Python都使用引用计数算法了内存管理,但是Java虚拟机中没有选用引用计数算法来管理内存,其实最重要的原因是它很难解决对象之间相互循环引用的问题

testGC()方法,虽然两个对象已经不可能被访问,但是因为互相引用着,导致他们计数器都不为0,于是引用计数算法无法通知GC收集器来回收它们

GC日志中包含了6747K->416K(51200K),意味着虚拟机并没有因为这两个对象互相引用就不回收它们,这也从侧面说明虚拟机并不是通过引用计数算法来判断对象是否是存活的

##3.2.2 可达性分析算法
在主流的商用程序语言(Java,C#,甚至古老的Lisp)的主流实现中,都是通过可达性分析(Reachability Analysis)来判定对象是否存活的.这个算法的基本思路就是通过一系列的称为"GC Roots"的对象作为起始点,从这些节点开始向下搜索,搜索所走过的路径称为引用链(Reference Chain),当一个对象到GC Roots没有任何引用链(用图论的话来说,就是从GC Root到这个对象不可达),则证明此对象是不可用的.

在Java语言中,可作为GC Roots的对象包括下面几种:
- 虚拟机栈(栈帧中的本地变量表)中引用的对象
- 方法区中类静态属性引用的对象
- 方法区中常量引用的对象
- 本地方法栈中JNI(Native方法)引用的对象

##3.2.3 再谈引用
jdk1.2后,对引用的概念进行了扩充
- 强引用
    类型Object obj=new Object();这类的,只要强引用还在,垃圾收集器永远不会回收掉被引用的对象
- 软引用
    软引用用来描述一些还有用但并非必需的对象.在系统将要发生内存溢出异常之前,将会把这些对象列进回收范围内进行二次回收.如果这次回收还没有足够的内存,才会抛出内存溢出异常.在JDK1.2后,提供了SoftReference类来实现软引用.
- 弱引用
    弱引用也是用来描述非必需对象.当垃圾收集器工作时,无论当前内存是否足够,都会回收掉只被弱引用关联的对象.在JDK1.2后,提供了WeakReference类实现弱引用.
- 虚引用
    虚引用也称为幽灵引用或者幻影引用.一个对象是否有虚引用的存在,完全不会对其生存时间构成影响,也无法通过虚引用来获得一个对象实例.为一个对象设置虚引用是为了能在这个对象被回收时能收到系统通知.在JDK1.2后,提供了PhantomReference类实现虚引用

##3.2.4 生存还是死亡
即使在可达性分析算法中不可达的对象,也并非是"非死不可"的,要宣告一个对象死亡,至少经历两次标记过程
代码清单3-2 一次对象自我拯救的演示
```
/**
 * 此代码演示了两点
 * 1.对象可以在被GC时自我拯救
 * 2.这个自救机会只有一次
 *
 * @author jimersylee
 */
public class FinalizeEscapeGC {
    public static FinalizeEscapeGC SAVE_HOOK = null;

    public void isAlive() {
        System.out.println("yes,i am still alive");
    }

    @Override
    protected void finalize() throws Throwable {
        super.finalize();
        System.out.println("finalize method executed");
        FinalizeEscapeGC.SAVE_HOOK = this;
    }

    public static void main(String[] args) throws Throwable {
        SAVE_HOOK = new FinalizeEscapeGC();
        //对象第一次成功拯救自己演示

        //释放对象
        SAVE_HOOK = null;
        System.gc();
        //因为finalize方法优先度低,所以暂停0.5秒等待它
        Thread.sleep(500);
        if (SAVE_HOOK != null) {
            SAVE_HOOK.isAlive();
        } else {
            System.out.println("no,i am dead :(");
        }

        //下面这段代码与上面的完全相同,但是这次自救却失败了
        SAVE_HOOK = null;
        System.gc();
        //因为finalize方法优先度低,所以暂停0.5秒等待它
        Thread.sleep(500);
        if (SAVE_HOOK != null) {
            SAVE_HOOK.isAlive();
        } else {
            System.out.println("no,i am dead :(");
        }
    }
}

```
##3.2.5 回收方法区
