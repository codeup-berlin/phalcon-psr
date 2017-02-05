<?php
namespace Codeup\PhalconPsr\Mvc\Middleware\Controller;

class HandlerAdapter
{
    /**
     * @var string
     */
    private $phalconHandlerClass;

    /**
     * @param string $phalconHandlerClass
     */
    public function __construct(string $phalconHandlerClass)
    {
        $this->phalconHandlerClass = $phalconHandlerClass;
    }
}
