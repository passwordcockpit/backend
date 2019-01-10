<?php

/**
 * ConfigProvider
 *
 * @package Authentication
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 */

namespace Authentication;

use Zend\ConfigAggregator\ConfigAggregator;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
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
    public function __invoke(): array
    {
        $aggregator = new ConfigAggregator([
            \Authentication\Api\V1\ConfigProvider::class
        ]);

        return $aggregator->getMergedConfig();
    }
}
