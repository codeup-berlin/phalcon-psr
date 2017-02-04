<?php
namespace Codeup\PhalconPsr\Http\Message;

class UriReadAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getUserInfo_withoutUserInfo()
    {
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame('', $classUnderTest->getUserInfo());
    }

    /**
     * @test
     */
    public function getUserInfo_withUsername()
    {
        $expectedUsername = uniqid('username');
        $_SERVER['PHP_AUTH_USER'] = $expectedUsername;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedUsername, $classUnderTest->getUserInfo());
    }

    /**
     * @test
     */
    public function getUserInfo_withPassword()
    {
        $_SERVER['PHP_AUTH_PW'] = uniqid('password');
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame('', $classUnderTest->getUserInfo());
    }

    /**
     * @test
     */
    public function getUserInfo_withUsernameAndPassword()
    {
        $expectedUsername = uniqid('username');
        $expectedPassword = uniqid('password');
        $_SERVER['PHP_AUTH_USER'] = $expectedUsername;
        $_SERVER['PHP_AUTH_PW'] = $expectedPassword;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedUsername . ':' . $expectedPassword, $classUnderTest->getUserInfo());
    }

    /**
     * @test
     */
    public function getAuthority_withHostOnly()
    {
        $expectedHost = uniqid('host');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedHost, $classUnderTest->getAuthority());
    }

    /**
     * @test
     */
    public function getAuthority_withUserInfo()
    {
        $expectedHost = uniqid('host');
        $expectedUsername = uniqid('username');
        $expectedPassword = uniqid('password');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $_SERVER['PHP_AUTH_USER'] = $expectedUsername;
        $_SERVER['PHP_AUTH_PW'] = $expectedPassword;
        $expectedResult = $expectedUsername . ':' . $expectedPassword . '@' . $expectedHost;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->getAuthority());
    }

    /**
     * @test
     */
    public function getAuthority_withUserInfoAndPort()
    {
        $expectedHost = uniqid('host');
        $expectedUsername = uniqid('username');
        $expectedPassword = uniqid('password');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $_SERVER['PHP_AUTH_USER'] = $expectedUsername;
        $_SERVER['PHP_AUTH_PW'] = $expectedPassword;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);

        $phalconRequestMock->method('getPort')->willReturn(458);

        $expectedResult = $expectedUsername . ':' . $expectedPassword . '@' . $expectedHost . ':458';
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->getAuthority());
    }

    /**
     * @test
     */
    public function getAuthority_withUserInfoAndHttpStandardPort()
    {
        $expectedHost = uniqid('host');
        $expectedUsername = uniqid('username');
        $expectedPassword = uniqid('password');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $_SERVER['PHP_AUTH_USER'] = $expectedUsername;
        $_SERVER['PHP_AUTH_PW'] = $expectedPassword;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);

        $phalconRequestMock->method('getPort')->willReturn(80);
        $phalconRequestMock->method('getScheme')->willReturn('http');

        $expectedResult = $expectedUsername . ':' . $expectedPassword . '@' . $expectedHost;
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->getAuthority());
    }

    /**
     * @test
     */
    public function getAuthority_withUserInfoAndHttpsStandardPort()
    {
        $expectedHost = uniqid('host');
        $expectedUsername = uniqid('username');
        $expectedPassword = uniqid('password');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $_SERVER['PHP_AUTH_USER'] = $expectedUsername;
        $_SERVER['PHP_AUTH_PW'] = $expectedPassword;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);

        $phalconRequestMock->method('getPort')->willReturn(443);
        $phalconRequestMock->method('getScheme')->willReturn('https');

        $expectedResult = $expectedUsername . ':' . $expectedPassword . '@' . $expectedHost;
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->getAuthority());
    }

    /**
     * @test
     */
    public function getAuthority_withUserInfoAndHttpsNonStandardPort()
    {
        $expectedHost = uniqid('host');
        $expectedUsername = uniqid('username');
        $expectedPassword = uniqid('password');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $_SERVER['PHP_AUTH_USER'] = $expectedUsername;
        $_SERVER['PHP_AUTH_PW'] = $expectedPassword;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);

        $phalconRequestMock->method('getPort')->willReturn(444);
        $phalconRequestMock->method('getScheme')->willReturn('https');

        $expectedResult = $expectedUsername . ':' . $expectedPassword . '@' . $expectedHost . ':444';
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->getAuthority());
    }

    /**
     * @test
     */
    public function getAuthority_withHttpsNonStandardPort()
    {
        $expectedHost = uniqid('host');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);

        $phalconRequestMock->method('getPort')->willReturn(444);
        $phalconRequestMock->method('getScheme')->willReturn('https');

        $expectedResult = $expectedHost . ':444';
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->getAuthority());
    }

    /**
     * @test
     */
    public function __toString_withAuthority()
    {
        $expectedHost = uniqid('host');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame('//' . $expectedHost, $classUnderTest->__toString());
    }

    /**
     * @test
     */
    public function __toString_withScheme()
    {
        $expectedHost = uniqid('host');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        $phalconRequestMock->method('getScheme')->willReturn('https');

        $expectedResult = 'https://' . $expectedHost;
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->__toString());
    }

    /**
     * @test
     */
    public function __toString_withPath()
    {
        $expectedHost = uniqid('host');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        $expectedPath = uniqid('/some/uri/');
        $_SERVER['REQUEST_URI'] = $expectedPath;

        $expectedResult = '//' . $expectedHost . $expectedPath;
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->__toString());
    }

    /**
     * @test
     */
    public function __toString_withRelativePath()
    {
        $expectedHost = uniqid('host');
        $_SERVER['SERVER_NAME'] = $expectedHost;
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        $expectedPath = uniqid('some/uri/');
        $_SERVER['REQUEST_URI'] = $expectedPath;

        $expectedResult = '//' . $expectedHost . '/' . $expectedPath;
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->__toString());
    }

    /**
     * @test
     */
    public function __toString_withPathOnly()
    {
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        $expectedPath = uniqid('/some/uri/');
        $_SERVER['REQUEST_URI'] = $expectedPath;

        $expectedResult = $expectedPath;
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->__toString());
    }

    /**
     * @test
     */
    public function __toString_withPathAndQuery()
    {
        $phalconRequestMock = $this->createMock(\Phalcon\Http\Request::class);
        $expectedPath = uniqid('/some/uri/');
        $_SERVER['REQUEST_URI'] = $expectedPath;
        $expectedQuery = uniqid('bla=baz&foo=bah');
        $_SERVER['QUERY_STRING'] = $expectedQuery;

        $expectedResult = $expectedPath . '?' . $expectedQuery;
        /** @var \Phalcon\Http\Request $phalconRequestMock */
        $classUnderTest = new UriReadAdapter($phalconRequestMock);
        $this->assertSame($expectedResult, $classUnderTest->__toString());
    }
}
