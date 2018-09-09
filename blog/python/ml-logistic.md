<!--
author: Jimersy Lee
head: 
date: 2016-05-05
title: <机器学习实战>学习笔记之Logistic
tags: PYTHON,MACHINE-LEARNING
images: 
category: python
status: publish
summary: Logistic回归,从疝气病预测兵马的死亡率
-->

项目地址:[http://github.com/jimersylee/MachineLearningAction](http://github.com/jimersylee/MachineLearningAction)

#Logistic回归
##本章内容
- Sigmoid函数和Logistic回归分类器
- 最优化理论初步
- 梯度下降最优化算法
- 数据中的缺失项处理

##最优化算法
比如如何在最短时间内从A点到B点?如何投入最少的工作量获得最大的收益


##Logistic回归的一般过程
1. 收集数据:采用任意方法收集数据
2. 准备数据:由于需要进行距离计算,因此要求数据类型为数值型.另外,结构化数据格式则最佳
3. 分析数据:采用任意方法对数据进行分析
4. 训练算法:大部分时间将将用于测试,训练的目的是为了找到最佳的分类回归系数
5. 测试算法:一旦训练步骤完成,分类将会很快
6. 使用算法:首先,我们需要输入一些数据,并将其转换成对应的结构化数值;接着,基于训练好的回归系数就可以对这些数值进行简单的回归计算,判定他们属于那个类别了;在这之后,我们就可以在输出的类别上做一些其他分析工作


##基于Logistic回归和Sigmoid函数的分类
优点:计算代价不高,易于理解和实现
缺点:容易欠拟合,分类精度可能不高
适用数据类型:数值型和标称型数据

我们想要的函数,能够接受所有的输入然后预测出类别.
使用单位阶跃函数
Sigmoid函数
f(z)=1/(1+e^-z)


##训练算法:随机梯度上升
梯度上升算法在每次更新回归系数时都需要遍历整个数据集,当数据集增加时,计算复杂度就太高了.
改进方法是一次禁用一个样本点来更新回归系数,该方法成为随机梯度上升算法.



##报错,未解决
  weights = weights + alpha * error * dataMatIn[i]
ValueError: operands could not be broadcast together with shapes (3,) (0,) 




#示例:从疝气病预测兵马的死亡率
本节使用Logistic回归来预测患有疝气病的马的存货问题
样本数据包含368个样本和28个特征
*有30%的数据的值是缺失的*.下面将介绍如何处理数据集中的数据确实问题,然后再用Logistic回归和随机梯度上升算法来预测病马的生死

##准备数据
数据缺失是个麻烦的问题.如何解决
- 使用可用特征的均值来填补缺失值
- 使用特殊值来填补缺失值,如-1
- 忽略有缺失值的样本
- 使用相似样本的均值填补缺失值
- 使用另外的机器学习算法来预测缺失值

 
```

from numpy import *
import matplotlib.pyplot as plt

def loadDataSet():
    """
    载入测试数据
    :return:
    """
    dataMat = []
    labelMat = []
    fr = open('testSet.txt')
    for line in fr.readlines():
        lineArr = line.strip().split()
        dataMat.append([1.0, float(lineArr[0]), float(lineArr[1])])  # 为了计算方便,将X0设置为1.0
        labelMat.append(int(lineArr[2]))
    return dataMat, labelMat


def sigmoid(inX):
    """
    Sigmoid函数,单位阶跃函数
    :param inX:
    :return:
    """
    return 1.0 / (1 + exp(-inX))


def gradAscent(dataMatIn, classLabels):
    """
    梯度上升算法
    :param dataMatIn: 输入的数据矩阵,存放的100*3的矩阵
    :param classLabels: 输入的数据类别矩阵
    :return:返回训练好的迭代次数
    """
    # 转换为NumPy矩阵类型
    dataMatrix = mat(dataMatIn)
    labelMat = mat(classLabels).transpose()  # 初始为1*100的行向量,为了便于矩阵运算,使用transpose转置为列向量100*1

    m, n = shape(dataMatrix)  # 得到矩阵大小
    alpha = 0.001  # 目标移动的步长
    maxCycles = 500  # 最大迭代次数
    weights = ones((n, 1))

    for k in range(maxCycles):
        # 矩阵相乘,下面两行,计算真实类别与预测类别的差值,接下来就是按照该差值的方向调整回归系数
        h = sigmoid(dataMatrix * weights)  # 代表的不是一次乘积计算,事实上该运算包含了300次的乘积,变量h不是一个数,而是一个列向量,100
        error = (labelMat - h)
        weights = weights + alpha * dataMatrix.transpose() * error
    return weights


def stocGradAscent0(dataMatIn, classLabels):
    m, n = shape(dataMatIn)
    alpha = 0.01
    weights = ones(n)
    for i in range(m):
        h = sigmoid(sum(dataMatIn[i] * weights))  # h是向量
        error = classLabels[i] - h  # error是向量
        weights = weights + alpha * error * dataMatIn[i]
    return weights


def stocGradAscent1(dataMatIn, classLabels, numIter=150):
    """
    改进的随机梯度上升算法
    :param dataMatIn:
    :param classLabels:
    :param numIter:
    :return:
    """
    m, n = shape(dataMatIn)
    weights = ones(n)
    for j in range(numIter):
        dataIndex = range(m)
        for i in range(m):
            alpha = 4 / (1.0 + j + i) + 0.01  # alpha每次迭代时需要调整
            randIndex = int(random.uniform(0, len(dataIndex)))  # 随机选取更新
            h = sigmoid(sum(dataMatIn[randIndex] * weights))
            error = classLabels[randIndex] - h
            weights = weights + alpha * error * dataMatIn[randIndex]
            del (dataIndex[randIndex])
    return weights


def plotBestFit(weights):
    """
    画出数据集和Logistic回归最佳拟合直线的函数
    :param weights:系数
    :return:
    """
    weights = weights.getA()
    dataMat, labelMat = loadDataSet()
    dataArr = array(dataMat)
    n = shape(dataArr)[0]
    xcord1 = []
    ycord1 = []
    xcord2 = []
    ycord2 = []

    for i in range(n):
        if int(labelMat[i]) == 1:
            xcord1.append(dataArr[i, 1])
            ycord1.append(dataArr[i, 2])
        else:
            xcord2.append(dataArr[i, 1])
            ycord2.append(dataArr[i, 2])
    fig = plt.figure()
    ax = fig.add_subplot(111)
    ax.scatter(xcord1, ycord1, s=30, c='red', marker='s')
    ax.scatter(xcord2, ycord2, s=30, c="green")
    x = arange(-3.0, 3.0, 0.1)
    y = (-weights[0] - weights[1] * x) / weights[2]
    ax.plot(x, y)
    plt.xlabel("X1")
    plt.ylabel("X2")
    plt.show()


def classifyVector(inX, weights):
    prob = sigmoid(sum(inX * weights))
    if prob > 0.5:
        return 1.0
    else:
        return 0.0


def colicTest():
    """
    疝气病马死亡分类测试
    :return:
    """
    frTrain = open('horseColicTraining.txt')
    frTest = open('horseColicTest.txt')
    trainingSet = []
    trainingLabels = []
    for line in frTrain.readlines():
        currLine = line.strip().split('\t')
        lineArr = []
        for i in range(21):
            lineArr.append(float(currLine[i]))
        trainingSet.append(lineArr)
        trainingLabels.append(float(currLine[i]))
    trainWeights = stocGradAscent1(array(trainingSet), trainingLabels, 500)
    errorCount = 0
    numTestVec = 0.0
    for line in frTest.readlines():
        numTestVec += 1.0
        currLine = line.strip().split('\t')
        lineArr = []
        for i in range(21):
            lineArr.append(float(currLine[i]))
        if int(classifyVector(array(lineArr), trainWeights)) != int(currLine[21]):
            errorCount += 1
    errorRate = (float(errorCount / numTestVec))
    print "the error rate of this test is: %f" % errorRate
    return errorRate


def multiTest():
    numTests = 10
    errorSum = 0.0
    for k in range(numTests):
        errorSum += colicTest()
    print "after %d iterations the average error rate is: %f" % (numTests, errorSum / float(numTests))


def testCal():
    dataArr, labelMat = loadDataSet()
    weights = gradAscent(dataArr, labelMat)
    print weights
    """
    得到一组回归系数,它确定了不同类别数据之间的分割线
    [[ 4.12414349]
     [ 0.48007329]
     [-0.6168482 ]]
    """


def testGradAscent():
    """
    测试梯度上升算法,画图
    :return:
    """
    dataArr, labelMat = loadDataSet()
    weights = gradAscent(dataArr, labelMat)
    plotBestFit(weights)


def testStocGradAscent0():
    """
    测试随机梯度上升算法,画图
    :return:
    """
    dataArr, labelMat = loadDataSet()
    weights = stocGradAscent0(dataArr, labelMat)
    plotBestFit(weights)


def testStocGradAscent1():
    """
    测试随机梯度上升算法,画图
    :return:
    """
    dataArr, labelMat = loadDataSet()
    weights = stocGradAscent1(dataArr, labelMat)
    plotBestFit(weights)


# testCal()

#testGradAscent()

# testStocGradAscent0()
multiTest()
```