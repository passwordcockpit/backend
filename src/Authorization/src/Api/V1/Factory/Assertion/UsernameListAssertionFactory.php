<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Factory\Assertion;

use Interop\Container\ContainerInterface;
use Authorization\Api\V1\Assertion\UsernameListAssertion;
use Folder\Api\V1\Facade\FolderUserFacade;
use Zend\I18n\Translator\Translator;

class UsernameListAssertionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $translator = $container->get(Translator::class);
        $folderUserFacade = $container->get(FolderUserFacade::class);

        return new UsernameListAssertion($translator, $folderUserFacade);
    }
}
