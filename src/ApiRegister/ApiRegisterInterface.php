<?php

namespace Everlution\SimpleRestApi\ApiRegister;

use Everlution\SimpleRestApi\Api\ApiInterface;

interface ApiRegisterInterface
{
    public function register(ApiInterface $api): void;
}
