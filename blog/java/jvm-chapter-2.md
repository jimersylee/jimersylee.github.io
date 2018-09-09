<!--
author: Jimersy Lee
head: 
date: 2016-08-11
title: <深入理解Java虚拟机>学习笔记-第二章
tags: JAVA,JVM
images: 
category: java
status: publish
summary: 
-->

#Java与C++的区别
项目地址: [http://github.com/jimersylee/javaStudy]('http://github.com/jimersylee/javaStudy')
##2.1 概述
内存管理领域
- c,c++开发人员拥有最高权利,可以操作每一个对象,又需要维护每个对象的开始与销毁
- java开发人员不需要为每一个对象写配对的delete/free代码,不容易出现内存泄露与内存溢出问题,不过,出现内存方面的问题,排查又是一项异常艰难的工作
##2.2运行时数据区域
- 方法区Method Area
- 虚拟机栈VM Stack
- 本地方法栈Native Method Stack
- 堆 Heap
- 程序计数器 program Counter Register
###2.2.1 程序计数器
java虚拟机通过线程轮换来分配处理器执行时间,为了线程切换后能恢复到正确的执行位置,每条线程都需要一个独立的程序计数器,为*线程私有*内存
###2.2.2 Java虚拟机栈
与程序计数器一样,Java虚拟机栈也是线程私有的,它的生命周期与线程相同.
###2.2.3 本地方法栈
本地方法栈为虚拟机使用到的Native方法服务
###2.2.4 Java堆
Java堆(Heap)是Java虚拟机所管理的 内存中最大的一块,Java对是被所有线程共享的一块内存区域,在虚拟机启动时创建.此内存区域的唯一目的就是存放对象实例.
Java堆是垃圾收集器管理的主要区域
现在的收集器基本采用分代收集算法
####Java堆
- 新生代
- 老年代
更细致
- Eden空间
- From Survivor空间
- To Survivor空间

Java堆可以处于物理上不连续的空间中,只要逻辑上连续即可,既可以实现成固定大小的,也可以是可扩展的,通过 *-Xmx* 和 *-Xms*控制,如果堆中没有足够的内存完成实例分配,则报 *OutOfMemory* 异常

###2.2.5
方法区与Java堆一样,是各个线程共享的内存区域,用于存放已被虚拟机加载的类信息,敞亮,静态变量,即时编译器编译后的代码等数据

HotSpot上,方法区习惯性称为 *永久代(Permanent Generation)* 

永久代有 *-XX:MaxPermSize* 上限
###2.2.6 运行时常量池
运行时常量池(Runtime Constant Pool)是方法区的一部分.用于存放编译期生成的各种字面量和符号引用.

当常量池无法申请到内存报OutOfMemory异常
###直接内存
JDK1.4中新加入NIO(New Input/Output)类,引入了一种基于通道(Channel)与缓冲区(Buffer)的I/O方式,它可以使用Native函数库直接分配堆外内存,然后通过一个存储在Java堆中的DirectByteBuffer对象作为这块内存的引用记性操作.
作用:提高性能
直接内存不收Java堆大小的限制,但是受本机总内存限制.除了设置 -Xmx等参数信息,也要注意直接内存


##2.3 HotSpot虚拟机对象探秘
###2.3.1 对象的创建
####划分内存
虚拟机遇到一条new指令时,首先检查常量池中有没有这个类的符号引用,检查是否已被加载,解析,初始化过.如果没有,那必须先执行相应的类加载过程
通过检查之后,接下来虚拟机将为新生对象分配内存.

*指针碰撞(Bump the Pointer):* 如果内存是规整的,那么就将指针从已使用内存向空闲内存移动对象大小的距离

*空闲列表(Free List):* 如果内存是不规整的,已使用的内存和空闲的内存项目交错,虚拟机就必须尾货一个列表,记录那些内存块是可用的,分配内存块,更新列表

选择那种分配方式由垃圾收集器是否带有压缩整理功能决定
 
指针碰撞,代用Compact过程的收集器
- Serial
- ParNew

空闲列表,基于Mark-Sweep算法的收集器
- CMS

####保证线程安全
1. 对分配内存空间的动作进行同步处理---实际上虚拟机采用CAS配上失败重试的方式保证更新操作的原子性
1. 把内存分配动作按照线程划分在不同的空间之中进行,即每个线程在Java堆中预先分配一小块内存,称为本地线程分配缓冲(Thread Local Allocation Buffer).虚拟机是否使用TLAB,可以通过-XX:+/-UseTLAB 参数设定
###2.3.2 对象的内存布局
在HotSpot虚拟机中,对象在内存中存储的布局可以分为3块区域
1. 对象头(Header)
    1. 运行时数据,如哈希码,GC分代你年龄,锁状态标识,线程持有的锁,偏向线程Id,偏向时间戳
    1. 类型指针,虚拟机通过这个指针确定这个对象是哪个类的示例
1. 实例数据(Instance Data)
    1. 各种类型的字段内容
1. 对齐填充(Padding)
    1. 不是必然存在,由于HotSpot Vm的自动内存管理系统要求对象起始地址必须是8字节的整数倍,当对象实例数据部分没有对齐时,就需要通过对齐填充来补全
###2.3.3 对象的访问定位
主流方式

HotSpot采用直接指针方式

1. 使用句柄
    - 原理:划分出一块内存作为句柄池.reference中存储的就是对象的句柄地址,句柄中包含了对象实例数据与类型数据各自的具体地址信息
    - 优点:reference中存储的是稳定的句柄地址,在对象被移动时,只会改变句柄中的实例数据指针,而reference本身不需要修改
2. 直接指针
    - 原理:Java堆对象的布局中就必须考虑如何放置访问类型数据的相关信息,而reference中存储的直接就是对象地址
    - 优点:速度更快,少了一次通过句柄定位指针的开销
    
##2.4 实战:OutOfMemoryError异常
1. 通过代码验证Java虚拟机规范中描述的各个运行时区域存储的内容
2. 根据异常的信息快速判断是哪个区域的内存一处,知道什么的样的代码可能导致这些区域内存溢出,以及出现这些异常后如何处理


##2.4.1 Java堆溢出

复现过程,设置jvm启动参数来实现,本文使用Idea,在Run/Debug configuration中的VM options选项中填入如下参数
```
-Xms20m -Xmx20m -XX:+HeapDumpOnOutOfMemoryError
```
以上代码限制Java堆的大小为20MB,不可扩展(将堆的最小值 -Xms参数与最大值-Xmx参数设置为一样即可避免堆自动扩展),通过-XX:+HeapDumpOnOutOfMemoryError可以让虚拟机在出现内存溢出异常时Dump出当前的内存对转储快照以便时候进行分析

代码清单
```
import java.util.ArrayList;
import java.util.List;

/**
 * VM Args: -Xms20m -Xmx20m -XX:+HeapDumpOnOutOfMemoryError
 * @author jimersylee 
 */
public class HeapOOM {
    private static class OOMObject{

    }
    public static void main(String[] args){
        List<OOMObject> list=new ArrayList<>();
        while(true){
            list.add(new OOMObject());
        }
    }
}
```
运行结果
```
java.lang.OutOfMemoryError: Java heap space
Dumping heap to java_pid27497.hprof ...
Exception in thread "main" Heap dump file created [27789642 bytes in 0.164 secs]
java.lang.OutOfMemoryError: Java heap space
```


##2.4.2 虚拟机栈和本地方法栈溢出
对于HotSpot来说,虽然-Xoss参数(设置本地方法栈大小)存在,但实际上是无效的,栈容量实际由-Xss参数设定.关于虚拟机栈和本地方法栈,在Java虚拟机中描述了两种异常:
- 如果线程请求的栈深度大于虚拟机所允许的最大深度,将抛出StackOverflowError异常.
- 如果虚拟机在扩展栈时无法申请到足够的内存空间,则抛出OutOfMemoryError异常.

将实验范围限制于单线程操作,下面两种方法均无法让虚拟机产生OutOfMemoryError异常,尝试的结果都是StackOverflowError异常,测试代码2-4
- 使用-Xss参数减少栈内存容量.结果抛出StackOverflowError异常,异常出现时输出的堆栈深度相应缩小
- 定义了大量的本地变量,增大此方法帧中本地变量表的长度.结果:抛出StackOverflowError异常时堆栈深度相应缩小

代码清单2-4 
```
/**
 * 虚拟机和本地方法栈OOM测试(仅作为第一点测试程序)
 * VM Args:-Xss228k
 * @author Jimersy Lee
 */
public class JavaVMStackSOF {
    private int stackLength=1;
    public void stackLeak(){
        stackLength++;
        stackLeak();
    }
    public static void main(String[] args) throws Throwable{
        JavaVMStackSOF oom=new JavaVMStackSOF();
        try {
            oom.stackLeak();
        }catch (Throwable e){
            System.out.println("stack length:"+oom.stackLength);
            throw e;
        }
    }
}
```
运行结果
```
Exception in thread "main" java.lang.StackOverflowError
   stack length:1517
   	at JVM.chapter2.JavaVMStackSOF.stackLeak(JavaVMStackSOF.java:11)
   	at JVM.chapter2.JavaVMStackSOF.stackLeak(JavaVMStackSOF.java:12)
   	at JVM.chapter2.JavaVMStackSOF.stackLeak(JavaVMStackSOF.java:12)
   	at JVM.chapter2.JavaVMStackSOF.stackLeak(JavaVMStackSOF.java:12)
.....后续异常堆栈信息省略
```
实验结果表明:在单个线程下,无论是由于栈帧太大或是虚拟机栈容量太小,当内存无法分配的时候,虚拟机抛出都是StackOverflowError异常

如果测试时不限于单线程,通过不断地建立线程的方式到时可以产生内存溢出异常,如代码清单2-5所示.但是这样的内存溢出异常与栈空间足够大并不存在任何关联,或者准确地说,在这种情况下,为每个线程的栈分配的内存越大,反而越容易产生内存溢出异常.

代码清单2-5 创建线程导致内存溢出异常
```
/**
 * 创建线程导致内存溢出异常
 * VM Args: -Xss2M
 * @author Jimersy Lee
 * todo:没有等到抛出异常
 */
public class JavaVMStackOOM {
    private void dontStop(){
        while (true){

        }
    }

    public void stackLeakByThread(){
        int count=0;
        while(true){
            Thread thread=new Thread(new Runnable() {
                @Override
                public void run() {
                    dontStop();
                }
            });
            thread.start();
            System.out.println(count++);
        }
    }
    public static void main(String[] args){
        JavaVMStackOOM oom=new JavaVMStackOOM();
        oom.stackLeakByThread();
    }
}
```
##2.4.3 方法区和运行时常量池溢出
运行时常量池是方法区的一部分,因此这两个区域的溢出测试放在一起进行.

jdk1.6之前的版本,常量池分配在永久代中,可以通过-XX:PermSize和-XX:MaxPermSize限制方法区大小,从而间接限制其中常量池的容量

代码清单2-6 运行时常量池导致的内存溢出异常

```
import java.util.ArrayList;
import java.util.List;

/**
 * 运行时常量池导致的内存溢出异常,适用版本jdk1.6,1.8已经去除永久代
 * @author jimersylee
 * VM Args: -XX:PermSize=10m -XX:MaxPermSize=10m
 * String.intern()是一个Native方法,它的作用是:如果字符串常量池中已经包含一个等于此String对象的字符串,则返回代表池中这个字符串的String对象;否则,将此String对象包含的字符串添加到常量池中,并且返回此String对象的引用
 */
public class RuntimeConstantPoolOOM {
    public static void main(String[] args){
        //使用List保持着常量池引用,避免Full GC回收常量池行为
        List<String> list=new ArrayList<>();
        //10MB的PermSize在Integer范围内足够产生OOM了
        int i=0;
        while (true){
            list.add(String.valueOf(i++).intern());
            System.out.println(list.size());
        }

    }
}
```

关于字符串常量池的实现问题,引出一个有意思的影响
```
/**
 * String.intern()返回引用测试
 */
public class StringIntern {
    public static void main(String[] args) {
        String str1 = new StringBuilder("计算机").append("软件").toString();
        System.out.println(str1.intern()==str1);


        String str2=new StringBuilder("ja").append("va").toString();
        System.out.println(str2.intern()==str2);
    }
}
```

jdk1.6中,得到两个false;jdk1.7以后,得到true,和false;

jdk1.6中,intern()方法会把首次遇到的字符串实例复制到永久代中,返回的也是永久代中的这个字符串实例的引用,而StringBuilder创建的字符串实例在Java堆上,必然不是同一个引用,将返回false;

jdk1.7中,intern()实现不会在复制实例,只是在常量池中记录首次出现的实例引用,因此intern()返回的引用和由StringBuilder创建的那个字符创实例是同一个.

方法区用于存放Class的相关信息,如类名,访问修饰符,常量池,字段描述,方法描述

对于这些区域的测试,基本思路是运行时产生大量的类去填满方法区,直到溢出


##2.2.4 本机直接内存溢出

DirectMemory容量可以通过-XX:MaxDirectMemorySize指定,如果不指定,则默认与Java堆最大值(-Xmx指定)一样

由DirectMemory导致的内存溢出,一个明显的特征是在Heap Dump文件中不会看见明显的异常,如果读者发现OOM之后的Dump文件很小,而程序中又直接或间接使用了NIO,那就可以考虑检查一下是不是这方面的原因



##本章小结
通过本章的学习,我们明白了虚拟机中的内存是如何划分的,哪部分区域,什么样的代码和操作可能导致内存溢出异常.Java虽然有垃圾收集机制,但是内存溢出离我们并不遥远,本章只是讲解了各个区域出现异常的原因,下一章将详细讲解Java垃圾收集机制为了避免内存溢出异常的出现做出了哪些努力
