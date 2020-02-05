<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace App\Factory\Validator;

use App\Validator\StringParameterValidator;
use Psr\Container\ContainerInterface;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\InputFilter\Factory as InputFilterFactory;
use Laminas\I18n\Translator\Translator;

class StringParameterValidatorFactory
{
    /**
     * Invoke method, create instance of StringParameterValidator class
     *
     * @param ContainerInterface $container
     * @return StringParameterValidator
     */
    public function __invoke(ContainerInterface $container)
    {
        return new StringParameterValidator(
            new InputFilterFactory(
                $container->get(InputFilterPluginManager::class)
            ),
            $container->get(Translator::class)
        );
    }
}
