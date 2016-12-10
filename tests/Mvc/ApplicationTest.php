<?php
namespace Codeup\PhalconPsr\Mvc;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     */
    public function handle_withImplicitView()
    {
        // prepare
        $psrFactoryStub = $this->createMock(\Codeup\PhalconPsr\Http\Factory::class);
        $psrRequestMock = $this->createMock(\Psr\Http\Message\ServerRequestInterface::class);
        $psrResponseStub = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
        $psrFactoryStub->method('factorServerRequest')->willReturn($psrRequestMock);
        $psrFactoryStub->method('factorResponse')->willReturn($psrResponseStub);

        $psrResponseBodyStreamMock = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $psrResponseStub->method('getBody')->willReturn($psrResponseBodyStreamMock);
        $psrResponseStub->method('getHeaders')->willReturn([]);

        $phalconRequestDummy = $this->createMock(\Phalcon\Http\Request::class);
        $serviceContainerDummy = $this->createMock(\Interop\Container\ContainerInterface::class);

        $expectedController = uniqid('controller');
        $expectedAction = uniqid('action');
        $expectedRequestParam1 = 'test';
        $expectedRequestValue1 = uniqid('bla');
        $expectedParams = [$expectedRequestParam1 => $expectedRequestValue1];
        $routerMock = $this->createMock(\Phalcon\Mvc\RouterInterface::class);
        $routerMock->method('getParams')->willReturn($expectedParams);
        $routerMock->method('getControllerName')->willReturn($expectedController);
        $routerMock->method('getActionName')->willReturn($expectedAction);

        $expectedViewContent = uniqid('content');
        $viewMock = $this->createMock(\Phalcon\Mvc\ViewInterface::class);
        $viewMock->method('getContent')->willReturn($expectedViewContent);

        $dispatcherMock = $this->createMock(\Phalcon\Mvc\DispatcherInterface::class);
        $dispatcherMock->method('getReturnedValue')->willReturn($psrResponseStub);
        $dispatcherMock->method('getParams')->willReturn($expectedParams);
        $dispatcherMock->method('getControllerName')->willReturn($expectedController);
        $dispatcherMock->method('getActionName')->willReturn($expectedAction);

        $phalconDiStub = $this->createMock(\Phalcon\DiInterface::class);
        $phalconDiStub->method('get')->willReturnMap([
            ['router', null, $routerMock],
            ['view', null, $viewMock],
            ['dispatcher', null, $dispatcherMock],
        ]);

        /** @var \Codeup\PhalconPsr\Http\Factory $psrFactoryStub */
        /** @var \Phalcon\Http\Request $phalconRequestDummy */
        /** @var \Interop\Container\ContainerInterface $serviceContainerDummy */
        $classUnderTest = new Application($psrFactoryStub, $phalconRequestDummy, $serviceContainerDummy, $phalconDiStub);

        // test
        $psrRequestMock->expects($this->once())
            ->method('withAttribute')
            ->with($expectedRequestParam1, $expectedRequestValue1)
            ->willReturn($psrRequestMock);
        $routerMock->expects($this->once())->method('handle')->with(null);
        $dispatcherMock->expects($this->once())->method('setControllerName')->with($expectedController);
        $dispatcherMock->expects($this->once())->method('setActionName')->with($expectedAction);
        $viewMock->expects($this->once())->method('start');
        $viewMock->expects($this->once())->method('render')->with($expectedController, $expectedAction, $expectedParams);
        $viewMock->expects($this->once())->method('finish');
        $psrResponseBodyStreamMock->expects($this->once())->method('write')->with($expectedViewContent);
        $psrResponseBodyStreamMock->expects($this->once())->method('__toString')->willReturn('');
        $result = $classUnderTest->handle();

        // verify
        $this->assertTrue($result);
    }
}
