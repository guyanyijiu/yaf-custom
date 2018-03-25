YAF
=========

对 yaf 框架做了一些修改，使其更易用

+ 引入 [composer](https://getcomposer.org/) 管理依赖库和自动加载
+ 引入 [illuminate/container](https://github.com/illuminate/container) Ioc 容器，实现控制反转，依赖注入
+ 修改 配置文件加载机制，实现多配置文件，延迟加载
+ 修改 框架路由加载流程，YAF框架初始化和匹配路由，自定义方式执行路由
+ 修改 框架结构，使用composer自动加载，可以任意增加自定义模块目录
+ 增加 event 和 middleware 支持

> require php >= 7.0

## 目录结构

~~~
yaf
├─application           应用目录
│  ├─events             事件定义目录
│  ├─jobs               消息任务相关目录
│  ├─library            默认的扩展类库目录
│  ├─listeners          事件监听者目录
│  ├─middleware         中间件目录
│  ├─models             model目录
│  ├─modules            模块目录
│  │  └─Api             业务模块目录
│  │    └─actions       action 目录
│  ├─services           services 目录
│  ├─Application.php    自定义框架功能
│  └─Bootstrap.php      启动引导文件
│  └─helpers.php        辅助函数
|
├─conf                  配置文件目录
│  ├─application.ini    应用配置文件
|  ├─cache.ini          缓存配置文件
│  ├─database.ini       数据库配置文件
│  └─route.php          路由配置文件
|
├─public
│  ├─index.php          入口文件
│  └─.htaccess          apache使用
|
├─script                普通脚本文件目录
├─swoole                swoole脚本文件目录
|
├─vendor                第三方类库目录（Composer依赖库）            
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

## 参考文档
+ [数据库](https://d.laravel-china.org/docs/5.5/queries)
+ [查询结果集合类](https://d.laravel-china.org/docs/5.5/eloquent-collections)
+ [数据验证](https://www.kancloud.cn/manual/thinkphp5/129319)
+ [HTTP请求](http://guzzle-cn.readthedocs.io/zh_CN/latest/quickstart.html)
+ [时间处理库Carbon](http://blog.csdn.net/for_happy123/article/details/52921089)
