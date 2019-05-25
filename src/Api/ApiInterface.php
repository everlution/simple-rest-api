<?php

namespace Everlution\SimpleRestApi\Api;

use Everlution\SimpleRestApi\ApiBusinessLogic\ApiBusinessLogicInterface;
use Everlution\SimpleRestApi\ApiRequestHandler\ApiRequestHandlerInterface;
use Everlution\SimpleRestApi\ApiValidator\ApiValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ApiInterface
{
    public function getTitle() : string;

    public function getDescription() : string;

    public static function getMethods() : array;

    public function isEnabled() : bool;

    public function isDeprecated() : bool;

    public function getStatusCodes() : array;

    public static function getRoutesPaths() : array;

    /**
     * This is basically a wrapper of build response that does a bunch of things such as validate the request and the
     * response which are tasks that are executed for every APIs
     *
     * @param Request $request
     * @return Response
     */
    public function sendResponse(Request $request) : Response;

    public function getRequestHandler(): ApiRequestHandlerInterface;

    public function getValidator(): ApiValidatorInterface;

    public function getBusinessLogic(): ApiBusinessLogicInterface;
}
