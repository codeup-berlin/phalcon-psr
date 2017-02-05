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
        $appControllerClass = parent::getHandlerClass();
        if ($this->wasForwarded()) {
            return $appControllerClass;
        } else {
            $appController = $this->getAppController($appControllerClass);
            $handlerClass = \Codeup\PhalconPsr\Mvc\Middleware\Controller\HandlerAdapter::class;
            /** @var \Phalcon\DiInterface $di */
            $di = $this->_dependencyInjector;
            $di->setShared(
                $handlerClass,
                new \Codeup\PhalconPsr\Mvc\Middleware\Controller\HandlerAdapter($appController)
            );
            return $handlerClass;
        }
    }

    /**
     * @param string $className
     * @throws \Phalcon\Mvc\Dispatcher\Exception
     */
    private function getAppController(string $className)
    {
        if (!$this->_dependencyInjector->has($className) && !class_exists($className)) {
            throw new \Phalcon\Mvc\Dispatcher\Exception(
                $className . " handler class cannot be loaded",
                \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND
            );
        }
        return $this->_dependencyInjector->getShared($className);
    }
}
