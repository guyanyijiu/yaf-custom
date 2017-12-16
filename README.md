YAF
=========

对 yaf 框架做了一些修改，使其更易用

+ 引入 [composer](https://getcomposer.org/) 管理依赖库和自动加载
+ 引入 [illuminate/container](https://github.com/illuminate/container) Ioc 容器，实现控制反转，依赖注入
+ 引入 [illuminate/database](https://github.com/illuminate/database) 查询构造器，方便数据库操作
+ 修改 配置文件加载机制，实现配置分文件，延迟加载
+ 修改 Action 加载机制，在 controller 中接管 yaf 对 action 的加载，之后完全由 container 去加载
+ 修改 框架结构，增加 Services 层，同时使用composer自动加载，可以任意增加自定义模块目录

> require php >= 7.0

## 目录结构

~~~
yaf
├─application           应用目录
│  ├─controllers        默认的控制器目录
│  ├─jobs               消息任务相关目录
│  ├─library        	 默认的扩展类库目录
│  │  ├─Model.php       Model 基类文件
│  │  ├─Controller.php  controller 基类文件
│  │  ├─Action.php      action 基类文件
│  │  ├─Service.php     services 基类文件
│  │  ├─Config.php      配置文件加载类
│  │  ├─DB.php          DB 快速操作类
│  │  ├─Http.php        curl 请求类
│  │  ├─Log.php         日志类
│  │  ├─Validate.php    数据校验类
│  │  ├─Request.php     请求类
│  │  └─Response.php    响应类
│  │
│  ├─models            model目录
│  ├─modules           模块目录
│  │  └─Api            业务模块目录
│  │    ├─actions      action 目录
│  │    └─controllers  controller 目录
│  ├─plugins           插件目录
│  ├─services          services 目录
│  │
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
│  └─.htaccess          apache使用
|
├─script                脚本文件目录
|
├─vendor                第三方类库目录（Composer依赖库）
|               
├─.gitignore            git 忽略文件
├─cli.php               命令行入口文件
├─composer.json         composer 文件
├─composer.lock         composer 文件
├─LICENSE               LICENSE
├─README.md             README
~~~

## 建议

+ 控制器层负责接收输入，调用 Service 层执行业务逻辑，响应输出
+ Service 层负责具体业务逻辑，从 Model 层获取数据
+ Model 层直接操作数据库，可以进行一些数据校验、字段限制、格式或类型转换之类数据的操作
+ 类和方法的功能要“专注”，不要使类的依赖关系变复杂
+ 变量尽量先初始化再使用，对数组操作要注意键不存在的情况，
+ 对于可能抛出异常的代码，要有防范
+ 代码要有注释，除非一眼就能看出来代码表达的意思

## 代码规范
## 示例
## 参考文档
+ [数据库](https://d.laravel-china.org/docs/5.5/queries)
+ [查询结果集合类](https://d.laravel-china.org/docs/5.5/eloquent-collections)
+ [数据验证](https://www.kancloud.cn/manual/thinkphp5/129319)
+ [HTTP请求](http://guzzle-cn.readthedocs.io/zh_CN/latest/quickstart.html)
+ [时间处理库Carbon](http://blog.csdn.net/for_happy123/article/details/52921089)
+ 辅助函数 `yaf/vendor/illuminate/support/helpers.php`
