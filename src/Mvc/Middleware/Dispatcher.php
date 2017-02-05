<?php
namespace Codeup\PhalconPsr\Mvc\Middleware;

/**
 * Middleware support trait for \Phalcon\Dispatcher derived classes exclusively.
 */
trait Dispatcher
{
    /**
     * @param string $value
     * @return string
     */
    private function camelize(string $value): string
    {
        return implode('', array_map('ucfirst', explode('_', $value)));
    }

    /**
     * Possible class name that will be located to dispatch the request
     *
     * @return string
     */
    public function getHandlerClass()
    {
        /** @var \Phalcon\Dispatcher $this */
        $phalconHandlerClass = parent::getHandlerClass();
        if ($this->wasForwarded()) {
            return $phalconHandlerClass;
        } else {
            $handlerClass = \Codeup\PhalconPsr\Mvc\Middlewar\Controller\HandlerAdapter::class;
            /** @var \Phalcon\DiInterface $di */
            $di = $this->_dependencyInjector;
            $di->setShared(
                $handlerClass,
                new \Codeup\PhalconPsr\Mvc\Middlewar\Controller\HandlerAdapter(
                    $phalconHandlerClass
                )
            );
            return $handlerClass;
        }
    }
}
