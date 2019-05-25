<?php

namespace Everlution\SimpleRestApi\ApiRequestHandler;

use Everlution\SimpleRestApi\Api\ApiInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ApiRequestHandlerInterface
{
    public function handle(ApiInterface $api, Request $request): Response;
}
