<!--
author: Jimersy Lee
head: 
date: 2017-09-22
title: <Go:build web application>的中文翻译版-第三章-连接数据
tags: GO
images: 
category: go
status: publish
summary: 
在上一章中,我们探索了在web应用中如何处理URLS和指引他们转到不同的页面.同时,我们通过net/http中的handle创建了动态的链接和动态的结果.在这一章中,我们将学习以下几个主题,连接数据库,使用GUID美化URLs,处理404页面
-->


在上一章中,我们探索了在web应用中如何处理URLS和指引他们转到不同的页面.同时,我们通过net/http中的handle创建了动态的链接和动态的结果.

通过实现和扩展Gorilla toolkit的mux路由,我们通过正则表达式扩展了路由的能力,使其给予我们的应用更大的灵活性.

其实这是一些最流行的web服务器的特性.比如说Apache和Nginx都在路由中提供了方法去解析正则表达式.

但是这仅仅是构成web应用的基石.为了更加深入,我们需要去看看如何引入数据.

前一章的例子中静态文件服务依赖于硬编码,这显然是过时的且难以控制的.

但是幸运的是,从90年代末期开始,网站变得动态化,数据库开始统治世界.虽然APIs,微服务和NoSQL在某些领域替代了这些架构,但是这个架构在当今的Web开发中还是万金油的角色.

所以,事不宜迟,让我们开始获取一些动态数据

在这一章中,我们将学习以下几个主题

- 连接数据库
- 使用GUID美化URLs
- 处理404页面

##连接一个数据库
为了连接数据库,Go的SQL接口提供了一个非常简单且可信赖的方式去连接拥有驱动的不同种类的数据库服务器.

目前,大部分流行的数据库都支持-MySQL,Postgres,SQLite,MSSQL和相当多的实现了Go提供的database/sql接口的数据库驱动.


>Note:在本书中,我们将会把MySQL和Postgres数据库使用最好的实践运用在多个例子上.安装MySQL和Postgres在Nix,Windows,OS X 系统的机器上是相当基础的工作
 
 
 
 
 
 ##创建MySQL数据库
 你可以选择设计任何你想要的应用,但是在这些例子中,我们将着手一个非常的简单的博客.
 
 我们的目标是尽可能地在数据库中创建一些博客的入口,最好可以使用GUID在数据库中直接地获取数据和展示,如果博客的入口不存在,将展示错误页面.
 
 为了实现这个需求,我们将创建一个包含了我们的页面的MySQL数据库.这个数据库将包含一个整数型的,自动递增的ID,一个全局唯一的标识,或者GUID,还有一些博客的初始数据.
 
 简单起见,我们创建一个叫存储标题的page_title字段,存储页面内容的page_content字段,还有一个使用Unix时间戳的字段page_date.
 
 
