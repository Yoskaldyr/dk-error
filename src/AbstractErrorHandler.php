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

    public function __construct(ErrorResponseStrategy $responseStrategy = null)
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
            return $response;
        }
        else {
            $errorResponse = null;
            if($this->responseStrategy) {
                $errorResponse = $this->responseStrategy->createResponse($request, $response, $error);
            }

            if ($errorResponse && !$errorResponse instanceof ResponseInterface) {
                throw new \RuntimeException(
                    sprintf('ErrorResponseStrategy must return an instance of %s or null if you want to skip',
                        ResponseInterface::class));
            }

            if(!$errorResponse) {
                if($next) {
                    return $next($request, $response->withStatus($error->getCode()), $error);
                }
                return $response->withStatus($error->getCode());
            }

            return $errorResponse;
        }
    }
}