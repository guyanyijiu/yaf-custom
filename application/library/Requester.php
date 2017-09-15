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
class Requester {

    /**
     * 超时时间
     *
     * @var
     */
    protected $timeout;

    /**
     * base uri
     *
     * @var
     */
    protected $baseUri;

    /**
     * headers
     *
     * @var
     */
    protected $headers;

    /**
     * 缓存一个裸的 guzzle 实例
     *
     * @var
     */
    protected static $client;

    /**
     * 获取一个 guzzle 实例
     *
     * @Author   liuchao
     * @return \GuzzleHttp\Client
     */
    protected function guzzle(){
        $args = [];

        if(!is_null($this->baseUri)){
            $args['base_uri'] = $this->baseUri;
        }

        if(!is_null($this->headers)){
            $args['headers'] = $this->headers;
        }

        if(!is_null($this->timeout)){
            $args['timeout'] = $this->timeout;
        }

        if(! $args){
            if(!is_null(static::$client)){
                return static::$client;
            }
            $args['headers']['REQUEST_ID'] = \Yaf_Registry::get('_requestId');
            return static::$client = new Client($args);
        }

        $this->clear();
        return new Client($args);
    }

    /**
     * 链式操作结束，清空设置
     *
     * @Author   liuchao
     */
    protected function clear(){
        $this->headers = null;
        $this->baseUri = null;
        $this->timeout = null;
    }

    /**
     * 设置 base URI
     *
     * @Author   liuchao
     *
     * @param $uri
     *
     * @return $this
     */
    protected function baseUri($uri){
        $this->baseUri = $uri;
        return $this;
    }

    /**
     * 设置 timeout
     *
     * @Author   liuchao
     *
     * @param int $second
     *
     * @return $this
     */
    protected function timeout($second = 30){
        $this->timeout = $second;
        return $this;
    }

    /**
     * 设置 header
     *
     * @Author   liuchao
     *
     * @param array $headers
     *
     * @return $this
     */
    protected function headers(array $headers){
        $this->headers = $headers;
        return $this;
    }

    /**
     * 发起一个 GET 请求
     *
     * @Author   liuchao
     *
     * @param       $url
     * @param array $query 参数，关联数组形式
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function get($url, $query = []){
        return $this->guzzle()->request('GET', $url, ['query' => $query]);
    }

    /**
     * 发起一个 POST 请求
     *
     * @Author   liuchao
     *
     * @param       $url
     * @param array $query 参数，关联数组形式
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function post($url, $query = []){
        return $this->guzzle()->request('POST', $url, ['form_params' => $query]);
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
    protected function getAsync($url, $query = [], $callback = null){
        return $this->requestAsync('GET', $url, ['query' => $query], $callback);
    }

    /**
     * 发起一个 POST 请求
     *
     * @Author   liuchao
     *
     * @param       $url
     * @param array $query
     * @param null  $callback
     *
     * @return bool
     */
    protected function postAsync($url, $query = [], $callback = null){
        return $this->requestAsync('POST', $url, ['form_params' => $query], $callback);
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
    protected function requestAsync($method, $url, $query = [], $callback = null){
        if(func_num_args() == 3){
            $query = [];
            $callback = $query;
        }

        if(!is_callable($callback)){
            $callback = function() use ($callback){
                return $callback;
            };
        }

        $promise = $this->guzzle()->requestAsync($method, $url, $query);

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
    protected function concurrence(array $requests, $callback = null){
        $promises = $requests;
        $results = settle($promises)->wait();
        if(! is_callable($callback)){
            return true;
        }
        return $callback($results);
    }

    /**
     * 发起一组不定数量的异步并发的请求
     *
     * Requester::pool('POST', 'http://yaf.app/api/user/hello',
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
    protected function pool($method, $url, Closure $parameterCallback, Closure $successCallback, Closure $failCallback, $concurrency = 5){
        $method = strtoupper($method);

        if($method == 'GET'){
            $requests = function () use ($method, $url, $parameterCallback) {
                $query = $parameterCallback();
                foreach($query as $v){
                    yield new PsrRequest($method, $url . '?' . http_build_query($v));
                }
            };
        }elseif($method == 'POST'){
            $requests = function () use ($method, $url, $parameterCallback) {
                $query = $parameterCallback();
                foreach($query as $v){
                    yield new PsrRequest($method, $url, ['content-type' => 'application/x-www-form-urlencoded'], http_build_query($v));
                }
            };
        }else{
            return false;
        }

        $client = $this->guzzle();
        $pool = new Pool($client, $requests(), [
            'concurrency' => $concurrency,
            'fulfilled' => $successCallback,
            'rejected' => $failCallback
        ]);

        $promise = $pool->promise();

        $promise->wait();
    }

    /**
     * 代理普通方法调用
     *
     * @Author   liuchao
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters){
        return call_user_func_array([$this, $method], $parameters);
    }

    /**
     * 代理静态方法调用
     *
     * @Author   liuchao
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters){
        return call_user_func_array([(new static), $method], $parameters);
    }

}
