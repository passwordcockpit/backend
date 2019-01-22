<?php

/**
 * Description of UserFacade
 *
 * Class with CRUD methods that interfaces User entity with DB
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Facade;

use User\Api\V1\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\ProblemDetailsException;
use Zend\I18n\Translator\Translator;
use User\Api\V1\Entity\Permission;
use User\Api\V1\Hydrator\UserPermissionHydrator;

class PermissionFacade
{
    /**
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     *
     * @var Translator
     */
    private $translator;

    /**
     *
     * @var UserFacade
     */
    private $userFacade;

    /**
     * Contructor
     *
     * @param EntityManager $entityManager
     * @param Translator $translator
     */
    public function __construct(
        EntityManager $entityManager,
        Translator $translator,
        UserFacade $useFacade
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->userFacade = $useFacade;
    }

    /**
     * Create user's permissions
     *
     * @param User $user
     */
    public function createUserPermission($user)
    {
        $permission = new Permission(false, false, false, false, $user);
        $this->entityManager->persist($permission);
        $this->entityManager->flush();
        return true;
    }

    /**
     * Get user's permissions
     *
     * @param int $id
     * @return Right
     */
    public function getUserPermission($id)
    {
        $user = $this->userFacade->get($id);
        $permission = $this->entityManager
            ->getRepository(Permission::class)
            ->find($user);
        if ($permission) {
            return $permission;
        } else {
            throw new ProblemDetailsException(
                404,
                sprintf(
                    $this->translator->translate(
                        'Permissions not found on user %s'
                    ),
                    $user->getUsername()
                ),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     * Returns user's permissions in array format
     *
     * @param User $user
     * @return array
     */
    public function getUserPermissionArray(User $user)
    {
        $userPermissions = $this->getUserPermission($user->getUserId());
        $userPermissionsHydrator = new UserPermissionHydrator();
        $permissions = $userPermissionsHydrator->extract($userPermissions);
        unset($permissions['user_id']);
        $permissions = array_keys(array_diff($permissions, [false]));

        // Everyone is now a 'user', so they can still access their information (only theirs!).
        array_push($permissions, "user");

        /**
         *  GET IDENTITY/TOKEN DATA PARAMS
         */
        $data = [
            "roles" => $permissions
        ];
        return $data;
    }

    /**
     * Update user's permissions
     *
     * @param int $id
     * @return Permission
     */
    public function updateUserPermission($id, ServerRequestInterface $request)
    {
        $user = $this->userFacade->get($id);
        $permission = $this->entityManager
            ->getRepository(Permission::class)
            ->find($user);
        if ($permission) {
            $payload = $request->getParsedBody();
            if (isset($payload['manage_users'])) {
                $permission->setManageUsers($payload['manage_users']);
            }
            if (isset($payload['create_folders'])) {
                $permission->setCreateFolders($payload['create_folders']);
            }
            if (isset($payload['access_all_folders'])) {
                $permission->setAccessAllFolders(
                    $payload['access_all_folders']
                );
            }
            if (isset($payload['view_logs'])) {
                $permission->setViewLogs($payload['view_logs']);
            }
            $this->entityManager->persist($permission);
            $this->entityManager->flush();
            return $permission;
        } else {
            throw new ProblemDetailsException(
                404,
                sprintf(
                    $this->translator->translate(
                        'Permissions not found on user %s'
                    ),
                    $user->getUsername()
                ),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }
}
