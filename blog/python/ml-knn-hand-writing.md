<!--
author: Jimersy Lee
head: 
date: 2016-05-04
title: <机器学习实战>学习笔记之KNN-手写数据识别
tags: PYTHON,MACHINE-LEARNING
images: 
category: python
status: publish
summary: 手写数据识别
-->

项目地址:[http://github.com/jimersylee/MachineLearningAction](http://github.com/jimersylee/MachineLearningAction)

```
# coding:utf8
from numpy import *  # 引入数学库
import operator  # 引入操作符库
from os import listdir  # 引入列出文件夹中的文件库


def createDataSet():
    """
    创造测试简单的测试数据
    :return: 返回组数据与标签数据
    """
    group = array([[1.0, 1.1], [1.0, 1.0], [0, 0], [0, 0.1]])
    labels = ['A', 'A', 'B', 'B']
    return group, labels


def img2vector(filename):
    """
    图像像素数据转为矩阵函数
    :param filename:文件名 
    :return: 
    """

    # 创建向量 1行.1024列,因为每个图片的宽度为32像素,高度为32像素,32*32=1024,一共1024个像素,
    # 所以初始化[1,1024]的向量
    returnVector = zeros((1, 1024))

    # 打开数据文件,读取每行内容
    file = open(filename)

    for i in range(32):  # 循环32次,代表32行
        # 读取每一行
        lineStr = file.readline()
        # 将每行前32字符转成int存入向量
        for j in range(32):
            returnVector[0, 32 * i + j] = int(lineStr[j])

    return returnVector


def classify0(inX, trainingDataSet, labels, k):
    """
    分类器实现函数
    :param inX: 用于分类的输入向量,测试向量
    :param trainingDataSet: 输入的训练样本集
    :param labels: 样本数据的类标签向量
    :param k: 用于选择最近的邻居的数据,如k=3,则选择[距离]最近的3个数,3个中出现最多的则归类到这个最多的数属于的类
    :return: 
    """
    # 获取样本数据数量
    dataSetSize = trainingDataSet.shape[0]

    # 矩阵运算,计算测试数据与每个样本数据对应数据的差值
    diffMat = tile(inX, (dataSetSize, 1)) - trainingDataSet
    # sqDistance 上一步骤结果平方和
    sqDiffMat = diffMat ** 2
    sqDistance = sqDiffMat.sum(axis=1)

    # 取平方根,得到距离向量
    distances = sqDistance ** 0.5

    # 按照距离从低到高排序
    sortedDistanceArr = distances.argsort()
    # 初始化类别变量,数据类型为dict字典
    classCount = {}

    # 依次取出最近的样本数据
    for i in range(k):
        # 记录该样本数据所属的类别
        voteLabel = labels[sortedDistanceArr[i]]
        classCount[voteLabel] = classCount.get(voteLabel, 0) + 1  # 如果出现此类别频次+1

    # 对类别出现的频次进行排序,从高到低
    sortedClassCount = sorted(classCount.iteritems(), key=operator.itemgetter(1), reverse=True)

    # 返回出现频次最高的类别
    return sortedClassCount[0][0]


def handwritingClassTest():
    """
    手写图片识别测试
    :return: 
    """
    # 手写样本数据的类标签列表
    handWritingLabels = []

    # 样本数据文件列表 listdir返回字符串列表,此处则是训练文件名的列表
    trainingFileList = listdir('digits/trainingDigits')
    m = len(trainingFileList)  # m等于文件数

    # 初始化样本数据矩阵(m*1024)
    trainingMat = zeros((m, 1024))
    # 依次读取所有样本数据到数据矩阵
    for i in range(m):  # 循环文件数次
        # 提取文件名中的数据  文件名格式 0_0.txt
        fileNameStr = trainingFileList[i]  # 0_0.txt
        fileStr = fileNameStr.split('.')[0]  # 0_0 文件名
        classNumStr = int(fileStr.split('_')[0])  # 0 类别名
        handWritingLabels.append(classNumStr)  # 将0这个类别加入类标签列表

        # 将样本数据存入矩阵
        trainingMat[i, :] = img2vector('digits/trainingDigits/%s' % fileNameStr)

    # 循环读取测试数据
    testFileList = listdir('digits/testDigits')

    # 初始化错误个数
    errorCount = 0.0
    mTest = len(testFileList)

    # 循环测试每个测试数据文件
    for i in range(mTest):
        # 提取文件中的名字
        fileNameStr = testFileList[i]
        fileStr = fileNameStr.split('.')[0]
        classNumStr = int(fileStr.split('_')[0])

        # 提取数据向量
        vectorUnderTest = img2vector('digits/testDigits/%s' % fileNameStr)

        # 对数据文件进行分类
        classifierResult = classify0(vectorUnderTest, trainingMat, handWritingLabels, 3)

        # 打印KNN算法分类结果和真实的分类
        print "the classifier came back with: %d, the real answer is: %d" % (classifierResult, classNumStr)

        # 判断KNN算法结果是否准确
        if (classifierResult != classNumStr):
            errorCount += 1.0

    # 打印错误率
    print "\n the total number of errors is: %d" % errorCount
    print "\n total error rate is: %f" % (errorCount / float(mTest))


# 执行算法测试
handwritingClassTest()

```