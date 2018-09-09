<!--
author: Jimersy Lee
head: 
date: 2015-03-14
title: 设计模式之面向接口
tags: JAVA,PATTERN
images: 
category: java
status: publish
summary: 不变的就是变化,驱动改变的因素很多.找出你的应用中需要改变代码的原因.1. 用户需要新的功能 2. 需要推出新的活动 3. 应用改版 4. 为了更好的性能
-->





#软件开发的一个不变真理
>不变的就是变化


驱动改变的因素很多.找出你的应用中需要改变代码的原因
1. 用户需要新的功能
1. 需要推出新的活动
1. 应用改版
1. 为了更好的性能


继承不能很好的解决问题,因为对象的行为在子类里不断地改变,并且让所有子类都有这些行为是不恰当的.
使用Fooable等接口,只用能实现的类才继承Fooable接口,但是java接口不具有实现代码,所以继承接口无法达到代码的复用.

引出一个**设计原则**
>找出应用中可能需要变化之处,把它独立出来,不要和那些不需要变化的代码混在一起


**设计原则**
>针对接口编程,而不是针对实现编程




#假设有很多鸭子,有真鸭,模型鸭,如何实现他们的行为呢?


##先定义个一个抽象的Duck类
```
/**
 * 鸭子的抽象类
 */
public abstract  class Duck{
    private String _headColor="yellow";//Duck对象必备的属性

    public String get_headColor() {
        return _headColor;
    }

    public void set_headColor(String _headColor) {
        this._headColor = _headColor;
    }

    public IFlyBehavior flyBehavior;//为行为接口类型声明引用变量,所有鸭子子类都继承他们

    public IFlyBehavior getFlyBehavior() {
        return flyBehavior;
    }

    public void setFlyBehavior(IFlyBehavior flyBehavior) {
        this.flyBehavior = flyBehavior;
    }

    public IQuackBehavior getQuackBehavior() {
        return quackBehavior;
    }

    public void setQuackBehavior(IQuackBehavior quackBehavior) {
        this.quackBehavior = quackBehavior;
    }

    public IQuackBehavior quackBehavior;//同上


    public Duck(){

    }

    public void performQuack(){
        quackBehavior.quack();//委托给行为类
    }

    public void performFly(){
        flyBehavior.fly();//委托给行为类
    }

    public abstract void display();

    /**
     * Duck必备的行为
     */
    public void swim(){
        System.out.println("All ducks float,even decoys!");
    }

}
```


##绿头鸭继承Duck
```
/**
 * 绿头鸭类
 */
public class MallardDuck extends Duck {
    public MallardDuck(){
        quackBehavior=new Quack();//绿头鸭使用Quack类处理叫,所以当performQuack()被调用时,叫的职责被委托给Quack
        flyBehavior=new FlyWithWings();//同理
    }

    public void display(){
        System.out.println("I'm a real Mallard duck");
    }
}

```

##模型鸭
```
/*
* 模型鸭
*/
public class ModelDuck extends Duck {
    public ModelDuck(){
        flyBehavior=new FlyNoWay();//一开始,模型鸭不会飞
        quackBehavior=new Quack();//一开始,模型鸭会呱呱叫
    }
    @Override
    public void display() {

    }
}

```

##鸭子的鸣叫行为接口类
```


/**
 * 叫行为接口类
 */
public interface IQuackBehavior{
    public void quack();
}
```

##鸭子的飞行行为接口类
```
/**
 * 飞行行为接口类
 */
public interface IFlyBehavior{
    public void fly();
}
```

##各种实现了飞行行为的实现类
```
/**
 * 这是飞行行为的实现,给真会飞的鸭子用
 */
public class FlyWithWings implements IFlyBehavior {
    @Override
    public void fly() {
        System.out.println("fly with wings");
    }
}

/**
 * 火箭动力的飞行行为
 */
public class FlyWithRocket implements IFlyBehavior {
    @Override
    public void fly() {
        System.out.println("I'm flying with a rocket!");
    }
}

/**
 * 这是飞行行为的实现,给不会飞的鸭子用
 */
public class FlyNoWay implements IFlyBehavior {
    @Override
    public void fly() {
        System.out.println("I can't fly!");
    }
}


```
##各种实现了鸣叫行为的实现类
```
/**
 * 叫的实现,给会呱呱叫的鸭子用
 */
public class Quack implements IQuackBehavior {
    @Override
    public void quack() {
        System.out.println("Quack,gua gua gua!");
    }
}

/**
 * 鸭子叫的沉默实现,给不会叫的鸭子用
 */
public class QuackMute implements IQuackBehavior {
    @Override
    public void quack() {
        System.out.println("<<Silence>>");
    }
}


```

##测试我们的鸭子们
```
public class Test {
    public static void main(String args[]){
        MallardDuck mallardDuck=new MallardDuck();
        mallardDuck.display();
        mallardDuck.performFly();
        mallardDuck.performQuack();


        //搞一只模型鸭
        ModelDuck md=new ModelDuck();
        md.performFly();//第一次调用飞行时,委托给FlyNoWay
        md.setFlyBehavior(new FlyWithRocket());//继承来的设置飞行模式的方法,给予火箭动力
        md.performFly();//现在能飞啦~

    }
}

//输出
Bobble gobble
I'm flying a short distance
gua!gua!gua!
I'm flying a long distance
Bobble gobble
I'm flying a short distance
I'm flying a short distance
I'm flying a short distance
I'm flying a short distance
I'm flying a short distance

```

##项目地址
[java设计模式实现](https://github.com/jimersylee/DesignPattern)
如果觉得有点收获,记得在项目上点star哦!