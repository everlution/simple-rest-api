<?php

namespace Everlution\SimpleRestApi\Api;

use Everlution\SimpleRestApi\ApiRequestHandler\ApiRequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ApiInterface
{
    public function getTitle() : string;

    public function getDescription() : string;

    public function getMethods() : array;

    public function isEnabled() : bool;

    public function isDeprecated() : bool;

    public function getStatusCodes() : array;

    public function getRoutesPaths() : array;

    /**
     * This is basically a wrapper of build response that does a bunch of things such as validate the request and the
     * response which are tasks that are executed for every APIs
     *
     * @param Request $request
     * @return Response
     */
    public function sendResponse(Request $request) : Response;

    public function getRequestHandler(): ApiRequestHandlerInterface;

    public function getRequestJsonSchema(): string;

    public function getResponseJsonSchema(): string;
}
