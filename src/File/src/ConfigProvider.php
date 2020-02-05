<?php

/**
 * Description of ConfigProvider
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace File;

use Laminas\ConfigAggregator\ConfigAggregator;

class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke() : array
    {
        $aggregator = new ConfigAggregator([
            \File\Api\V1\ConfigProvider::class
        ]);

        return $aggregator->getMergedConfig();
    }
}
