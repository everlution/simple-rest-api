<?php

namespace Everlution\SimpleRestApi\ApiBusinessLogic;

use Everlution\SimpleRestApi\Api\ApiInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ApiBusinessLogicInterface
{
    public function execute(ApiInterface $api, Request $request): Response;
}
