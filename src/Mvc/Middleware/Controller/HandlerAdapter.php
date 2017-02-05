<?php
namespace Codeup\PhalconPsr\Mvc\Middlewar\Controller;

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
