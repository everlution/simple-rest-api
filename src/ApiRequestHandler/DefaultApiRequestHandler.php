<?php

namespace Everlution\SimpleRestApi\ApiRequestHandler;

use Everlution\SimpleRestApi\Api\ApiInterface;
use Everlution\SimpleRestApi\ApiValidator\ApiValidatorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultApiRequestHandler implements ApiRequestHandlerInterface
{
    private $debug;

    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }

    public function handle(ApiInterface $api, Request $request): Response
    {
        // request validation

        try {
            $api
                ->getValidator()
                ->validateRequest($api, $request);
        } catch (ApiValidatorException $exception) {
            return new JsonResponse($exception->getErrors(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            return $this->getExceptionResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // logic

        try {
            $response = $api
                ->getBusinessLogic()
                ->execute($api, $request);
        } catch (\Exception $exception) {
            return $this->getExceptionResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // response validation

        try {
            $api
                ->getValidator()
                ->validateResponse($api, $response);
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
}
