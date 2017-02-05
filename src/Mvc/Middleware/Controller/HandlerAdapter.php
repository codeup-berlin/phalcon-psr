<?php
namespace Codeup\PhalconPsr\Mvc\Middleware\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HandlerAdapter implements \Psr\Http\Middleware\DelegateInterface
{
    /**
     * @var string
     */
    private $applicationController;

    /**
     * @var string
     */
    private $actionName = '';

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $serviceContainer;

    /**
     * @param object $applicationController
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
        list($request, $response, $serviceContainer, $middlewareStack) = $arguments;

        if (!($request instanceof \Psr\Http\Message\ServerRequestInterface)) {
            throw new \InvalidArgumentException('PSR-7 request expected: ' . get_class($request));
        }
        if (!($response instanceof \Psr\Http\Message\ResponseInterface)) {
            throw new \InvalidArgumentException('PSR-7 response expected: ' . get_class($response));
        }
        if (!($serviceContainer instanceof \Interop\Container\ContainerInterface)) {
            throw new \InvalidArgumentException('Interop service container expected: ' . get_class($serviceContainer));
        }
        if ($middlewareStack !== null && !($middlewareStack instanceof \Codeup\InteropMvc\Middleware\RespondingArrayServerStack)) {
            throw new \InvalidArgumentException('PSR-15 middleware stack expected: ' . get_class($middlewareStack));
        }

        if (!method_exists($this->applicationController, $actionName)) {
            throw new \Phalcon\Mvc\Dispatcher\Exception(
                "Action '" . $actionName . "' was not found on handler '" . get_class($this->applicationController) . "'",
                \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND
            );
        }

        if ($middlewareStack) {
            $this->actionName = $actionName;
            $this->response = $response;
            $this->serviceContainer = $serviceContainer;
            $middlewareStack->setResponseSourceDelegate($this);
            return $middlewareStack->process($request);
        } else {
            return $this->applicationController->$actionName($request, $response, $serviceContainer);
        }
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function next(RequestInterface $request)
    {
        if (!($request instanceof ServerRequestInterface)) {
            throw new \InvalidArgumentException('ServerRequest expected.');
        }
        $actionName = $this->actionName;
        return $this->applicationController->$actionName($request, $this->response, $this->serviceContainer);
    }
}
