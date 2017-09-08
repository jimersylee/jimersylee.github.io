<!--
author: Jimersy Lee
head: 
date: 2014-11-14
title: Linux快捷键
tags: LINUX,SHORTCUT-KEY,CENTOS
images: 
category: linux
status: publish
summary: CentOS桌面版常用的快捷键,以及Linux常用命令
-->

#linux系统快捷键
```

文件和目录:

# cd /home                        进入 '/home' 目录

# cd ..                                返回上一级目录

# cd ../..                             返回上两级目录

# cd -                                 返回上次所在目录

# cp file1 file2                    将file1复制为file2

# cp -a dir1 dir2                 复制一个目录

# cp -a /tmp/dir1 .              复制一个目录到当前工作目录（.代表当前目录）

# ls                                    查看目录中的文件

# ls -a                                显示隐藏文件

# ls -l                                 显示详细信息

# ls -lrt                               按时间显示文件（l表示详细列表，r表示反向排序，t表示按时间排序）

# pwd                                显示工作路径

# mkdir dir1                       创建 'dir1' 目录

# mkdir dir1 dir2                同时创建两个目录

# mkdir -p /tmp/dir1/dir2    创建一个目录树

# mv dir1 dir2                    移动/重命名一个目录

# rm -f file1                        删除 'file1'

# rm -rf dir1                       删除 'dir1' 目录及其子目录内容

查看文件内容:

# cat file1                          从第一个字节开始正向查看文件的内容

# head -2 file1                   查看一个文件的前两行

# more file1                       查看一个长文件的内容

# tac file1                          从最后一行开始反向查看一个文件的内容

# tail -3 file1                      查看一个文件的最后三行

文本处理:

# grep str /tmp/test            在文件 '/tmp/test' 中查找 "str"

# grep ^str /tmp/test           在文件 '/tmp/test' 中查找以 "str" 开始的行

# grep [0-9] /tmp/test         查找 '/tmp/test' 文件中所有包含数字的行

# grep str -r /tmp/*             在目录 '/tmp' 及其子目录中查找 "str"

# diff file1 file2                   找出两个文件的不同处

# sdiff file1 file2                 以对比的方式显示两个文件的不同

查找:

# find / -name file1                                                 从 '/' 开始进入根文件系统查找文件和目录

# find / -user user1                                                查找属于用户 'user1' 的文件和目录

# find /home/user1 -name \*.bin                            在目录 '/ home/user1' 中查找以 '.bin' 结尾的文件

# find /usr/bin -type f -atime +100                         查找在过去100天内未被使用过的执行文件

# find /usr/bin -type f -mtime -10                           查找在10天内被创建或者修改过的文件

# locate \*.ps                                                         寻找以 '.ps' 结尾的文件，先运行 'updatedb' 命令

# find -name '*.[ch]' | xargs grep -E 'expr'              在当前目录及其子目录所有.c和.h文件中查找 'expr'

# find -type f -print0 | xargs -r0 grep -F 'expr'        在当前目录及其子目录的常规文件中查找 'expr'

# find -maxdepth 1 -type f | xargs grep -F 'expr'    在当前目录中查找 'expr'

压缩和解压:

# bzip2 file1                                   压缩 file1

# bunzip2 file1.bz2                        解压 file1.bz2

# gzip file1                                     压缩 file1

# gzip -9 file1                                最大程度压缩 file1

# gunzip file1.gz                            解压 file1.gz

# tar -cvf archive.tar file1               把file1打包成 archive.tar

（-c: 建立压缩档案；-v: 显示所有过程；-f: 使用档案名字，是必须的，是最后一个参数）

# tar -cvf archive.tar file1 dir1        把 file1，dir1 打包成 archive.tar

# tar -tf archive.tar                         显示一个包中的内容

# tar -xvf archive.tar                      释放一个包

# tar -xvf archive.tar -C /tmp         把压缩包释放到 /tmp目录下

# zip file1.zip file1                          创建一个zip格式的压缩包

# zip -r file1.zip file1 dir1               把文件和目录压缩成一个zip格式的压缩包

# unzip file1.zip                             解压一个zip格式的压缩包到当前目录

# unzip test.zip -d /tmp/                 解压一个zip格式的压缩包到 /tmp 目录

yum工具:

# yum -y install [package]              下载并安装一个rpm包

# yum localinstall [package.rpm]    安装一个rpm包，使用你自己的软件仓库解决所有依赖关系

# yum -y update                              更新当前系统中安装的所有rpm包

# yum update [package]                 更新一个rpm包

# yum remove [package]                删除一个rpm包

# yum list                                        列出当前系统中安装的所有包

# yum search [package]                 在rpm仓库中搜寻软件包

# yum clean [package]                   清除缓存目录（/var/cache/yum）下的软件包

# yum clean headers                      删除所有头文件

# yum clean all                                删除所有缓存的包和头文件

网络:

# ifconfig eth0                                                                       显示一个以太网卡的配置

# ifconfig eth0 192.168.1.1 netmask 255.255.255.0            配置网卡的IP地址

# ifdown eth0                                                                        禁用 'eth0' 网络设备

# ifup eth0                                                                            启用 'eth0' 网络设备

# iwconfig eth1                                                                     显示一个无线网卡的配置

# iwlist scan                                                                         显示无线网络

# ip addr show                                                                     显示网卡的IP地址

其他:

# su -                                 切换到root权限（与su有区别）

# shutdown -h now           关机

# shutdown -r now            重启

# top                                  罗列使用CPU资源最多的linux任务 （输入q退出）

# pstree                             以树状图显示程序

# man ping                        查看参考手册（例如ping 命令）

# passwd                          修改密码

# df -h                               显示磁盘的使用情况

# cal -3                             显示前一个月，当前月以及下一个月的月历

# cal 10 1988                   显示指定月，年的月历

# date --date '1970-01-01 UTC 1427888888 seconds'   把一相对于1970-01-01 00:00的秒数转换成时间

```

