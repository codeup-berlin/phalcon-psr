<?php
namespace Codeup\PhalconPsr\Mvc;

class Application extends \Phalcon\Mvc\Application
{
    /**
     * @var \Codeup\PhalconPsr\Http\Factory
     */
    protected $psrFactory = null;

    /**
     * @var \Phalcon\Http\Request
     */
    protected $phalconRequest = null;

    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $serviceContainer = null;

    /**
     * @var null|\Psr\Http\Middleware\StackInterface
     */
    private $middlewareStack = null;

    /**
     * Application constructor.
     *
     * @param \Codeup\PhalconPsr\Http\Factory $psrFactory
     * @param \Phalcon\Http\Request $phalconRequest
     * @param \Interop\Container\ContainerInterface $serviceContainer
     * @param null|\Phalcon\DiInterface $dependencyInjector
     */
    public function __construct(
        \Codeup\PhalconPsr\Http\Factory $psrFactory,
        \Phalcon\Http\Request $phalconRequest,
        \Interop\Container\ContainerInterface $serviceContainer,
        \Phalcon\DiInterface $dependencyInjector = null
    ) {
        $this->psrFactory = $psrFactory;
        $this->phalconRequest = $phalconRequest;
        $this->serviceContainer = $serviceContainer;
        parent::__construct($dependencyInjector);
    }

    /**
     * Handles a request
     * @param string $uri
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function handle($uri = null)
    {
        /** @var \Phalcon\DiInterface $di */
        $di = $this->_dependencyInjector;

        /** @var \Phalcon\Mvc\RouterInterface $router */
        $router = $di->get('router');
        $router->handle($uri);

        if ($this->_implicitView) {
            /** @var \Phalcon\Mvc\ViewInterface $view */
            $view = $di->get('view');
        } else {
            $view = null;
        }

        /** @var \Codeup\PhalconPsr\Mvc\Dispatcher $dispatcher */
        // \Codeup\PhalconPsr\Mvc\Dispatcher is required to instantiate the middleware handler adapter
        // inbetween the actual application controller
        $dispatcher = $di->get('dispatcher');
        if (!($dispatcher instanceof \Codeup\PhalconPsr\Mvc\Dispatcher)) {
            throw new \InvalidArgumentException('\Codeup\PhalconPsr\Mvc\Dispatcher expected.');
        }
        $dispatcher->setControllerName($router->getControllerName());
        $dispatcher->setActionName($router->getActionName());

        // apply phalcon router params as PSR request attributes
        $psrServerRequest = $this->psrFactory->factorServerRequest($this->phalconRequest);
        foreach ($router->getParams() as $k => $v) {
            $psrServerRequest = $psrServerRequest->withAttribute($k, $v);
        }

        if ($di->has('response')) {
            $requestedResponse = $di->get('response');
            if (!($requestedResponse instanceof \Psr\Http\Message\ResponseInterface)) {
                throw new \InvalidArgumentException('PSR-7 response expected.');
            }
        } else {
            $requestedResponse = $this->psrFactory->factorResponse();
            $di->set('response', $requestedResponse);
        }
        $dispatcher->setParams([
            $psrServerRequest,
            $requestedResponse,
            $this->serviceContainer,
            $this->middlewareStack
        ]);

        if ($view) {
            $view->start();
        }
        try {
            $dispatcher->dispatch();
        } catch (\Phalcon\Mvc\Dispatcher\Exception $e) {
            header("HTTP/1.0 404 Not Found");
            return false;
        }

        if ($view) {
            $view->render(
                $dispatcher->getControllerName(),
                $dispatcher->getActionName(),
                $dispatcher->getParams()
            );
            $view->finish();
        }

        $resultedResponse = $dispatcher->getReturnedValue();
        if (!($resultedResponse instanceof \Psr\Http\Message\ResponseInterface)) {
            throw new \UnexpectedValueException('PSR-7 response expected.');
        }

        if ($view) {
            $resultedResponse->getBody()->write(
                $view->getContent()
            );
        }

        $this->sendResponse($resultedResponse);

        return true;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return void
     */
    protected function sendResponse(\Psr\Http\Message\ResponseInterface $response)
    {
        $httpHeader = 'HTTP/' . $response->getProtocolVersion() . ' '
            . $response->getStatusCode() . ' '
            . $response->getReasonPhrase();
        header($httpHeader);

        foreach ($response->getHeaders() as $name => $values) {
            header($name . ': ' . implode(', ', $values));
        }

        echo $response->getBody();
    }

    /**
     * @param \Psr\Http\Middleware\StackInterface $middlewareStack
     * @return void
     */
    public function setMiddlewareStack(\Psr\Http\Middleware\StackInterface $middlewareStack)
    {
        $this->middlewareStack = $middlewareStack;
    }
}
