<?php

namespace Everlution\SimpleRestApi\Api;

interface JsonSchemaApiInterface
{
    public function getRequestJsonSchema(): string;

    public function getResponseJsonSchema(): string;
}
