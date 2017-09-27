Yaf-custom
===============

Yaf 是一款高性能的PHP框架，本身非常简洁，开发过程中常常需要自己增加一些扩展类库，使其更易用。根据目前业务需求扩展如下：

 + 使用 [composer](https://getcomposer.org/) 管理类库和自动加载
 + 使用 [Dependency Injection Container](https://github.com/silexphp/Pimple) 管理类库依赖
 + 使用多配置文件, 支持 ini 和 php两种类型, 使用时自动加载
 + 使用基于 [monolog](https://github.com/Seldaek/monolog) 的日志类, 每次请求自动生成唯一ID, 方便跨项目请求日志跟踪
 + 使用基于 laravel 的 [Eloquent](https://d.laravel-china.org/docs/5.5/database) 修改而来的数据库操作类
 + Redis 操作类
 + 其它一些实用的类库


> 运行环境要求PHP7.0以上。


## 目录结构

初始的目录结构如下：

~~~
yaf-custom  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─controllers        默认的控制器目录
│  ├─library        	 默认的扩展类库目录
│  │  ├─Model.php       Model 基类文件
│  │  ├─Controller.php  controller 基类文件
│  │  ├─Action.php      action 基类文件
│  │  ├─Config.php      配置文件加载类
│  │  ├─DB.php          DB 快速操作类
│  │  ├─Http.php        curl 请求类
│  │  ├─Log.php         日志类
│  │  ├─Request.php     请求类
│  │  └─Response.php    响应类
│  │
│  ├─models            model目录
│  ├─modules           模块目录
│  ├─plugins           插件目录
│  └─Bootstrap.php     启动引导文件
|
├─conf                 配置文件目录
│  ├─application.ini   应用配置文件
|  ├─cache.ini         缓存配置文件
│  ├─database.ini      数据库配置文件
│  └─route.php         路由配置文件
│
├─helper                函数目录
│  ├─functions.php      功能函数文件
│  └─helpers.php        助手函数文件
|
├─public
│  ├─index.php          入口文件
│  └─.htaccess          用于apache的重写
|
├─script                脚本文件目录
|
├─vendor                第三方类库目录（Composer依赖库）
|               
├─.gitignore            git 忽略文件
├─cli.php               命令行入口文件
├─composer.json         composer 定义文件
├─LICENSE               LICENSE
├─README.md             README
~~~

> 整体目录结构遵循 [YAF](http://www.laruence.com/manual/index.html) 默认目录结构


## 使用简介

### 关键点
- index.php 入口文件，定义 启动时间常量 YAF_START、根目录常量 ROOT_PATH ，启动框架
- Bootstrap.php 框架引导文件，这里依次 初始化基础设置、生成唯一请求ID、引入composer、加载helper函数、加载容器、加载路由配置、加载 plugin
- 在加载容器的时候注册要用到的类库
- 在 plugin 做一些预处理，比如参数加解密校验，访问限制，权限验证等


### 容器

- 在 Bootstrap.php 中实例化容器

```php
	// 使用的是Pimple容器类，也是遵循标准的容器实现
	use Pimple\Container;
	use guyanyijiu\Database\DatabaseServiceProvider;
	
	// 实例化一个容器对象
	$container = new Container();
	
	//向容器中注册一个类，容器实现了ArrayAccess接口，可以用数组的语法操作，闭包内返回一个对象
	$container['config'] = function (){
	    return new Config();
	};
	
	//后续便可以这样用
	$configObject = $container['config']; // 容器内部执行闭包并返回 Config 对象
	
	// 还可以定义一个 “服务提供者” 类，在里面定义要注册到容器的 “服务”
	$container->register(new DatabaseServiceProvider());

```
- 在框架启动之后，任何地方都可以使用容器来获取对象，为了方便，提供助手函数如下

```php

	$configObject = container('config');
	
	$users = container('db')->table('user')->where('id', '<', 3)->get();
	
```

### 数据库操作

- 数据库操作类是基于 Laravel 的 Eloquent 修改而来，拥有它的查询构造器的所有功能，去掉了 ORM 模型功能，即 Model 基类也只是提供查询构造器的功能和自动维护修改时间和创建时间字段，并不会映射到数据表

```php
	//查询构造器, 具体参考 laravel 5.5 文档
	//可以通过容器获取 db 实例
	$db = container('db');
	$db->table()->where()->get();
	
	//也可以使用DB类
	DB::table()->where()->get(); // DB 类只是简单的代理，仍然是去调用 container('db') 的方法

```

