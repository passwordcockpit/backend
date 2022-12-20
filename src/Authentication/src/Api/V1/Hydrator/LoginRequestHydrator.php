<?php

/**
 * Description of LoginRequestHydrator
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Hydrator;

use Laminas\Hydrator\AbstractHydrator;

class LoginRequestHydrator extends AbstractHydrator
{
    /**
     * extract method, information from a LoginRequest class
     *
     * @param LoginRequest $loginRequest
     * @return array
     */
    public function extract($loginRequest): array
    {
        $data = [];

        $data['request_id'] = $loginRequest->getRequestId();
        $data['ip'] = $loginRequest->getIp();
        $data['attemptdate'] = \App\Service\DateConverter::formatDateTime(
            $loginRequest->getAttemptDate(),
            'outputDate'
        );
        $data['username'] = $loginRequest->getUsername();

        return $data;
    }

    /**
     *
     * @param array $data
     * @param object $object
     * @throws \Exception
     */
    public function hydrate(array $data, object $object)
    {
        throw new \Exception("Method not implemented");
    }
}
