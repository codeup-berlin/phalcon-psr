<?php
namespace Codeup\PhalconPsr\Http\Message;

class ServerRequestReadAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string[]
     */
    private $expectedRequestHeaders;

    /**
     * @var string
     */
    private $expectedRequestMethod = 'POST';

    /**
     * @var string
     */
    private $expecetdRequestUri;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $phalconRequestMock;

    /**
     * @var ServerRequestReadAdapter
     */
    private $classUnderTest;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->expecetdRequestUri = uniqid('/some/uri/');
        $this->expectedRequestHeaders = [
            'X-Some-Known-Header' => [
                uniqid('someHeaderValue'),
            ],
            'X-Some-Known-Multi-Value-Header' => [
                uniqid('someHeaderValue'),
                uniqid('someOtherHeaderValue'),
            ],
        ];

        $_SERVER['REQUEST_URI'] = $this->expecetdRequestUri;
        $_SERVER['REQUEST_METHOD'] = $this->expectedRequestMethod;
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/2.0';
        $_SERVER['SERVER_NAME'] = 'www.example.com';
        $_SERVER['HTTP_X_SOME_KNOWN_HEADER'] = implode(', ', $this->expectedRequestHeaders['X-Some-Known-Header']);
        $_SERVER['HTTP_X_SOME_KNOWN_MULTI_VALUE_HEADER'] = implode(', ', $this->expectedRequestHeaders['X-Some-Known-Multi-Value-Header']);

        $this->phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        $this->phalconRequestMock->method('getHeaders')->willReturn($this->expectedRequestHeaders);
        $this->phalconRequestMock->method('getScheme')->willReturn('https');

        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $phalconRequestMock = $this->phalconRequestMock;
        $this->classUnderTest = new ServerRequestReadAdapter($phalconRequestMock);
    }

    /**
     * @test
     */
    public function getProtocolVersion()
    {
        $this->assertSame('2.0', $this->classUnderTest->getProtocolVersion());
    }

    /**
     * @test
     */
    public function getHeaders()
    {
        $this->assertSame($this->expectedRequestHeaders, $this->classUnderTest->getHeaders());
    }

    /**
     * @test
     */
    public function hasHeader_withoutHeader()
    {
        $this->assertFalse($this->classUnderTest->hasHeader('X-Some-Unknown-Header'));
    }

    /**
     * @test
     */
    public function hasHeader_withHeader()
    {
        $this->assertTrue($this->classUnderTest->hasHeader('X-Some-Known-Header'));
    }

    /**
     * @test
     */
    public function hasHeader_withHeaderLowerCase()
    {
        $this->assertTrue($this->classUnderTest->hasHeader('x-some-known-header'));
    }

    /**
     * @test
     */
    public function hasHeader_withMultiValueHeader()
    {
        $this->assertTrue($this->classUnderTest->hasHeader('X-Some-Known-Multi-Value-Header'));
    }

    /**
     * @test
     */
    public function getHeader_withoutHeader()
    {
        $this->assertSame([], $this->classUnderTest->getHeader('X-Some-Unknown-Header'));
    }

    /**
     * @test
     */
    public function getHeader_withHeader()
    {
        $this->assertSame(
            $this->expectedRequestHeaders['X-Some-Known-Header'],
            $this->classUnderTest->getHeader('X-Some-Known-Header')
        );
    }

    /**
     * @test
     */
    public function getHeader_withHeaderLowerCase()
    {
        $this->assertSame(
            $this->expectedRequestHeaders['X-Some-Known-Header'],
            $this->classUnderTest->getHeader('x-some-known-header')
        );
    }

    /**
     * @test
     */
    public function getHeader_withMultiValueHeader()
    {
        $this->assertSame(
            $this->expectedRequestHeaders['X-Some-Known-Multi-Value-Header'],
            $this->classUnderTest->getHeader('X-Some-Known-Multi-Value-Header')
        );
    }

    /**
     * @test
     */
    public function getHeaderLine_withoutHeader()
    {
        $this->assertSame('', $this->classUnderTest->getHeaderLine('X-Some-Unknown-Header'));
    }

    /**
     * @test
     */
    public function getHeaderLine_withHeader()
    {
        $this->assertSame(
            $_SERVER['HTTP_X_SOME_KNOWN_HEADER'],
            $this->classUnderTest->getHeaderLine('X-Some-Known-Header')
        );
    }

    /**
     * @test
     */
    public function getHeaderLine_withHeaderLowerCase()
    {
        $this->assertSame(
            $_SERVER['HTTP_X_SOME_KNOWN_HEADER'],
            $this->classUnderTest->getHeaderLine('x-some-known-header')
        );
    }

    /**
     * @test
     */
    public function getHeaderLine_withMultiValueHeader()
    {
        $this->assertSame(
            $_SERVER['HTTP_X_SOME_KNOWN_MULTI_VALUE_HEADER'],
            $this->classUnderTest->getHeaderLine('X-Some-Known-Multi-Value-Header')
        );
    }

    /**
     * @test
     */
    public function jsonSerialize()
    {
        // test
        $result = json_encode($this->classUnderTest);

        // verify
        $requestArray = json_decode($result);
        $this->assertSame('2.0', $requestArray->httpProtocol);
        $this->assertSame('GET', $requestArray->method);
        $this->assertSame('https://www.example.com' . $this->expecetdRequestUri, $requestArray->url);

        var_dump($requestArray);
    }
}
