<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/24/2016
 * Time: 5:58 PM
 */

namespace N3vrax\DkError;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ErrorResponseStrategy
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Error $error
     * @return ResponseInterface
     */
    public function createResponse(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Error $error);
}