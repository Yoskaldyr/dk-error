<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/24/2016
 * Time: 5:55 PM
 */

namespace N3vrax\DkError;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractErrorHandler
{
    protected $responseStrategy;

    public function __construct(ErrorResponseStrategy $responseStrategy)
    {
        $this->responseStrategy = $responseStrategy;
    }

    public function __invoke(
        $error,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null)
    {
        if(!$error instanceof Error)
        {
            if($next) {
                return $next($request, $response, $error);
            }
        }

        $errorResponse = $this->responseStrategy->createResponse($request, $response, $error);
        if(!$errorResponse instanceof ResponseInterface)
        {
            throw new \RuntimeException(
                sprintf('ErrorResponseStrategy must return an instance of %s', ResponseInterface::class));
        }

        return $errorResponse;
    }
}