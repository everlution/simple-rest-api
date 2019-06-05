<?php

namespace Everlution\SimpleRestApi\ApiValidator;

use Everlution\SimpleRestApi\Api\ApiInterface;
use Everlution\SimpleRestApi\Api\JsonSchemaApiInterface;
use JsonSchema\Validator;
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

        if ($dataArray === null) {
            throw new ApiValidatorException([
                '' => 'The Request Content is not a valid JSON string',
            ]);
        }

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

        if ($dataArray === null) {
            throw new ApiValidatorException([
                '' => 'The Request Content is not a valid JSON string',
            ]);
        }

        $this->jsonSchemaValidate($api->getResponseJsonSchema(), $dataArray);
    }


    private function jsonSchemaValidate(string $jsonSchema, array $record)
    {
        $data = $this->arrayToObject($record);

        $this
            ->validator
            ->validate($data, json_decode($jsonSchema, true));

        if (!$this->validator->isValid()) {
            throw new ApiValidatorException($this->validator->getErrors());
        }
    }

    /**
     * This must be recursive, so the whole array and sub arrays must be stdClass
     *
     * @param $array
     * @return mixed
     */
    private function arrayToObject($array): object
    {
        if (count($array) == 0) {
            return new \stdClass();
        }

        // First we convert the array to a json string
        $json = json_encode($array);

        // The we convert the json string to a stdClass()
        $object = json_decode($json);

        return $object;
    }
}
