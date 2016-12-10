<?php
namespace Codeup\PhalconPsr\Http;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function factorServerRequest_guzzleGet()
    {
        // prepare
        $classUnderTest = New Factory();
        $requestMock = $this->createMock(\Phalcon\Http\Request::class);
        $requestMock->method('getHeaders')->willReturn([]);
        $requestMock->method('getRawBody')->willReturn('');
        $requestMock->method('isPost')->willReturn(false);
        $requestMock->expects($this->never())->method('getPost');

        // test
        /** @var \Phalcon\Http\Request $requestMock */
        $result = $classUnderTest->factorServerRequest($requestMock, 'guzzle');

        // verify
        $this->assertInstanceOf(\GuzzleHttp\Psr7\ServerRequest::class, $result);
    }

    /**
     * @test
     */
    public function factorServerRequest_guzzlePost()
    {
        // prepare
        $classUnderTest = New Factory();
        $requestMock = $this->createMock(\Phalcon\Http\Request::class);
        $requestMock->method('getHeaders')->willReturn([]);
        $requestMock->method('getRawBody')->willReturn('');
        $requestMock->method('isPost')->willReturn(true);
        $requestMock->expects($this->once())->method('getPost')->willReturn([uniqid('testBody')]);

        // test
        /** @var \Phalcon\Http\Request $requestMock */
        $result = $classUnderTest->factorServerRequest($requestMock, 'guzzle');

        // verify
        $this->assertInstanceOf(\GuzzleHttp\Psr7\ServerRequest::class, $result);
    }

    /**
     * @test
     */
    public function factorServerRequest_default()
    {
        // prepare
        $classUnderTest = New Factory();
        $requestStub = $this->createMock(\Phalcon\Http\Request::class);
        $requestStub->method('getHeaders')->willReturn([]);
        $requestStub->method('getRawBody')->willReturn('');

        // test
        /** @var \Phalcon\Http\Request $requestDummy */
        $result = $classUnderTest->factorServerRequest($requestStub);

        // verify
        $this->assertInstanceOf(\Psr\Http\Message\ServerRequestInterface::class, $result);
    }

    /**
     * @test
     * @expectedException \DomainException
     */
    public function factorServerRequest_unknown()
    {
        // prepare
        $classUnderTest = New Factory();
        $requestDummy = $this->createMock(\Phalcon\Http\Request::class);

        // test
        /** @var \Phalcon\Http\Request $requestDummy */
        $classUnderTest->factorServerRequest($requestDummy, uniqid('wtf?'));

        // verified by annotation
    }

    /**
     * @test
     */
    public function factorResponse_guzzle()
    {
        // prepare
        $classUnderTest = New Factory();

        // test
        $result = $classUnderTest->factorResponse('guzzle');

        // verify
        $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $result);
    }

    /**
     * @test
     */
    public function factorResponse_default()
    {
        // prepare
        $classUnderTest = New Factory();

        // test
        $result = $classUnderTest->factorResponse();

        // verify
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $result);
    }

    /**
     * @test
     * @expectedException \DomainException
     */
    public function factorResponse_unknown()
    {
        // prepare
        $classUnderTest = New Factory();

        // test
        $classUnderTest->factorResponse(uniqid('wtf?'));

        // verified by annotation
    }
}
