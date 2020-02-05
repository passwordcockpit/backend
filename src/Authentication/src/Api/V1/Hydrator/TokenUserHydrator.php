<?php

/**
 * Description of TokenUserHydrator
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Hydrator;

use Laminas\Hydrator\AbstractHydrator;

class TokenUserHydrator extends AbstractHydrator
{
    /**
     * Extract information from a TokenUser object
     *
     * @param Tokenuser $tokenUser
     * @return array
     */
    public function extract($tokenUser): array
    {
        $data = [];

        $data['user_id'] = $tokenUser->getUser()->getUserId();
        $data['token'] = $tokenUser->getToken();
        $data['last_login'] = \App\Service\DateConverter::formatDateTime(
            $tokenUser->getLastLogin(),
            'outputDate'
        );

        return $data;
    }
}
