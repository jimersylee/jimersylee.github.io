<!--
author: Jimersy Lee
head: 
date: 2015-04-20
title: 设计模式之工厂模式
tags: JAVA,PATTERN
images: 
category: java
status: publish
summary: 针对接口编程,可以隔离掉以后系统可能发生的一大堆改变
         为了让系统有弹性,我们希望一个类是抽象类或接口.但如果这样,这些类或接口就无法直接实例化
         根据类的类型,我们实例化正确的具体类,然后返回具体类的对象,这些具体类必须实现抽象类接口
         但是压力来自于增加更多的具体类类型
         把创建对象的代码从具体方法中抽离,把创建的过程搬到另一个对象中,这个对象只管如何创建对象.
         我们称这个对象为*工厂*,现在我们就来实现一个披萨工厂
-->

#工厂模式
针对接口编程,可以隔离掉以后系统可能发生的一大堆改变
为了让系统有弹性,我们希望一个类是抽象类或接口.但如果这样,这些类或接口就无法直接实例化
根据类的类型,我们实例化正确的具体类,然后返回具体类的对象,这些具体类必须实现抽象类接口
但是压力来自于增加更多的具体类类型
把创建对象的代码从具体方法中抽离,把创建的过程搬到另一个对象中,这个对象只管如何创建对象.
我们称这个对象为*工厂*,现在我们就来实现一个披萨工厂


##简单工厂模式
创建对象的过程在工厂类中
```
/**
 * 简单工厂
 * 披萨工厂类
 */
public class SimplePizzaFactory {
    /**
     * 首先,在这个工厂类定义一个createPizza()方法,所有客户使用这个方法来实例化对象
     * @param type:披萨类型
     * @return Pizza
     */
    public Pizza createPizza(String type){
        Pizza pizza=null;
        if(type.equals("cheese")){
            pizza=new CheesePizza();
        }else if(type.equals("pepperoni")){
            pizza=new Pepperoni();
        }

        return pizza;
    }
}
```



##工厂方法模式
工厂方法用来处理对象的创建,并将这样的行为封装在子类中.这样,客户程序中关于超类的代码就和子类对象创建代码解耦了.工厂方法可能需要参数,也可能不需要.
```
abstract Product factoryMethod(String type)
abstract:工厂方法是抽象的
Product:工厂方法必须返回一个产品.超类中定义的方法,通常使用工厂方法的返回值
factoryMethod:工厂方法将客户(也就是超类中的代码,列入orderPizza())和实际创建具体产品的代码分割开来
type:工厂方法可能需要/不需要参数来制定所要的产品
```










##项目地址
[java设计模式实现](https://github.com/jimersylee/DesignPattern)
如果觉得有点收获,记得在项目上点star哦!