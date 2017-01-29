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

        /** @var \Phalcon\Mvc\DispatcherInterface $dispatcher */
        $dispatcher = $di->get('dispatcher');
        $dispatcher->setControllerName(
            $router->getControllerName()
        );
        $dispatcher->setActionName(
            $router->getActionName()
        );

        // apply phalcon router params as PSR request attributes
        $psrServerRequest = $this->psrFactory->factorServerRequest($this->phalconRequest);
        foreach ($router->getParams() as $k => $v) {
            $psrServerRequest = $psrServerRequest->withAttribute($k, $v);
        }

        $dispatcher->setParams([
            $psrServerRequest,
            $this->psrFactory->factorResponse(),
            $this->serviceContainer
        ]);

        if ($view) {
            $view->start();
        }

        if (!$di->has('response')) {
            $di->set(
                'response',
                function () {
                    return new \Phalcon\Http\Response();
                }
            );
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

        $response = $dispatcher->getReturnedValue();
        if (!($response instanceof \Psr\Http\Message\ResponseInterface)) {
            throw new \UnexpectedValueException('PSR-7 response expected.');
        }

        if ($view) {
            $response->getBody()->write(
                $view->getContent()
            );
        }

        $this->sendResponse($response);

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
}
