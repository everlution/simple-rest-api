<?php

namespace Everlution\SimpleRestApi\Api;

use Everlution\SimpleRestApi\ApiRequestHandler\ApiRequestHandlerInterface;
use Everlution\SimpleRestApi\ApiValidator\ApiValidatorException;
use Everlution\SimpleRestApi\ApiValidator\ApiValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApi implements ApiInterface
{
    private $debug;

    private $apiValidator;

    private $apiRequestHandler;

    public function __construct(
        bool $debug,
        ApiValidatorInterface $apiValidator,
        ApiRequestHandlerInterface $apiRequestHandler
    ) {
        $this->debug = $debug;
        $this->apiValidator = $apiValidator;
        $this->apiRequestHandler = $apiRequestHandler;
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
        // request validation

        try {
            $this
                ->apiValidator
                ->validateRequest($this, $request);
        } catch (ApiValidatorException $exception) {
            return new JsonResponse($exception->getErrors(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            return $this->getExceptionResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // logic

        try {
            $response = $this
                ->getRequestHandler()
                ->handle($this, $request);
        } catch (\Exception $exception) {
            return $this->getExceptionResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // response validation

        try {
            $this
                ->apiValidator
                ->validateResponse($this, $response);
        } catch (ApiValidatorException $exception) {
            return $this->getExceptionResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR, $response);
        } catch (\Exception $exception) {
            return $this->getExceptionResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR, $response);
        }

        return $response;
    }

    private function getExceptionResponse(\Exception $e, int $statusCode, ?Response $response = null): JsonResponse
    {
        $data = [
            'api' => get_class($this),
            'api_validation_errors' => $e instanceof ApiValidatorException ? $e->getErrors() : null,
            'response' => $response ? json_decode($response->getContent(), true) : null,
            'exception' => (string) $e,
        ];

        if ($this->debug) {
            return new JsonResponse($data, $statusCode);
        } else {
            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getRequestHandler(): ApiRequestHandlerInterface
    {
        return $this->apiRequestHandler;
    }
}
