<?php

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request as PsrRequest;
use function GuzzleHttp\Promise\settle;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * 基于 guzzle 的 http 请求类
 *
 * @Author   liuchao
 *
 * Class Requester
 */
class Http {

    /**
     * 缓存一个 guzzle 实例
     *
     * @var
     */
    protected static $client;

    /**
     * 请求的body类型
     *
     * @var array
     */
    protected static $allowBodyType = [
        'form'      => 'form_params',
        'multipart' => 'multipart',
        'string'    => 'body',
        'stream'    => 'body',
        'json'      => 'json',
    ];

    /**
     * 获取一个 guzzle 实例
     *
     * @param array $args
     *
     * @return Client
     *
     * @author  liuchao
     */
    public static function guzzle($args = []) {

        if ( !is_null(static::$client)) {
            return static::$client;
        }
        $args['headers']['Requestid'] = \Uniqid::getRequestId();

        return static::$client = new Client($args);

    }

    /**
     * 发起一个 GET 请求
     *
     * @param       $url
     * @param array $query
     * @param array $args
     *
     * @return mixed|ResponseInterface
     *
     * @author  liuchao
     */
    public static function get($url, $query = [], $args = []) {
        return static::guzzle($args)->request('GET', $url, ['query' => $query]);
    }

    /**
     * 发起一个 POST 请求
     *
     * @param        $url
     * @param array  $query
     * @param string $body
     * @param array  $args
     *
     * @return mixed|ResponseInterface
     *
     * @author  liuchao
     */
    public static function post($url, $query = [], $body = 'form', $args = []) {
        return static::guzzle($args)->request('POST', $url, [self::$allowBodyType[$body] => $query]);
    }

    /**
     * 发起一个异步的 GET 请求
     *
     * @Author   liuchao
     *
     * @param       $url
     * @param array $query
     * @param null  $callback 回调函数，会传递响应过去
     *
     * @return bool
     */
    public static function getAsync($url, $query = [], $callback = null) {
        return static::requestAsync('GET', $url, ['query' => $query], $callback);
    }

    /**
     * 发起一个异步的 POST 请求
     *
     * @param        $url
     * @param array  $query
     * @param null   $callback
     * @param string $body
     *
     * @return bool
     *
     * @author  liuchao
     */
    public static function postAsync($url, $query = [], $callback = null, $body = 'form') {
        return static::requestAsync('POST', $url, [self::$allowBodyType[$body] => $query], $callback);
    }

    /**
     * 发起一个异步请求
     *
     * @Author   liuchao
     *
     * @param       $method
     * @param       $url
     * @param array $query
     * @param null  $callback
     *
     * @return bool
     */
    public static function requestAsync($method, $url, $query = [], $callback = null) {
        if (func_num_args() == 3) {
            $query = [];
            $callback = $query;
        }

        if ( !is_callable($callback)) {
            $callback = function () use ($callback) {
                return $callback;
            };
        }

        $promise = static::guzzle()->requestAsync($method, $url, $query);

        $promise->then(
            function (ResponseInterface $response) use ($callback) {
                return $callback($response);
            },
            function (RequestException $e) use ($callback) {
                $response = (new Response())->withStatus(400, $e->getMessage());

                return $callback($response);
            }
        );

        return true;
    }

    /**
     * 发起一组异步并发的请求
     *
     * @Author   liuchao
     *
     * @param array    $requests 形如 ['user' => Requester::getAsync('http://domain.com/user')] 的关联数组
     * @param \Closure $callback 回调，会回传响应数组，形如 ['user' => Response]
     *
     * @return bool
     */
    protected function concurrence(array $requests, $callback = null) {
        $promises = $requests;
        $results = settle($promises)->wait();
        if ( !is_callable($callback)) {
            return true;
        }

        return $callback($results);
    }

    /**
     * 发起一组不定数量的异步并发的请求
     *
     * Http::pool('POST', 'http://yaf.app/api/user/hello',
     *     //提供不同的参数
     *     function(){
     *         for($i = 0; $i < 10; $i++){
     *             yield ['id' => $i];
     *         }
     *     },
     *     //成功回调
     *     function ($response, $index){
     *         var_dump($response->getBody()->getContents());
     *     },
     *     //失败回调
     *     function ($reason, $index){
     *         var_dump($reason);
     *     }
     * );
     *
     * @Author   liuchao
     *
     * @param              $method               请求方法
     * @param              $url                  URL
     * @param \Closure     $parameterCallback    参数闭包，返回迭代器对象
     * @param \Closure     $successCallback      成功回调
     * @param \Closure     $failCallback         失败回调
     * @param int          $concurrency          一次请求并发量
     *
     * @return bool
     */
    protected function pool($method, $url, Closure $parameterCallback, Closure $successCallback, Closure $failCallback, $concurrency = 5) {
        $method = strtoupper($method);

        if ($method == 'GET') {
            $requests = function () use ($method, $url, $parameterCallback) {
                $query = $parameterCallback();
                foreach ($query as $v) {
                    yield new PsrRequest($method, $url . '?' . http_build_query($v));
                }
            };
        } elseif ($method == 'POST') {
            $requests = function () use ($method, $url, $parameterCallback) {
                $query = $parameterCallback();
                foreach ($query as $v) {
                    yield new PsrRequest($method, $url, ['content-type' => 'application/x-www-form-urlencoded'], http_build_query($v));
                }
            };
        } else {
            return false;
        }

        $client = $this->guzzle();
        $pool = new Pool($client, $requests(), [
            'concurrency' => $concurrency,
            'fulfilled'   => $successCallback,
            'rejected'    => $failCallback,
        ]);

        $promise = $pool->promise();

        $promise->wait();
    }

}
