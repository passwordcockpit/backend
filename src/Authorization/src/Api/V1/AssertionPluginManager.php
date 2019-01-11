<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Mvc\I18n\Translator;
use Folder\Api\V1\Facade\FolderUserFacade;
use Doctrine\ORM\EntityManager;

class AssertionPluginManager extends AbstractPluginManager
{
    protected $assertionsRegisteredByRoute;
    protected $instanceOf = AssertionInterface::class;

    protected $translator;
    protected $folderUserFacade;
    private $entityManager;

    protected $assertions;

    public function __construct(
        $configInstanceOrParentLocator = null,
        array $config = array(),
        array $assertionRegisteredByRoute = array(),
        Translator $translator,
        FolderUserFacade $folderUserFacade,
        EntityManager $entityManager
    ) {
        parent::__construct($configInstanceOrParentLocator, $config);

        $this->assertions = $assertionRegisteredByRoute;
        $this->translator = $translator;
        $this->folderUserFacade = $folderUserFacade;
        $this->entityManager = $entityManager;
    }

    public function assert(
        \Zend\Permissions\Rbac\Rbac $rbac,
        $role,
        $permission,
        \Psr\Http\Message\ServerRequestInterface $request,
        $user
    ) {
        if (isset($this->assertions[$permission])) {
            foreach ($this->assertions[$permission] as $assertionClass) {
                $assertion = $this->get($assertionClass);
                $assertion->setRequest($request);
                $assertion->setUser($user);

                return $assertion->assert(
                    $rbac,
                    $rbac->getRole($role),
                    $permission
                );
            }
        }

        return true;
    }
}
