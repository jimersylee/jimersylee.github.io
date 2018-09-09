<!--
author: Jimersy Lee
head: 
date: 2015-07-09
title: PHP字符串常用函数学习
tags: PHP,STRING
images: 
category: php
status: publish
summary: 包括字符串的常用操作
-->



```
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<body>

</body>
</head>
</html>
<?php

echo '1 int crc32(string str),产生32位长的crc多项式,比如crc32("helllo")<br/>';
echo crc32 ("jkkajjjk\n");
echo '<br/>';

echo '2 string bin2hex(string str) , 把二进制转换为十六进制,比如bin2hex("helloworld")<br/>';
echo bin2hex("hello world");
echo '<br/>';

echo '3 string chop(string str),移除str后面多余的空白,返回新的字符串<br/>';
echo 'kkkj l ';
echo 'hahah';
echo chop("kkkj l ");
echo 'hahah';
echo '<br/>';

echo '4 string chr(int ascii),返回指定ascii码表示的字符,如chr(100)<br/>';
echo chr(100);
echo '<br/>';
echo '5 int ord(string str),返回str字符串第一个字符的ascii码,如ord("d"),<br/>';
echo ord("d");
echo "<br/>";
echo '6 string chunk_split(string str,int chunklen, string end),把字符串没隔一定数目就分割,如chunk_split("jasdjkasjdkasadas",5,"|"),就是把字符串每5个字符用|分割<br/>';
echo chunk_split("jasdjkasjdkasadas",5,"|");
echo '<br/>';
echo '7 string crypt(string str,string salt),单向加密,无解密函数~';
echo '<br/>';
echo crypt("hello world","kk");
echo '<br/>';
echo '
CRYPT_STD_DES - 基于标准 DES 算法的散列使用 "./0-9A-Za-z" 字符中的两个字符作为盐值。在盐值中使用非法的字符将导致 crypt() 失败。
CRYPT_EXT_DES - 扩展的基于 DES 算法的散列。其盐值为 9 个字符的字符串，由 1 个下划线后面跟着 4 字节循环次数和 4 字节盐值组成。它们被编码成可打印字符，每个字符 6 位，有效位最少的优先。0 到 63 被编码为 "./0-9A-Za-z"。在盐值中使用非法的字符将导致 crypt() 失败。
CRYPT_MD5 - MD5 散列使用一个以 $1$ 开始的 12 字符的字符串盐值。
CRYPT_BLOWFISH - Blowfish 算法使用如下盐值：“$2a$”，一个两位 cost 参数，“$” 以及 64 位由 “./0-9A-Za-z” 中的字符组合而成的字符串。在盐值中使用此范围之外的字符将导致 crypt() 返回一个空字符串。两位 cost 参数是循环次数以 2 为底的对数，它的范围是 04-31，超出这个范围将导致 crypt() 失败。
CRYPT_SHA256 - SHA-256 算法使用一个以 $5$ 开头的 16 字符字符串盐值进行散列。如果盐值字符串以 “rounds=<N>$” 开头，N 的数字值将被用来指定散列循环的执行次数，这点很像 Blowfish 算法的 cost 参数。默认的循环次数是 5000，最小是 1000，最大是 999,999,999。超出这个范围的 N 将会被转换为最接近的值。
CRYPT_SHA512 - SHA-512 算法使用一个以 $6$ 开头的 16 字符字符串盐值进行散列。如果盐值字符串以 “rounds=<N>$” 开头，N 的数字值将被用来指定散列循环的执行次数，这点很像 Blowfish 算法的 cost 参数。默认的循环次数是 5000，最小是 1000，最大是 999,999,999。超出这个范围的 N 将会被转换为最接近的值。

';

if (CRYPT_STD_DES == 1)
{
echo "Standard DES: ".crypt("hello world")."\n<br />";
}
else
{
echo "Standard DES not supported.\n<br />";
}

if (CRYPT_EXT_DES == 1)
{
echo "Extended DES: ".crypt("hello world")."\n<br />";
}
else
{
echo "Extended DES not supported.\n<br />";
}

if (CRYPT_MD5 == 1)
{
echo "MD5: ".crypt("hello world")."\n<br />";
}
else
{
echo "MD5 not supported.\n<br />";
}

if (CRYPT_BLOWFISH == 1)
{
echo "Blowfish: ".crypt("hello world");
}
else
{
echo "Blowfish DES not supported.";
}

echo '<br/>';

echo ' 8 array explode (string separator, string string [, int limit]),传回一个字符串的数组，以参数 separator为界线将参数 string切开，如果有设定参数 limit，则传回的数组最多将会包含 limit个元素，而最后一个元素将会包含 string全部剩余的部份。<br/>';
$pizza="haa kkkk kllom lljjijj iioo ";
$pieces=explode(" ",$pizza);
foreach($pieces as $val){
echo $val;

}
echo '<br/>';

echo '9 string implode (string glue, array pieces) 以参数glue将数组pieces的各个元素结合起来成字符串返回.与join(string glue,array pieces)相同用法<br/>';

echo implode (":", $pieces);
echo '<br/>';

echo '10 array split (string pattern, string string [, int limit]),以正则把字符串切开 '

?>
```