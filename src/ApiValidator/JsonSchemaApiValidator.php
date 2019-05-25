<?php

namespace Everlution\SimpleRestApi\ApiValidator;

use Everlution\SimpleRestApi\Api\ApiInterface;
use Everlution\SimpleRestApi\Api\JsonSchemaApiInterface;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonSchemaApiValidator implements ApiValidatorInterface
{
    protected $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function validateRequest(ApiInterface $api, Request $request): void
    {
        if (!$api instanceof JsonSchemaApiInterface) {
            throw new \Exception('Api MUST implement ' . JsonSchemaApiInterface::class);
        }

        if ($request->headers->get('Content-Type') != 'application/json') {
            throw new \Exception('Request Content-Type must be application/json');
        }

        $data = $request->getContent();
        $dataArray = json_decode($data, true);

        $this->jsonSchemaValidate($api->getRequestJsonSchema(), $dataArray);
    }

    public function validateResponse(ApiInterface $api, Response $response): void
    {
        if (!$api instanceof JsonSchemaApiInterface) {
            throw new \Exception('Api MUST implement ' . JsonSchemaApiInterface::class);
        }

        if ($response->headers->get('Content-Type') != 'application/json') {
            throw new \Exception('Response Content-Type must be application/json');
        }

        $data = $response->getContent();
        $dataArray = json_decode($data, true);

        $this->jsonSchemaValidate($api->getResponseJsonSchema(), $dataArray);
    }


    private function jsonSchemaValidate(string $jsonSchema, array $record)
    {
        $schema = Schema::fromJsonString($jsonSchema);

        $validator = new Validator();

        $object = json_decode(json_encode($record));

        $result = $validator->schemaValidation($object, $schema);

        if (!$result->isValid()) {
            $errors = [];

            foreach ($result->getErrors() as $error) {
                $errors[$error->keyword()] = json_encode($error->keywordArgs());
            }

            throw new ApiValidatorException($errors);
        }
    }
}
