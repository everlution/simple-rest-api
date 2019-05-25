<?php

namespace Everlution\SimpleRestApi\ApiValidator;

use Throwable;

class ApiValidatorException extends \Exception
{
    private $errors;

    public function __construct(array $errors, int $code = 0, Throwable $previous = null)
    {
        $message = 'API Validation error';

        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
