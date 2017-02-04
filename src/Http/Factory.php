<?php
namespace Codeup\PhalconPsr\Http;

class Factory
{
    /**
     * @param \Phalcon\Http\Request $phalconRequest
     * @param string $psrImplementation
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function factorServerRequest(\Phalcon\Http\Request $phalconRequest, $psrImplementation = 'guzzle')
    {
        switch ($psrImplementation) {
            case 'phalconReadAdapter':
                return new Message\ServerRequestReadAdapter($phalconRequest);
            case 'guzzle':
                $httpMethod = $phalconRequest->getMethod();
                $result = new \GuzzleHttp\Psr7\ServerRequest(
                    $httpMethod,
                    $phalconRequest->getURI(),
                    $phalconRequest->getHeaders(),
                    $phalconRequest->getRawBody(),
                    '1.1',
                    $_SERVER
                );
                if ($phalconRequest->isPost()) {
                    $result = $result->withParsedBody(
                        $phalconRequest->getPost()
                    );
                }
                break;
            default:
                throw new \DomainException('Unknown PSR implementation: ' . $psrImplementation);
        }
        return $result;
    }

    /**
     * @param string $psrImplementation
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \DomainException
     */
    public function factorResponse($psrImplementation = 'guzzle')
    {
        switch ($psrImplementation) {
            case 'guzzle':
                return new \GuzzleHttp\Psr7\Response();
            default:
                throw new \DomainException('Unknown PSR implementation: ' . $psrImplementation);
        }
    }
}
