<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Hydrator;

use Log\Api\V1\Entity\Log;
use Zend\Hydrator\AbstractHydrator;
use App\Service\DateConverter;
use Zend\Mvc\I18n\Translator;

/**
 * Description of LogHydrator
 */
class LogHydrator extends AbstractHydrator
{
    function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    /**
     * Returns array based on Log's data
     *
     * @param Log $log
     * @return array
     */
    public function extract($log)
    {
        $data = [];
        $data['log_id'] = $log->getLogId();
        $data['password_id'] = $log->getPassword()->getPasswordId();
        $data['user_id'] = $log->getUser()->getUserId();
        $data['action_date'] = DateConverter::formatDateTime(
            $log->getActionDate(),
            'outputDateTime'
        );
        $data['action'] = $this->translator->translate($log->getAction());
        return $data;
    }

    public function hydrate(array $data, $object)
    {
    }
}
