<?php

/*
|--------------------------------------------------------------------------
| 自定义应用功能
|--------------------------------------------------------------------------
| 目前可以自定义绑定服务到容器、注册中间件、注册事件和事件订阅
|
*/

/**
 * 注册自定义服务
 */

// db
//$container->singleton('db', function ($container) {
//    return new \DB($container);
//});

// redis
//$container->singleton('redis', function ($container) {
//    $config = $container->make('config')->get('database.redis');
//    $driver = $config['client'];
//    unset($config['client']);
//
//    return new \Illuminate\Redis\RedisManager($driver, $config);
//});

// events
//$container->singleton('events', function ($container) {
//    return new \Illuminate\Events\Dispatcher($container);
//});

// 多语言支持
//$container->singleton('files', function () {
//    return new \Illuminate\Filesystem\Filesystem;
//});
//
//$container->instance('path.lang', ROOT_PATH . '/resources/lang');
//
//$container->singleton('translation.loader', function ($container) {
//    return new \Illuminate\Translation\FileLoader($container['files'], $container['path.lang']);
//});
//
//$container->singleton('translator', function ($container) {
//    $loader = $container['translation.loader'];
//
//    $locale = $container['config']['app.locale'];
//
//    $trans = new \Illuminate\Translation\Translator($loader, $locale);
//
//    $trans->setFallback($container['config']['app.fallback_locale']);
//
//    return $trans;
//});

/**
 * 注册中间件
 * 按照中间件注册的顺序，请求处理前逻辑是倒序执行，请求处理后逻辑是正序执行
 */
//$container->middleware(function (\Request $request, \Response $response, $next) {
//    // 请求处理前逻辑
//
//    $response = $next($request, $response);
//
//    // 请求处理后逻辑
//
//    return $response;
//});

//$container->middleware([
//    \Middleware\Example::class,
//]);


/**
 * 注册事件
 */
//$container->listen([
//    'Events\ExampleEvent' => [
//        'Listeners\ExampleListener',
//    ],
//]);
//
//$container->subscribe([
//    'Listeners\ExampleSubscriber',
//]);
