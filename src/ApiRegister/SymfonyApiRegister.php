<?php

namespace Everlution\SimpleRestApi\ApiRegister;

use Everlution\SimpleRestApi\Api\ApiInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class SymfonyApiRegister implements ApiRegisterInterface
{
    private $routeCollection;

    public function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }

    public function register(ApiInterface $api): void
    {
        $i = 0;

        foreach ($api->getRoutesPaths() as $routePath) {
            $name = $this->getControllerIdByNamespace($api, $i);

            $this
                ->routeCollection
                ->add($name, new Route(
                    $routePath,
                    ['_controller' => sprintf('%s::sendResponse', static::class)],
                    [],
                    [],
                    '',
                    [],
                    $api->getMethods()
                ));

            $i ++;
        }
    }

    private function getControllerIdByNamespace(ApiInterface $api, int $index): string
    {
        $className = get_class($api);
        $className = str_replace('\\', '_', $className);
        $className = strtolower($className);
        $className .= $index;

        return $className;
    }

}
