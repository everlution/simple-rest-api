# Everlution Simple REST API

This library provides all the tools for defining REST APIs and validate requests and responses.

## API Definition

In order to define a new API you need to implement the `Everlution\SimpleRestApi\Api\ApiInterface`.

If you are implementing an API that is going to be validated with JSON Schema you can implement the `JsonSchemaApiInterface`.

```php
<?php

namespace App\Api\Sms;

use App\ApiBusinessLogic\Sms\SendApiBusinessLogic;
use App\JsonSchema\Api\Sms\Send\RequestJsonSchema;
use App\JsonSchema\Api\Sms\Send\ResponseJsonSchema;
use Everlution\SimpleRestApi\Api\AbstractApi;
use Everlution\SimpleRestApi\Api\JsonSchemaApiInterface;
use Everlution\SimpleRestApi\ApiRequestHandler\DefaultApiRequestHandler;
use Everlution\SimpleRestApi\ApiValidator\JsonSchemaApiValidator;
use Symfony\Component\HttpFoundation\Request;

class SendApi extends AbstractApi implements JsonSchemaApiInterface
{
    private $requestJsonSchema;

    private $responseJsonSchema;

    public function __construct(
        JsonSchemaApiValidator $apiValidator,
        DefaultApiRequestHandler $apiRequestHandler,
        SendApiBusinessLogic $apiBusinessLogic,
        RequestJsonSchema $requestJsonSchema,
        ResponseJsonSchema $responseJsonSchema
    ) {
        parent::__construct($apiValidator, $apiRequestHandler, $apiBusinessLogic);

        $this->requestJsonSchema = $requestJsonSchema;
        $this->responseJsonSchema = $responseJsonSchema;
    }

    public function getTitle(): string
    {
        return 'SMS Send';
    }

    public function isDeprecated(): bool
    {
        return false;
    }

    public static function getMethods(): array
    {
        return [Request::METHOD_POST];
    }

    public static function getRoutesPaths(): array
    {
        return [
            '/sms/send',
        ];
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getStatusCodes(): array
    {
        return parent::getStatusCodes() + [401 => 'Unauthorized'];
    }

    public function getRequestJsonSchema(): string
    {
        return $this->requestJsonSchema;
    }

    public function getResponseJsonSchema(): string
    {
        return $this->responseJsonSchema;
    }
}

```

## Api Request Handler

This is the service that is in charge to do validation of the request and response and execute the business logic.
The library provides a default handler `Everlution\SimpleRestApi\ApiRequestHandler\DefaultApiRequestHandler`.

## API Validation

```php
<?php

namespace App\JsonSchema\Api\Sms\Send;

class RequestJsonSchema
{
    public function toArray(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'number' => [
                    'type' => 'string',
                    'description' => 'The phone number',
                ],
                'text' => [
                    'type' => 'string',
                    'description' => 'The content of the message',
                    'maxLength' => 255
                ]
            ]
        ];
    }
    
    public function generate(): string
    {
        return json_encode($this->toArray());
    }
}

```

## API Business Logic

This is the service that define what the API is suppose to do.

```php
<?php

namespace App\ApiBusinessLogic\Sms;

use Everlution\SimpleRestApi\Api\ApiInterface;
use Everlution\SimpleRestApi\ApiBusinessLogic\ApiBusinessLogicInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SendApiBusinessLogic implements ApiBusinessLogicInterface
{
    public function execute(ApiInterface $api, Request $request): Response
    {
        // Logic here
    }
}

```

Same thing for the Response JSON Schema

## How to integrate with frameworks

### Symfony 4

Add this file to `config/routes.php`

```php
<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use App\Api\Sms;

return function (RoutingConfigurator $routes) {
    $i = 0;
    foreach (Sms\SendApi::getRoutesPaths() as $routePath) {
        $pathName = sprintf('%s_%s', str_replace('\\', '_', Sms\SendApi::class), $i);
        $routes
            ->add($pathName, $routePath)
            ->controller([Sms\SendApi::class, 'sendResponse'])
            ->methods(Sms\SendApi::getMethods());

        $i++;
    }
};

```