```
CREATE TABLE `pages` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`page_guid` varchar(256) NOT NULL DEFAULT '',
`page_title` varchar(256) DEFAULT NULL,
`page_content` mediumtext,
`page_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON
UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
UNIQUE KEY `page_guid` (`page_guid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf-8;

```

将page_guid标记为UNIQUE_KEY相当重要,如果我们允许出现重复的page_guid,在浏览某个网址的时候可能出现不准确的情况.
我们使用以下的语句插入一些blog数据
```
INSERT INTO `pages` (`id`, `page_guid`, `page_title`,
`page_content`, `page_date`) VALUES (NULL, 'hello-world', 'Hello,
World', 'I\'m so glad you found this page! It\'s been sitting
patiently on the Internet for some time, just waiting for a
visitor.', CURRENT_TIMESTAMP);
```
执行了上面的语句之后,我们就获得了初始数据
使用下面的代码来获得连接数据库的能力

```
package main
import (
"database/sql"
"fmt"
_ "github.com/go-sql-driver/mysql"
"log"
)
```

我们导入MySQL驱动包来完成需求.通常,这意味着驱动是另一个包的实现.你能注意到使用_ 符号来导入包.你可能已经熟悉这点,作为一种快速且脏的方式去忽略类的实例的返回值.比如说x,_ :=something() 允许你去忽略第二个返回值.这经常也被开发者用在计划去使用一个库,但是目前还没有用到的情况.通过这种方式准备包,它允许导入声明而不引起编译期报错.虽然这不是推荐的做法,但是在预载入方法中使用下划线_或者空白标识符,好处是这是很常见的做法也普遍被接受.
其实,这全部依赖于你怎样以及为何使用标识符.

```
const (
DBHost = "127.0.0.1"
DBPort = ":3306"
DBUser = "root"
DBPass = "password!"
DBDbase = "cms"
)
```

记得使用你自己的配置去替换以上值
```
var database *sql.db
```
为了避免大量重复代码,我们可以将数据库连接引用作为一个全局变量.为了清晰可见,我们将在代码开头定义.其实也没有什么事会阻止你把这个定义为一个常量,但是如果这样我们将会失去一定的灵活性,比如说添加多个数据库到单个应用中

```
type Page struct {
Title string
Content string
Date string
}
```

这个结构,跟我们的数据表结构非常匹配了,有标题,内容,时间.我们马上也将在本书中看到更好的数据结构设计.你需要确保你结构的字段是可以导出的或者公共的.任何小写的字段将不会被导出,因此也不能被模板化.我们将在后面讨论更多.
```
func main(){
	dbConn:=fmt.Sprintf("%s:%s@tcp(%s)/%s",DBUser,DBPass,DBHost,DBDbase)
	db,err:=sql.Open("mysql",dbConn)
	if err!=nil{
		log.Println("Couldn't connect")
		log.Println(err.Error())
	}
	log.Println("Connect successfully")
	database=db
}
```
正如我们之前提到的一样,这主要是脚手架.我们唯一想做的就是确保我们可以连接我们的数据库.如果出现一个错误,检查你的连接配置和输出的日志.
如果如我们期望的那样,我们使用上面的代码连接上数据库,我们就可以创建通用的路由代码来匹配请求中GUID,然后去数据库查询数据.

为了以上的目标,我们需要去重新实现Gorilla,创建单个路由,然后实现一个handler去匹配我们的数据库

看看下面的修改
```
package main
import (
"database/sql"
"fmt"
_ "github.com/go-sql-driver/mysql"
"github.com/gorilla/mux"
"log"
"net/http"
)
```

比较大的变化是我们在项目中引入了Gorilla和net/http 库.很显然我们需要这些来构建服务

```
const (
DBHost = "127.0.0.1"
DBPort = ":3306"
DBUser = "root"
DBPass = "password!"
DBDbase = "cms"
PORT = ":8080"
)
```
我们增加了PORT常量,用来绑定HTTP 服务器端口

```
var database *sql.DB


type Page struct{
	Title string
	Content string
	Date string
}


/**
数据库连接测试
 */
func main(){
	dbConn:=fmt.Sprintf("%s:%s@/%s",DBUser,DBPass,DBDbase)
	db,err:=sql.Open("mysql",dbConn)
	if err!=nil{
		log.Println("Couldn't connect")
		log.Println(err.Error())
	}
	log.Println("Connect successfully")
	database=db

	//设置路由
	routes:=mux.NewRouter()
	routes.HandleFunc("/page/{id:[0-9a-zA\\-]+",ServePage)
	http.Handle("/",routes)
	http.ListenAndServe(PORT,nil)
}

func ServePage(w http.ResponseWriter,r *http.Request){
	vars:=mux.Vars(r)
	pageID:=vars["id"]
	thisPage:=Page{}
	fmt.Println("pageID:"+pageID,"guid:"+pageGUID)
	err:=database.QueryRow("select page_title,page_content,page_date from pages where id=?",pageID).Scan(&thisPage.Title,&thisPage.Content,&thisPage.Date)
	if err!=nil{
		log.Println("Couldn't get page: +pageID")
		log.Println(err.Error())
	}

	html:=`<html><head><title>` + thisPage.Title +
`</title></head><body><h1>` + thisPage.Title + `</h1><div>` +
thisPage.Content + `</div></body></html>`
	fmt.Fprintln(w,html)
}

```

ServePage()是一个从mux.Vars中获取id,然后查询数据库的方法.最简单的查询数据库的方法就是使用使用预处理语句,比如Query,QueryRow或者Prepare.利用其中任何一个包含可注入的变量的声明语句,可以避免手工构建查询语句的风险.

Scan方法获取结果然后解析成数据结构.在这个例子中,我们解析page_title,page_content,page_date到Page结构中的Title,Content,Date

```
func main() {
dbConn := fmt.Sprintf("%s:%s@/%s", DBUser, DBPass, DBDbase)
fmt.Println(dbConn)
db, err := sql.Open("mysql", dbConn)
if err != nil {
log.Println("Couldn't connect to"+DBDbase)
log.Println(err.Error)
}
database = db
routes := mux.NewRouter()
routes.HandleFunc("/page/{id:[0-9]+}", ServePage)
http.Handle("/", routes)
http.ListenAndServe(PORT, nil)
}
```
看看我们这的正则表达式:只获取数字

还记得们谈论过使用内置的GUID?我们将马上会用到,现在我们看看访问localhost:8080/page/1的结果

```
Hello, World

I'm so glad you found this page! It's been sitting patiently on the Internet for some time, just waiting for a visitor.
```

在前面的例子中,我们可以看到在数据库中的博客内容.

##使用GUID来美化URLs
在本章的前几段我们讨论过使用GUID来作为所有请求的URL标识符.

我们需要去修改正则表达式和SQL语句
```
routes.HandleFunc("/page/{id:[0-9a-zA\\-]+}", ServePage)
```
修改为
```
routes.HandleFunc("/page/{guid:[0-9a-zA\\-]+}", ServePage)
```
修改相关方法和SQL
```
func ServePage(w http.ResponseWriter, r *http.Request) {
vars := mux.Vars(r)
pageGUID := vars["guid"]
thisPage := Page{}
fmt.Println(pageGUID)
err := database.QueryRow("SELECT page_title,page_content,page_date
FROM pages WHERE page_guid=?",
pageGUID).Scan(&thisPage.Title, &thisPage.Content, &thisPage.Date)
```
在完成之后,我们访问 localhost:8080/page/hello-world,可以获得之前访问localhost:8080/page/1同样的结果,但是修改后的url可读性更高,而且对搜索引擎更加友好


##处理404
我们之前的代码有个显而易见的问题就是它不处理无效的ID(或GUID)的请求.

真实的情况是,一个访问/page/999的请求,将返回空白页,控制台将会输出*Couldn't get page!* 

解决这个问题最简单的方法就是使用合适的错误.在上一章中,我们探索过定制的404页面,在这里我们可以实现其中一种,但是在一个请求不能找到时最简单的方法就是直接返回一个HTTP 状态码,允许浏览器去处理.

在我们之前的代码中,我们有一个错误处理器,仅仅是写日志,让我们把它变得更加丰富

```
err:=database.QueryRow("select page_title,page_content,page_date from pages where page_guid=?",pageGUID).Scan(&thisPage.Title,&thisPage.Content,&thisPage.Date)
	if err!=nil{
		http.Error(w,http.StatusText(404),http.StatusNotFound)
		log.Println("Couldn't get page: +pageID")
		log.Println(err.Error())
		return
	}
```

这样子的话当遇到错误页面的时候将会有一个友好的提示页面

```
http://127.0.0.1:8080/page/hello-world22


Not Found
```
