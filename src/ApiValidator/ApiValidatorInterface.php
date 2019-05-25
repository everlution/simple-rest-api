<?php

namespace Everlution\SimpleRestApi\ApiValidator;

use Everlution\SimpleRestApi\Api\ApiInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ApiValidatorInterface
{
    public function validateRequest(ApiInterface $api, Request $request): void;

    public function validateResponse(ApiInterface $api, Response $response): void;
}
