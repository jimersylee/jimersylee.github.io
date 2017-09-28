<!--
author: Jimersy Lee
head: 
date: 2016-05-30
title: <机器学习实战>学习笔记之SVM
tags: PYTHON,MACHINE-LEARNING
images: 
category: python
status: publish
summary: SVM(支持向量机)
-->

项目地址:[http://github.com/jimersylee/MachineLearningAction](http://github.com/jimersylee/MachineLearningAction)

#支持向量机
- 简单介绍支持向量机
- 利用SMO进行优化
- 利用核函数对数据进行空间转换
- 将SVM和其他分类器进行对比


##什么是支持向量机
支持向量机(Support Vector Machines,SVM),SVM是最好的现成的分类器,"现成"指的是分类器不加修改即可直接使用.SVM能够对训练集之外的数据点做出很好的分类决策.

SVM有很多实现,本章值关注其中最流行的一种实现.即序列最小优化(Sequential Minimal Optimization,SMO)算法 .在此之外,将介绍如何使用一种核函数(kernel)的方式将SVM扩展到更多数据集上.最后会回顾第一章手写识别的例子,并考察能否通过SVM提高识别的效果

##基于最大间隔分隔数据
- 优点:泛华错误率低,计算开销不大,结果易解释
- 缺点:对参数调节和核函数的选择敏感,原始分类器不加修改仅适用于处理二类问题.
- 使用数据类型:数值型和标称型数据


```

import random

from numpy import *


def loadDataSet(fileName):
    """
    载入测试数据
    :param fileName:
    :return:
    """
    dataMat = []
    labelMat = []
    fr = open(fileName)
    for line in fr.readlines():
        lineArr = line.strip().split('\t')
        dataMat.append([float(lineArr[0]), float(lineArr[1])])
        labelMat.append(float(lineArr[2]))
    return dataMat, labelMat


def selectJrand(i, m):
    """
    取得一个和i不一样的随机值j返回
    :param i:是第一个alpha的下表
    :param m: 所有alpha的数目
    :return:
    """
    j = i
    while j == i:
        j = int(random.uniform(0, m))
    return j


def clipAlpha(aj, H, L):
    if aj > H:
        aj = H
    if L > aj:
        aj = L
    return aj


def smoSimple(dataMatIn, classLabels, C, toler, maxIter):
    """
    smo简易算法
    :param dataMatIn:数据集
    :param classLabels: 类别标签
    :param C: 常数C
    :param toler: 容错率
    :param maxIter: 取消前最大迭代次数
    :return:
    """
    dataMatrix = mat(dataMatIn)
    labelMat = mat(classLabels).transpose()
    b = 0
    m, n = shape(dataMatrix)
    alphas = mat(zeros((m, 1)))
    iter = 0
    while iter < maxIter:
        alphaPairsChanged = 0
        for i in range(m):
            fXi = float(multiply(alphas, labelMat).T * (dataMatrix * dataMatrix[i, :].T)) + b
            Ei = fXi - float(labelMat[i])
            if ((labelMat[i] * Ei < -toler) and (alphas[i] < C)) or (
                        (labelMat[i] * Ei > toler) and (alphas[i] > 0)):  # 如果alpha可以更改进入优化过程
                j = selectJrand(i, m)  # 随机选择第二个alpha
                fXj = float(multiply(alphas, labelMat).T * (dataMatrix * dataMatrix[j, :].T)) + b
                Ej = fXj - float(labelMat[j])
                alphaIold = alphas[i].copy()
                alphaJold = alphas[j].copy()
                # 保证alpha在0和C之间开始
                if labelMat[i] != labelMat[j]:
                    L = max(0, alphas[j] - alphas[i])
                    H = min(C, C + alphas[j] - alphas[i])
                else:
                    L = max(0, alphas[j] + alphas[i] - C)
                    H = min(C, alphas[j] + alphas[i])
                # 保证alpha在0和C之间结束
                if L == H:
                    print "L==H"
                    continue
                eta = 2.0 * dataMatrix[i, :] * dataMatrix[j, :].T - dataMatrix[i, :] * dataMatrix[i, :].T - dataMatrix[
                                                                                                            j,
                                                                                                            :] * dataMatrix[
                                                                                                                 j, :].T
                if eta >= 0:
                    print "eta>=0"
                    continue
                alphas[j] -= labelMat[j] * (Ei - Ej) / eta
                alphas[j] = clipAlpha(alphas[j], H, L)
                if abs(alphas[j] - alphaIold) < 0.00001:
                    print "j not moving enough"
                    continue
                alphas[i] += labelMat[j] * labelMat[i] * (alphaJold - alphas[j])  # 对i进行修改,修改量和j相同,但是方向相反
                b1 = b - Ei - labelMat[i] * (alphas[i] - alphaIold) * \
                              dataMatrix[i, :] * dataMatrix[i, :].T - labelMat[j] * (alphas[j] - alphaJold) * \
                                                                      dataMatrix[i, :] * dataMatrix[j, :].T
                b2 = b - Ej - labelMat[i] * (alphas[i] - alphaIold) * \
                              dataMatrix[i, :] * dataMatrix[j, :].T - \
                     labelMat[j] * (alphas[j]) - alphaJold * \
                                                 dataMatrix[j, :] * dataMatrix[j, :].T
                if 0 < alphas[i] and (C > alphas[i]):
                    b = b1
                elif (0 < alphas[j]) and (C > alphas[j]):
                    b = b2
                else:
                    b = (b1 + b2) / 2
                alphaPairsChanged += 1
                print "iter: %d i:%d, pairs changed %d" % (iter, i, alphaPairsChanged)
        if alphaPairsChanged == 0:
            iter += 1
        else:
            iter = 0
        print "iteration number: %d" % iter
    return b, alphas


def smoTest():
    dataMat, labelMat = loadDataSet("testSet.txt")
    b, alphas = smoSimple(dataMat, labelMat, 0.6, 0.001, 40)



smoTest()
```