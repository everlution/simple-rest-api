<?php

namespace Everlution\SimpleRestApi\Api;

use Everlution\SimpleRestApi\ApiBusinessLogic\ApiBusinessLogicInterface;
use Everlution\SimpleRestApi\ApiRequestHandler\ApiRequestHandlerInterface;
use Everlution\SimpleRestApi\ApiValidator\ApiValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApi implements ApiInterface
{
    private $apiValidator;

    private $apiRequestHandler;

    private $apiBusinessLogic;

    public function __construct(
        ApiValidatorInterface $apiValidator,
        ApiRequestHandlerInterface $apiRequestHandler,
        ApiBusinessLogicInterface $apiBusinessLogic
    ) {
        $this->apiValidator = $apiValidator;
        $this->apiRequestHandler = $apiRequestHandler;
        $this->apiBusinessLogic = $apiBusinessLogic;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function getStatusCodes() : array
    {
        return [
            Response::HTTP_BAD_REQUEST => 'Bad Request - Invalid Input',
            Response::HTTP_NOT_FOUND => 'Not found',
            Response::HTTP_INTERNAL_SERVER_ERROR => 'Internal Error',
        ];
    }

    public function sendResponse(Request $request): Response
    {
        return $this
            ->apiRequestHandler
            ->handle($this, $request);
    }

    public function getRequestHandler(): ApiRequestHandlerInterface
    {
        return $this->apiRequestHandler;
    }

    public function getBusinessLogic(): ApiBusinessLogicInterface
    {
        return $this->apiBusinessLogic;
    }
}
