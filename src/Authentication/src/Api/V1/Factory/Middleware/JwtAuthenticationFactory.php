<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use Slim\Middleware\JwtAuthentication;
use Zend\Diactoros\Response\JsonResponse;

class JwtAuthenticationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];

        if (!isset($config['authentication']['secret_key'])) {
            throw new \Exception("Secret authentication key not found");
        }
        $secure = true;
        if (isset($config['authentication']['secure'])) {
            $secure = $config['authentication']['secure'];
        }

        return new JwtAuthentication([
            "secure" => $secure,
            "secret" => $config['authentication']['secret_key'],
            "path" => ["/api", "/"],
            "passthrough" => ["/api/auth", "/api/ping"], //"/api/v1/"
            "error" => function ($request, $response, $arguments) {
                $data["status"] = 401;
                $data["title"] = "Unauthorized";
                $data["type"] = "https://httpstatuses.com/401";
                $data["detail"] = $arguments["message"];
                return new JsonResponse($data);
            }
        ]);
    }
}
