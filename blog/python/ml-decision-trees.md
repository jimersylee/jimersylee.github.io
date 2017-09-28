<!--
author: Jimersy Lee
head: 
date: 2016-05-30
title: <机器学习实战>学习笔记之Decision Trees
tags: PYTHON,MACHINE-LEARNING
images: 
category: python
status: publish
summary: SVM(支持向量机)
-->

项目地址:[http://github.com/jimersylee/MachineLearningAction](http://github.com/jimersylee/MachineLearningAction)

###决策树(Decision Trees)
类似20个问题的游戏
参与游戏的一方在脑海里想某个事物,其他参与者向他提问题,只允许提20个问题,问题的答案只能用对或错回答,问问题的人通过推断分解,逐步缩小带猜测事物的范围.决策树的工作原理与20个问题类似,用户输入一系列数据,然后给出游戏的答案

决策树
优点:计算复杂度不高,输入结果易于理解,对中间值的缺失不敏感,可以处理不相关特征数据
缺点:可能产生过度匹配问题
适用数据类型:数值型与标称型

决策树的一般流程
1. 收集数据:可以适用任何方法
1. 准备数据:树构造算法只适用于标称型数据,因此数值型数据必须离散化
1. 分析数据:可以适用任何方法,构造书完成之后,我们应该检查图形是否符合预期
1. 训练算法:构造树的数据结构
1. 测试算法:适用经验树计算错误率
1. 适用算法:此步骤可以适用于任何监督学习算法,而适用决策树可以更好地理解数据的内在含义




```
# coding:utf-8
from math import log
import operator
import matplotlib.pyplot as plt


def calcShannonEnt(dataSet):
    """
    计算给定数据集的香农熵,香农熵代表数据的无序程度,香农熵越大,数据集越无序
    :param dataSet: 数据集
    :return:香农熵的值
    """
    numEntries = len(dataSet)  # 获得数据中实例的总数
    labelCounts = {}  # 声明一个字典
    # 为所有可能分类创建字典,它的键值是最后一列的数值
    for featVec in dataSet:
        currentLabel = featVec[-1]  # 目前的标签=数据集的每行的最后一个元素
        if currentLabel not in labelCounts.keys():  # 如果键值不存在,则扩展字典并将当前键值加入字典
            labelCounts[currentLabel] = 0  # 初始化为0

        labelCounts[currentLabel] = labelCounts[currentLabel] + 1  # 每个键值都记录了当前类别出现的次数,出现一次加1

    shannonEnt = 0.0
    for key in labelCounts:
        prob = float(labelCounts[key]) / numEntries  # 使用所有类标签的发生频率计算类别出现的概率
        shannonEnt = shannonEnt - prob * log(prob, 2)  # 使用这个概率计算香农熵 以2为底求对数
    return shannonEnt


def createDataSet():
    """
    创建简单鱼类鉴定数据集
    :return:dataSet(数据集),labels(标签)
    """
    _dataSet = [
        [1, 1, 'yes'],
        [1, 1, 'yes'],
        [1, 0, 'no'],
        [0, 1, 'no'],
        [0, 1, 'no']
    ]

    _labels = ['no surfacing', 'flippers']
    return _dataSet, _labels


myData, labels = createDataSet()
print myData
print calcShannonEnt(myData)

"""
result

[[1, 1, 'yes'], [1, 1, 'yes'], [1, 0, 'no'], [0, 1, 'no'], [0, 1, 'no']]
0.970950594455
"""

# 熵越高,则混合的数据也越多,我们可以在数据集中添加更多的分类,观察熵是如何变化的,这里我们增加第三个名为maybe的分类,测试熵的变化

myData[0][-1] = 'maybe'  # 第0行的最后一个元素置为maybe
print myData
print calcShannonEnt(myData)
"""
[[1, 1, 'maybe'], [1, 1, 'yes'], [1, 0, 'no'], [0, 1, 'no'], [0, 1, 'no']]
1.37095059445
"""
# 得到熵以后,我们就可以按照获取最大信息增益的方法划分数据集

# 另一个度量集合无需程度的方法是`基尼不纯度`(Gini impurity),简单地说就是从一个数据集中随机选取子项,度量其被错误分类到其他分组里的概率



"""
分类算法除了要测量信息熵,还需要划分数据集,度量花费数据集的熵,以便判断当前是否正确划分了数据集,我们将对每个特征划分数据集的结果计算一次信息熵,然后判断按照那个特征划分数据集是最好的划分方式
"""


def splitDataSet(dataSet, axis, value):
    """
    按照给定特征划分数据集
    :param dataSet: 待划分的数据集
    :param axis:划分数据集的特征位
    :param value:预设特征值
    :return:
    """
    retDataSet = []  # 创建新的list对象,因为函数中传递的是列表的引用,在函数内部对列表对象的修改,将会影响该列表对象的整个生存周期,因此需要创建一个新的列表
    for featVec in dataSet:
        if featVec[axis] == value:  # 如果axis位的值等于预设的特征值
            reducedFeatVec = featVec[:axis]  # 取行数据的前axis位
            reducedFeatVec.extend(featVec[axis + 1:])  # 加上axis位后的元素
            retDataSet.append(reducedFeatVec)  # 就是将除了axis特征位的元素抽取出来
    return retDataSet


# 测试splitDataSet
print "测试splitDataSet"
myData, labels = createDataSet()
print myData

newData = splitDataSet(myData, 0, 1)  # 将待测试数据的第0位为1的数据的其他特征和标签筛选出来
print newData


def chooseBestFeatureToSplit(dataSet):
    """
    选择最好的数据集划分方式
    :param dataSet: 数据集
    :return:
    """
    numFeatures = len(dataSet[0]) - 1
    # 计算整个数据的原始香农熵
    baseEntropy = calcShannonEnt(dataSet)
    bestInfoGain = 0.0
    bestFeature = -1
    # 遍历数据集中的所有特征
    for i in range(numFeatures):
        # 创建唯一的分类标签列表
        featList = [example[i] for example in dataSet]
        uniqueVals = set(featList)  # 将list转换为set,集合类型中每个值不同,从列表中创建集合是Python语言中得到列表中唯一元素值的最快方法
        newEntropy = 0.0
        # 计算每种划分方式的信息熵
        for value in uniqueVals:  # 遍历当前特征中的所以唯一属性值,对每个特征划分一次数据集
            subDataSet = splitDataSet(dataSet, i, value)
            prob = len(subDataSet) / float(len(dataSet))
            newEntropy += prob * calcShannonEnt(subDataSet)  # 并对所有唯一特征值得到的熵求和
        infoGian = baseEntropy - newEntropy  # 信息增益=熵的减少值或者数据无序度的减少
        # 计算最好的信息增益
        if infoGian > bestInfoGain:
            bestInfoGain = infoGian
            bestFeature = i
    return bestFeature  # 返回最好特征划分的索引值


# 获取最好特征划分索引值
print "获取最好特征划分索引值"
myData, labels = createDataSet()
print "dataSet:" + str(myData)
print "best index:" + str(chooseBestFeatureToSplit(myData))

"""
代码表示,第0个特征是最好的用户划分数据集的特征
"""

"""
递归构建决策树

"""


def majorityCnt(classList):
    """
    类似classify0部分的投票表决
    创建键值为classList中唯一值的数据字典,字典对象存储了classList中每个类标签出现的频率,最后利用operator操作键值排序字典,返回出现次数最多的分类名称
    :rtype: str
    :param classList: 分类名称的列表
    :return: 返回出现次数最多的分类名称
    """
    classCount = {}
    for vote in classList:
        if vote not in classCount.keys():  # 如果投票的类别不在classCount字典的key中,
            classCount[vote] = 0  # 则新建此key,value设置为0
        classCount[vote] += 1

    sortedClassCount = sorted(classCount.iteritems(), key=operator.itemgetter(1), reverse=True)
    return sortedClassCount[0][0]


def createTree(dataSet, labels):
    """
    创建树,使用了递归
    :param dataSet:数据集
    :param labels: 分类标签
    :return: 树结构
    """

    classList = [example[-1] for example in dataSet]  # 将数据集中的每一行的最后一个元素的值取出生成类别列表classList
    if classList.count(classList[0]) == len(classList):  # 递归停止条件1
        return classList[0]  # 类别完全相同时停止继续划分
    if len(dataSet[0]) == 1:  # 递归停止条件2
        return majorityCnt(classList)  # 遍历完所有特征时返回出现次数最多的
    bestFeat = chooseBestFeatureToSplit(dataSet)  # 选择最好的划分特征位
    bestFeatLabel = labels[bestFeat]  # 取出最好的标签
    myTree = {bestFeatLabel: {}}  # 初始化一个tree字典
    del (labels[bestFeat])  # 删除最好的标签,防止树的下一个节点还使用此标签
    featValues = [example[bestFeat] for example in dataSet]  # 创建最好的特征位的值所构建的list
    uniqueVals = set(featValues)  # 生成唯一的特征位的值构成的集合set
    for value in uniqueVals:
        subLabels = labels[:]  # 复制所有标签,且树不会和已经存在的标签搞混
        myTree[bestFeatLabel][value] = createTree(splitDataSet(dataSet, bestFeat, value), subLabels)  # 递归创建树
    return myTree


#  构建树测试
print "构建树测试"
myData, labels = createDataSet()
myTree = createTree(myData, labels)
print myTree





```


```
# coding:utf-8
import matplotlib.pyplot as plt  # 导入绘制图库

"""
绘制树图
"""
decisionNode = dict(boxstyle="sawtooth", fc="0.8")
leafNode = dict(boxstyle="round4", fc="0.8")
arrow_args = dict(arrowstyle="<-")


def getNumLeafs(myTree):
    """
    获取叶节点的数目
    :param myTree:树结构
    :return: 叶节点的数目
    """
    numLeafs = 0
    firstStr = myTree.keys()[0]
    secondDict = myTree[firstStr]
    for key in secondDict.keys():
        if type(secondDict[
                    key]).__name__ == 'dict':  # test to see if the nodes are dictonaires, if not they are leaf nodes
            numLeafs += getNumLeafs(secondDict[key])
        else:
            numLeafs += 1
    return numLeafs


def getTreeDepth(myTree):
    """
    获取树的层数(深度)
    :param myTree:
    :return: 树的层数(深度)
    """
    maxDepth = 0
    firstStr = myTree.keys()[0]
    secondDict = myTree[firstStr]
    for key in secondDict.keys():
        if type(secondDict[
                    key]).__name__ == 'dict':  # 判断此节点是否是字典,如果不是就是叶节点
            thisDepth = 1 + getTreeDepth(secondDict[key])  # 是字典,层数+1
        else:
            thisDepth = 1  # 叶节点,层数为1
        if thisDepth > maxDepth:
            maxDepth = thisDepth
    return maxDepth


def plotNode(nodeTxt, centerPt, parentPt, nodeType):
    """
    绘制节点
    :param nodeTxt:节点名称
    :param centerPt: 目前的位置
    :param parentPt: 父节点的位置
    :param nodeType: 节点类型
    :return:
    """
    createPlot.ax1.annotate(nodeTxt, xy=parentPt, xycoords='axes fraction',
                            xytext=centerPt, textcoords='axes fraction',
                            va="center", ha="center", bbox=nodeType, arrowprops=arrow_args)


def plotMidText(currentPt, parentPt, txtString):
    """
    绘制中间的文本
    :param currentPt:目前的位置 
    :param parentPt: 父节点的位置
    :param txtString: 想绘制的文本
    :return: 
    """
    xMid = (parentPt[0] - currentPt[0]) / 2.0 + currentPt[0]
    yMid = (parentPt[1] - currentPt[1]) / 2.0 + currentPt[1]
    createPlot.ax1.text(xMid, yMid, txtString, va="center", ha="center", rotation=30)


def plotTree(myTree, parentPt, nodeTxt):  # if the first key tells you what feat was split on
    numLeafs = getNumLeafs(myTree)  # this determines the x width of this tree
    depth = getTreeDepth(myTree)
    firstStr = myTree.keys()[0]  # the text label for this node should be this
    cntrPt = (plotTree.xOff + (1.0 + float(numLeafs)) / 2.0 / plotTree.totalW, plotTree.yOff)
    plotMidText(cntrPt, parentPt, nodeTxt)
    plotNode(firstStr, cntrPt, parentPt, decisionNode)
    secondDict = myTree[firstStr]
    plotTree.yOff = plotTree.yOff - 1.0 / plotTree.totalD
    for key in secondDict.keys():
        if type(secondDict[
                    key]).__name__ == 'dict':  # test to see if the nodes are dictonaires, if not they are leaf nodes
            plotTree(secondDict[key], cntrPt, str(key))  # recursion
        else:  # it's a leaf node print the leaf node
            plotTree.xOff = plotTree.xOff + 1.0 / plotTree.totalW
            plotNode(secondDict[key], (plotTree.xOff, plotTree.yOff), cntrPt, leafNode)
            plotMidText((plotTree.xOff, plotTree.yOff), cntrPt, str(key))
    plotTree.yOff = plotTree.yOff + 1.0 / plotTree.totalD


# if you do get a dictionary you know it's a tree, and the first element will be another dict

def createPlot(inTree):
    fig = plt.figure(1, facecolor='white')
    fig.clf()
    axprops = dict(xticks=[], yticks=[])
    createPlot.ax1 = plt.subplot(111, frameon=False, **axprops)  # no ticks
    # createPlot.ax1 = plt.subplot(111, frameon=False) #ticks for demo puropses
    plotTree.totalW = float(getNumLeafs(inTree))
    plotTree.totalD = float(getTreeDepth(inTree))
    plotTree.xOff = -0.5 / plotTree.totalW
    plotTree.yOff = 1.0
    plotTree(inTree, (0.5, 1.0), '')
    plt.show()


# def createPlot():
#    fig = plt.figure(1, facecolor='white')
#    fig.clf()
#    createPlot.ax1 = plt.subplot(111, frameon=False) #ticks for demo puropses 
#    plotNode('a decision node', (0.5, 0.1), (0.1, 0.5), decisionNode)
#    plotNode('a leaf node', (0.8, 0.1), (0.3, 0.8), leafNode)
#    plt.show()

def retrieveTree(i):
    listOfTrees = [{'no surfacing': {0: 'no', 1: {'flippers': {0: 'no', 1: 'yes'}}}},
                   {'no surfacing': {0: 'no', 1: {'flippers': {0: {'head': {0: 'no', 1: 'yes'}}, 1: 'no'}}}}
                   ]
    return listOfTrees[i]

    # createPlot(thisTree)


myTree = retrieveTree(0)
createPlot(myTree)

```