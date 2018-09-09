<!--
author: Jimersy Lee
head: 
date: 2015-05-22
title: 设计模式之模板方法模式
tags: JAVA,PATTERN
images: 
category: java
status: publish
summary: 模板方法模式在一个方法中定义了一个算法的骨架,而将一些步骤延迟到子类中.模板方法使得子类可以在不改变算法结构的情况下,重新定义算法中的某些步骤

-->

# 模板方法模式


## 模板方法模式
>模板方法模式在一个方法中定义了一个算法的骨架,而将一些步骤延迟到子类中.模板方法使得子类可以在不改变算法结构的情况下,重新定义算法中的某些步骤

这个模式是用来创建一个算法的模板.什么是模板?模板就是一个方法.更具体地说,这个方法将算法定义为一组步骤,其中的任何步骤都可以是抽象的,由子类负责实现.这可以确保算法的结构保持不变,同时由子类提供部分实现.


# 快速搞定咖啡和茶的类

```
/**
 * 这是我们的咖啡类,用来煮咖啡
 */
public class Coffee {
    void prepareRecipe(){
        boilWater();
        brewCoffeeGrinds();
        pourInCup();
        addSugarAndMilk();
    }

    private void addSugarAndMilk() {
        System.out.println("addSugarAndMilk");
    }

    private void pourInCup() {
        System.out.println("pourInCup");
    }

    private void brewCoffeeGrinds() {
        System.out.println("brewCoffeeGrinds");
    }

    private void boilWater() {
        System.out.println("boilWater");
    }
}


public class Tea {
    void prepareRecipe(){
        boilWater();
        steepTeaBag();
        pourInCup();
        addLemon();
    }

    private void addLemon() {
        System.out.println("addLemon");
    }

    private void pourInCup() {
        System.out.println("pourInCup");
    }

    private void steepTeaBag() {
        System.out.println("steepTeaBag");
    }

    private void boilWater() {
        System.out.println("boilWater");
    }
}



```

请注意,boilWater()和pourCup()这两个方法完全一样,也就是说这里出现了重复的代码

在这里,茶和咖啡是如此的相似,可以提取基类


注意两份冲泡法都采用了相同的算法

抽象prepareRecipe()


```
void prepareRecipe(){
    boilwater();
    brew();
    pourInCup();
    addCondiments();
}

```



prepareRecipe()就是我们的模板方法
- 它是一个方法
- 它用作一个算法的模板,在这个例子中,算法是用来制作咖啡饮料的
- 在这个模板中,算法内的每一个步骤都被一个方法代表了
- 某些方法是由这个类(也就是超类)处理的
- 某些方法是由子类处理的
- 需要由子类提供的方法,必须在超类中声明为抽象


## 优劣对比
不好的茶和咖啡的实现
- Coffee和Tea主导一切;他们控制了算法
- Coffee和Tea之间存在重复的代码
- 对于算法所做的代码改变,需要打开子类修改许多地方
- 由于类的组织方式不具有弹性,所以加入新种类的咖啡因饮料需要做许多工作
- 算法的知识和它的实现会分散在很多类中

模板方法提供的酷炫咖啡因饮料
- 由CaffeineBeverage类主导一切,它拥有算法,而且保护这个算法
- 对子类来说,CaffeineBeverage类的存在,可以将代码的复用最大化
- 算法只存在于一个地方,所以容易修改
- 这个模板方法提供了一个框架,可以让其他的咖啡因饮料插进来.新的咖啡因饮料只需要实现自己的方法就可以了
- CaffeineBeverage类专注于算法本身,而子类提供完整的实现



## 要点
- "模板方法"定义了算法的步骤,把这些步骤的实现延迟到子类
- 模板方法模式为我们提供了一种代码复用的重要技巧
- 模板方法的抽象类可以定义具体方法,抽象方法和钩子
- 抽象方法由子类实现
- 钩子是一种方法,它在抽象类中不做事,或者只做默认的事情,子类可以选择要不要去覆盖它
- 为了防止子类改变模板方法中的算法,可以将模板方法声明为final
- 好莱坞原则告诉我们,将决策权房子高层模块中,以便决定如何以及何时调用底层模块
- 策略模式和模板方法模式都封装算法,一个用组合,一个用继承
- 工厂方法是模板方法的一个特殊版本





##项目地址
[java设计模式实现](https://github.com/jimersylee/DesignPattern)
如果觉得有点收获,记得在项目上点star哦!