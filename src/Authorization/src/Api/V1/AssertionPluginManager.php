<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\Permissions\Rbac\AssertionInterface;
use Laminas\I18n\Translator\Translator;
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

    /**
     * Constructor
     *
     * @param Translator $translator
     * @param FolderUserFacade $folderUserFacade
     * @param EntityManager $entityManager
     * @param string $configIstanceOrParentLocator
     * @param array $config
     * @param array $assertionRegisteredByRoute
     */
    public function __construct(
        Translator $translator,
        FolderUserFacade $folderUserFacade,
        EntityManager $entityManager,
        $configInstanceOrParentLocator = null,
        array $config = [],
        array $assertionRegisteredByRoute = [],
    ) {
        parent::__construct($configInstanceOrParentLocator, $config);

        $this->assertions = $assertionRegisteredByRoute;
        $this->translator = $translator;
        $this->folderUserFacade = $folderUserFacade;
        $this->entityManager = $entityManager;
    }

    /**
     * function called by the abstract plugin manager
     *
     * @param Rbac $rbac
     * @param string $role
     * @param string $permission
     * @param ServerRequestInterface $request
     * @param User $user
     *
     * @return bool true|false
     */
    public function assert(
        \Laminas\Permissions\Rbac\Rbac $rbac,
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
