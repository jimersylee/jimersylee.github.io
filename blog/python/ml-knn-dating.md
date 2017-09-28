<!--
author: Jimersy Lee
head: 
date: 2016-05-04
title: <机器学习实战>学习笔记之KNN-约会数据识别
tags: PYTHON,MACHINE-LEARNING
images: 
category: python
status: publish
summary: 约会数据分类识别
-->

项目地址:[http://github.com/jimersylee/MachineLearningAction](http://github.com/jimersylee/MachineLearningAction)

```
# -*- coding: utf-8 -*-
"""
约会数据kNN
"""

from numpy import *
import matplotlib.pyplot as plt
import operator


def classify0(inX, dataSet, labels, k):
    dataSetSize = dataSet.shape[0]
    diffMat = tile(inX, (dataSetSize, 1)) - dataSet
    sqDiffMat = diffMat ** 2
    sqDistances = sqDiffMat.sum(axis=1)
    distances = sqDistances ** 0.5
    sortedDistIndicies = distances.argsort()
    classCount = {}
    for i in range(k):
        voteIlabel = labels[sortedDistIndicies[i]]
        classCount[voteIlabel] = classCount.get(voteIlabel, 0) + 1
    sortedClassCount = sorted(classCount.iteritems(), key=operator.itemgetter(1), reverse=True)
    return sortedClassCount[0][0]


def file2matrix(filename):
    fr = open(filename)
    numberOfLines = len(fr.readlines())  # get the number of lines in the file
    returnMat = zeros((numberOfLines, 3))  # prepare matrix to return
    classLabelVector = []  # prepare labels return
    fr = open(filename)
    index = 0
    for line in fr.readlines():
        line = line.strip()
        listFromLine = line.split('\t')
        returnMat[index, :] = listFromLine[0:3]
        classLabelVector.append(int(listFromLine[-1]))
        index += 1
    return returnMat, classLabelVector


def autoNorm(dataSet):
    """
    归一化数值
    因为计算距离的时候,差值最大的属性对计算结果的影响最大,如果对于此数据,我们认为权重一样,则需要将数据进行处理,将数值归一化
    将属性的取值范围处理为0到1或者-1到1,使用下面的公式
    newValue=(oldValue-min)/(max-min)
    :param dataSet:矩阵
    :return:normDataSet:归一化后的矩阵
            range:取值范围
            minVals:最小值
    """
    minVals = dataSet.min(0)  # 0代表第一列,取得第一列最小值
    maxVals = dataSet.max(0)  # 取得第一列最大值
    ranges = maxVals - minVals  # 可能的取值范围
    normDataSet = zeros(shape(dataSet))  # 创建新的返回矩阵
    m = dataSet.shape[0]
    normDataSet = dataSet - tile(minVals, (m, 1))
    normDataSet = normDataSet / tile(ranges, (m, 1))  # element wise divide
    return normDataSet, ranges, minVals


def datingClassTest():
    hoRatio = 0.50  # hold out 10%
    datingDataMat, datingLabels = file2matrix('datingData/datingTestSet2.txt')  # load data setfrom file
    normMat, ranges, minVals = autoNorm(datingDataMat)
    m = normMat.shape[0]
    numTestVecs = int(m * hoRatio)
    errorCount = 0.0
    for i in range(numTestVecs):
        classifierResult = classify0(normMat[i, :], normMat[numTestVecs:m, :], datingLabels[numTestVecs:m], 3)
        print "the classifier came back with: %d, the real answer is: %d" % (classifierResult, datingLabels[i])
        if (classifierResult != datingLabels[i]): errorCount += 1.0
    print "the total error rate is: %f" % (errorCount / float(numTestVecs))
    print errorCount


def showNormal():
    """
    最基本散点图
    没有样本类别标签的约会数据散点图.难以辨识途中的点究竟属于那个样本分类
    :return:
    """
    datingDataMat, datingLables = file2matrix('datingData/datingTestSet2.txt')
    fig = plt.figure()
    ax = fig.add_subplot(111)
    ax.scatter(datingDataMat[:, 1], datingDataMat[:, 2])
    plt.xlabel("Percentage of Time Spent Playing Video Games")
    plt.ylabel("Liters of Icc Cream Consumed Per Week")
    plt.show()


def showLable():
    """
    带有样本分类标签的约会数据散点图
    虽然能够比较容易区分数据点丛书类别,但依然很那根据这张图得出结论性信息
    :return:
    """
    datingDataMat, datingLables = file2matrix('datingData/datingTestSet2.txt')
    fig = plt.figure()
    ax = fig.add_subplot(111)
    ax.scatter(datingDataMat[:, 1], datingDataMat[:, 2], 15.0 * array(datingLables), 15.0 * array(datingLables))
    ax.axis([-2, 25, -0.2, 2.0])
    plt.xlabel("Percentage of Time Spent Playing Video Games")
    plt.ylabel("Liters of Icc Cream Consumed Per Week")
    plt.show()


def showClass():
    """
    显示不同的分类
    标识了三个不同的样本分类区域,具有不同爱好的人其类别区域也不同
    每年赢得的飞行常客里程数与玩游戏视频游戏所占百分比的约会数据散点图
    约会数据有三个分类标签,通过途中展示的两个特征更容易区分数据点从属的类别
    :return:
    """

    n = 1000  # number of points to create
    xcord1 = [];
    ycord1 = []
    xcord2 = [];
    ycord2 = []
    xcord3 = [];
    ycord3 = []
    markers = []
    colors = []
    fw = open('datingData/datingTestSet.txt', 'w')
    for i in range(n):
        [r0, r1] = random.standard_normal(2)
        myClass = random.uniform(0, 1)
        if (myClass <= 0.16):
            fFlyer = random.uniform(22000, 60000)
            tats = 3 + 1.6 * r1
            markers.append(20)
            colors.append(2.1)
            classLabel = 1  # 'didntLike'
            xcord1.append(fFlyer);
            ycord1.append(tats)
        elif ((myClass > 0.16) and (myClass <= 0.33)):
            fFlyer = 6000 * r0 + 70000
            tats = 10 + 3 * r1 + 2 * r0
            markers.append(20)
            colors.append(1.1)
            classLabel = 1  # 'didntLike'
            if (tats < 0): tats = 0
            if (fFlyer < 0): fFlyer = 0
            xcord1.append(fFlyer);
            ycord1.append(tats)
        elif ((myClass > 0.33) and (myClass <= 0.66)):
            fFlyer = 5000 * r0 + 10000
            tats = 3 + 2.8 * r1
            markers.append(30)
            colors.append(1.1)
            classLabel = 2  # 'smallDoses'
            if (tats < 0): tats = 0
            if (fFlyer < 0): fFlyer = 0
            xcord2.append(fFlyer);
            ycord2.append(tats)
        else:
            fFlyer = 10000 * r0 + 35000
            tats = 10 + 2.0 * r1
            markers.append(50)
            colors.append(0.1)
            classLabel = 3  # 'largeDoses'
            if (tats < 0): tats = 0
            if (fFlyer < 0): fFlyer = 0
            xcord3.append(fFlyer);
            ycord3.append(tats)

    fw.close()
    fig = plt.figure()
    ax = fig.add_subplot(111)
    # ax.scatter(xcord,ycord, c=colors, s=markers)
    type1 = ax.scatter(xcord1, ycord1, s=20, c='red')
    type2 = ax.scatter(xcord2, ycord2, s=30, c='green')
    type3 = ax.scatter(xcord3, ycord3, s=50, c='blue')
    ax.legend([type1, type2, type3], ["Did Not Like", "Liked in Small Doses", "Liked in Large Doses"], loc=2)
    ax.axis([-5000, 100000, -2, 25])
    plt.xlabel('Frequent Flyier Miles Earned Per Year')
    plt.ylabel('Percentage of Time Spent Playing Video Games')
    plt.show()


showNormal()
showLable()
showClass()
datingClassTest()

```