#桌面用快捷键

```
Ctrl + u            删除光标之前到行首的字符

Ctrl + k            删除光标之前到行尾的字符

Ctrl + c            取消当前行输入的命令，相当于Ctrl + Break

Ctrl + a            光标移动到行首（ahead of line），相当于通常的Home键

Ctrl + e            光标移动到行尾（end of line）

Ctrl + f             光标向前（forward）移动一个字符位置

Ctrl + b            光标往回（backward）移动一个字符位置

Ctrl + l             清屏，相当于执行clear命令

Ctrl + r            显示:号提示，根据用户输入查找相关历史命令（reverse-i-search）

Ctrl + w           删除从光标位置前到当前所处单词（word）的开头

Ctrl + t             交换光标位置前的两个字符

Ctrl + y            粘贴最后一次被删除的单词

Ctrl + Alt + d   显示桌面

Alt + b             光标往回（backward）移动到前一个单词

Alt + d             删除从光标位置到当前所处单词的末尾

Alt + F2           运行

Alt + F4           关闭当前窗口

Alt + F9           最小化当前窗口

Alt + F10         最大化当前窗口

Alt + Tab         切换窗口

Alt +按住左键  移动窗口（或在最下面的任务栏滚动鼠标滑轮）

[鼠标中间键] 粘贴突出显示的文本。使用鼠标左键来选择文本。把光标指向想粘贴文本的地方。点击鼠标中间键来粘贴。

[Tab] 命令行自动补全。使用 shell 提示时可使用这一方式。键入命令或文件名的前几个字符，然后按 [Tab] 键，它会自动补全命令或显示匹配键入字符的所有命令。

在桌面或文件管理器中直接按 / 就可以输入位置，打开文件管理器。

快速搜索：在 vi 或 Firefox 中直接按 / 即可进入搜索状态。

网站链接和图片可直接拖放到桌面或者目录，可以马上下载。

直接将文件管理器中的文件拖到终端中就可以在终端中得到完整的路径名。

在滚动条的空白处点击鼠标中键，屏幕即滚动到那个地方。
```