<!--
author: Jimersy Lee
head: 
date: 2015-04-08
title: 设计模式之观察者模式
tags: JAVA,PATTERN
images: 
category: 
status: publish
summary: 观察者模式(Observer),让你的对象知悉现况
-->

#观察者模式(Observer)
让你的对象知悉现况

使用自定义的Subject(主题)与Observer(观察者模式)


设计原则
>找出程序中会变化的方面,然后将其和固定不变的部分分离

在观察者模式中,会改变的事主题的状态,以及观察者的数目和类型.用这个模式,你可以改变依赖于主题状态的对象,却不改变主题.这就叫提前规划

>针对接口编程,不针对实现编程

主题与观察者都使用接口:观察者利用主题的接口向主题注册,二主题利用观察者接口通知观察者.这样可以让两者之前运作正常,同时具有松耦合的优点
>多用组合,少用继承

观察者模式利用"组合"将许多观察者组合进主题中.对象之前的这种关系不是通过继承产生的,而是在运行时利用组合的方式而产生的.



#自己实现观察者模式

##我们先定义主题接口
```

/**
 * 主题接口
 */
public interface Subject {
    public void registerObserver(Observer o);
    public void removeObserver(Observer o);
    public void notifyObservers();

}

```
##定义观察者接口
```
/**
 * 观察者接口
 */
public interface Observer {
    public void update(float temp,float humidity,float pressure);
}

```

##显示元素接口
```
/**
 * 显示元素接口
 */
public interface DisplayElement {
    public void display();
}

```


##编写公告板实现,实现了观察者接口与显示元素接口
```
/**
 * 公告板实现
 */
public class CurrentConditionDisplay implements Observer,DisplayElement {
    private float temperature;
    private float humidity;
    private Subject weatherData;

    /**
     * 构造器需要weatherData对象(也就是主题)作为注册之用
     * @param weatherData:天气对象
     */
    public CurrentConditionDisplay(Subject weatherData){
        this.weatherData=weatherData;
        weatherData.registerObserver(this);
    }

    /**
     * display()方法就只是把最近的问的和湿度显示出来
     */
    @Override
    public void display() {
        System.out.println("Current conditions:"+temperature+"F degree and "+humidity+"% humidity");
    }

    /**
     * 当update被调用时,我们把温度和湿度保存起来,然后调用display
     * @param temp
     * @param humidity
     * @param pressure
     */
    @Override
    public void update(float temp, float humidity, float pressure) {
            this.temperature=temp;
            this.humidity=humidity;
            display();
    }
}

```

##天气数据实现主题接口
```
import java.util.ArrayList;

/**
 * 天气数据类实现了Subject(主题)接口
 */
public class WeatherData implements Subject {
    private ArrayList<Observer> observers;
    private float temperature;
    private float humidity;
    private float pressure;


    public WeatherData(){
        observers=new ArrayList<>();
    }

    @Override
    public void registerObserver(Observer o) {
        observers.add(o);
    }

    @Override
    public void removeObserver(Observer o) {
        int i=observers.indexOf(o);
        if(i>=0){
            observers.remove(i);
        }
    }

    @Override
    public void notifyObservers() {
        for (Observer observer : observers) {
            observer.update(temperature, humidity, pressure);
        }
    }

    /**
     * 此方法会在气象值变化时被调用
     */
    public void measurementsChanged(){
        notifyObservers();
    }


    public void setMeasurements(float temperature,float humidity,float pressure){
        this.temperature=temperature;
        this.humidity=humidity;
        this.pressure=pressure;
        measurementsChanged();
    }
}

```


##来个测试吧
```
public class WeatherStation {
    public static void main(String[] args){
        WeatherData weatherData=new WeatherData();

        CurrentConditionDisplay currentConditionDisplay=new CurrentConditionDisplay(weatherData);
        weatherData.setMeasurements(80,65,30.4f);
    }
}


#输出
Current conditions:80.0F degree and 65.0% humidity
```


#使用java自带的Observer

##定义显示元素接口
```
package Observable;

public interface DisplayElement {
    public void display();
}

```
##实现观察者接口和显示元素接口
```
package Observable;

import java.util.Observable;
import java.util.Observer;

/**
 * 天气状况布告板
 * Created by jimersylee on 
 */
public class CurrentConditionDisplay implements Observer,DisplayElement {
    Observable observable;
    private float temperature;
    private float humidity;

    public CurrentConditionDisplay(Observable observable){
        this.observable=observable;
        observable.addObserver(this);
    }


    public void update(Observable obs,Object arg){
        if(obs instanceof WeatherData){
            WeatherData weatherData=(WeatherData)obs;
            this.temperature=weatherData.getTemperature();
            this.humidity=weatherData.getHumidity();
            display();
        }
    }

    public void display(){
        System.out.println("Current conditions:"+temperature+"F degrees and "+humidity+"% humidity");
    }



}

```

##实现观察者抽象类
```
package Observable;

import java.util.Observable;

/**
 * 使用java.util内置的观察者模式实现
 * Created by jimersylee 
 */
public class WeatherData extends Observable {
    private float temperature;
    private float humidity;
    private float pressure;


    public WeatherData(){

    }

    public void measurementsChanged(){
        setChanged();
        notifyObservers();
    }

    public float getTemperature(){
        return temperature;
    }

    public float getHumidity(){
        return humidity;
    }

    public float getPressure(){
        return pressure;
    }

    public void setMeasurements(float temperature,float humidity,float pressure){
        this.temperature=temperature;
        this.humidity=humidity;
        this.pressure=pressure;
        measurementsChanged();
    }

}

```

##写个测试吧

```
package Observable;

public class WeatherStation {
    public static void main(String[] args){
        WeatherData weatherData=new WeatherData();

        CurrentConditionDisplay currentConditionDisplay=new CurrentConditionDisplay(weatherData);

        weatherData.setMeasurements(80,30,33.2f);
    }
}

#输出
Current conditions:80.0F degrees and 30.0% humidity

```


##项目地址
[java设计模式实现](https://github.com/jimersylee/DesignPattern)
如果觉得有点收获,记得在项目上点star哦!