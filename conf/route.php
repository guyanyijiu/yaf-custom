<?php

return [
    /**
     * 默认路由 Yaf_Route_Static
     * 实例化 Yaf_Route_Static 是没有必要的，也没必要将它加入Yaf_Router的路由堆栈
     * 因为在Yaf_Router的路由堆栈中总是存在它的一个实例，并且总是在最后被调用
     */

    /**
     * 简单路由
     *
     * 对于如下请求: "http://domain.com/index.php?c=index&a=test
     * 能得到如下路由结果
     * array(
     *   'module'     => '默认模块',
     *   'controller' => 'index',
     *   'action'     => 'test',
     * )
     */
//    'simple' => new Yaf_Route_Simple("m", "c", "a"),

    /**
     * 指定supervar变量名
     *
     * 对于如下请求: "http://domain.com/index.php?r=/a/b/c
     * 能得到如下路由结果
     * array(
     *   'module'     => 'a',
     *   'controller' => 'b',
     *   'action'     => 'c',
     * )
     */
//    'supervar' => new Yaf_Route_Supervar("r"),

    /**
     * 对于请求request_uri为"/ap/foo/bar"
     * base_uri为"/ap"
     * 则最后参加路由的request_uri为"/foo/bar"
     * 然后, 通过对URL分段, 得到如下分节
     * foo, bar
     * 组合在一起以后, 得到路由结果foo_bar
     * 然后根据在构造Yaf_Route_Map的时候, 是否指明了控制器优先,
     * 如果没有, 则把结果当做是动作的路由结果
     * 否则, 则认为是控制器的路由结果
     * 默认的, 控制器优先为FALSE
     */

    /**
     * Rewrite 路由
     * 对于如下请求: "http://domain.com/user/2
     * 能得到如下路由结果
     * array(
     *   'module'     => 'Api',
     *   'controller' => 'User',
     *   'action'     => 'get',
     *   'params'     => [
     *      'id' => 2
     *   ]
     * )
     * 如果路由是 '/user/:id/*' 则后面成对出现的都将被做成变量名/值对放入参数里
     */
//    'rewrite' => new Yaf_Route_Rewrite(
//        'user/:id',
//        [
//            'module' => 'Api',
//            'controller' => 'User',
//            'action' => 'get',
//        ]
//    ),

    /**
     * Regex 路由
     * 对于如下请求: "http://domain.com/order/20170813123456
     * 能得到如下路由结果
     * array(
     *   'module'     => 'Api',
     *   'controller' => 'Order',
     *   'action'     => 'get',
     *   'params'     => [
     *      'id' => 20170813123456
     *   ]
     * )
     * 其中变量就是正则中反向引用的\1，第三个参数提供将数字映射为自定义的变量名
     *
     * 第三个参数必须提供，否则获取不到参数
     * 正则表达式必须是正规的正则语法
     *
     */
//    'regex' => new Yaf_Route_Regex(
//        "/order\/([0-9]+)/",
//        [
//            'module' => 'Api',
//            'controller' => 'Order',
//            'action' => 'get',
//        ],
//        [
//            1 => 'id'
//        ]
//    ),


];
