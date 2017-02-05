<?php
namespace Codeup\PhalconPsr\Mvc;

class Dispatcher extends \Phalcon\Mvc\Dispatcher
{
    /**
     * Throws an internal exception
     *
     * @param string $message
     * @param int $exceptionCode
     * @return bool
     * @throws \Phalcon\Mvc\Dispatcher\Exception
     */
    protected function _throwDispatchException($message, $exceptionCode = 0)
    {
        $exception = new \Phalcon\Mvc\Dispatcher\Exception($message, $exceptionCode);
        if ($this->_handleException($exception) === false) {
            return false;
        }
        throw $exception;
    }
}
