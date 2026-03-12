<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Factory\Middleware;

use Psr\Container\ContainerInterface;
use JimTools\JwtAuth\Middleware\JwtAuthentication;
use JimTools\JwtAuth\Decoder\FirebaseDecoder;
use JimTools\JwtAuth\Options;
use JimTools\JwtAuth\Rules\RequestMethodRule;
use JimTools\JwtAuth\Secret;
use JimTools\JwtAuth\Rules\RequestPathRule;

class JwtAuthenticationFactory
{
    /**
     * Invoke method, create instance of JwtAuthentication class
     *
     * @param ContainerInterface $container
     * @return JwtAuthentication
     */
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

        $relaxed = [];
        if(isset($config['authentication']['relaxed'])){
            $relaxed = $config['authentication']['relaxed'];
        }

        $decoder = new FirebaseDecoder(
            new Secret(
                $config['authentication']['secret_key'],
                'HS256'
            )
        );

        $options = new Options(
            isSecure: $secure,
            relaxed: $relaxed
        );

        $rules = [
            new RequestMethodRule(),
            new RequestPathRule(
                paths: ["/api", "/"],
                ignore: ["/api/auth", "/api/v1/token/update"]
            ),
        ];

        return new JwtAuthentication($options, $decoder, $rules);
    }
}
