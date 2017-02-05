<?php
namespace Codeup\PhalconPsr\Mvc\Middleware\Controller;

use Interop\Container\ContainerInterface;

class HandlerAdapter extends \Codeup\InteropMvc\Controller\ServiceContainer
{
    /**
     * @var string
     */
    private $applicationController;

    /**
     * @param class $applicationController
     */
    public function __construct($applicationController)
    {
        if (!is_object($applicationController)) {
            throw new \InvalidArgumentException('Application controller instance expected.');
        }
        $this->applicationController = $applicationController;
    }

    /**
     * @param string $actionName
     * @param array $arguments
     */
    public function __call($actionName, $arguments)
    {
        list($request, $response, $serviceContainer) = $arguments;

        if (!($request instanceof \Psr\Http\Message\RequestInterface)) {
            throw new \InvalidArgumentException('PSR-7 request expected: ' . get_class($request));
        }
        if (!($response instanceof \Psr\Http\Message\ResponseInterface)) {
            throw new \InvalidArgumentException('PSR-7 response expected: ' . get_class($response));
        }
        if (!($serviceContainer instanceof \Interop\Container\ContainerInterface)) {
            throw new \InvalidArgumentException('Interop service container expected: ' . get_class($serviceContainer));
        }

        if (!method_exists($this->applicationController, $actionName)) {
            throw new \Phalcon\Mvc\Dispatcher\Exception(
                "Action '" . $actionName . "' was not found on handler '" . get_class($this->applicationController) . "'",
                \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND
            );
        }

        return $this->applicationController->$actionName($request, $response, $serviceContainer);
    }
}
