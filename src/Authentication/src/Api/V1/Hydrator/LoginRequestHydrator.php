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

use Zend\Hydrator\AbstractHydrator;

class LoginRequestHydrator extends AbstractHydrator
{
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
}
