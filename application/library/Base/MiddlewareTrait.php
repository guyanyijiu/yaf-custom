<?php

namespace Base;

use RuntimeException;
use SplDoublyLinkedList;
use SplStack;
use UnexpectedValueException;

/**
 * 中间件
 *
 * Trait MiddlewareTrait
 *
 * @package Base
 */
trait MiddlewareTrait {

    /**
     * @var SplStack
     */
    protected $stack;

    /**
     * @var bool
     */
    protected $middlewareLock = false;

    /**
     * 添加中间件
     *
     * @param callable $callable
     *
     * @return $this
     *
     * @author  liuchao
     */
    protected function addMiddleware(callable $callable) {
        if ($this->middlewareLock) {
            throw new RuntimeException('Middleware can’t be added once the stack is dequeuing');
        }

        if (is_null($this->stack)) {
            $this->seedMiddlewareStack();
        }

        $next = $this->stack->top();
        $this->stack[] = function (
            $request,
            $response
        ) use (
            $callable,
            $next
        ) {
            $result = $callable($request, $response, $next);
            if ($result instanceof HttpResponse === false) {
                throw new UnexpectedValueException(
                    'Middleware must return instance of \Base\HttpResponse'
                );
            }

            return $result;
        };

        return $this;
    }

    /**
     * 初始化一个中间件栈
     *
     * @param callable|null $kernel
     *
     * @author  liuchao
     */
    protected function seedMiddlewareStack(callable $kernel = null) {
        if ( !is_null($this->stack)) {
            throw new RuntimeException('MiddlewareStack can only be seeded once.');
        }
        if ($kernel === null) {
            $kernel = $this;
        }
        $this->stack = new SplStack;
        $this->stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
        $this->stack[] = $kernel;
    }

    /**
     * 执行中间件
     *
     * @param $request
     * @param $response
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function callMiddlewareStack($request, $response) {
        if (is_null($this->stack)) {
            $this->seedMiddlewareStack();
        }

        $start = $this->stack->top();
        $this->middlewareLock = true;
        $response = $start($request, $response);
        $this->middlewareLock = false;

        return $response;
    }
}