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

use App\Abstracts\AbstractFacade;
use User\Api\V1\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\ProblemDetailsException;
use Folder\Api\V1\Entity\Folder;
use Folder\Api\V1\Entity\FolderUser;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\I18n\Translator\Translator;
use User\Api\V1\Entity\Permission;

class UserFacade extends AbstractFacade
{
    /**
     * Contructor
     *
     * @param EntityManager $entityManager
     * @param Translator $translator
     */
    public function __construct(
        protected EntityManager $entityManager,
        protected Translator $translator
    ) {
        parent::__construct($translator, $entityManager, User::class);
    }

    /**
     *
     * @param array $data
     */
    public function create($data): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param string $id
     * @param array $filter
     */
    public function fetch($id, $filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param array $filter
     */
    public function fetchAll($filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param string $id
     * @param array $data
     */
    public function update($id, $data): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param type $id
     * @param type $filter
     */
    public function delete($id, $filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     * Create a new user
     *
     * @param ServerRequestInterface $request
     * @return User
     */
    public function createUser(ServerRequestInterface $request)
    {
        $payload = $request->getParsedBody(); // recupero il payload
        $user = new User(); // creo l'oggetto utente
        // setto i campi
        $user->setUsername($payload['username']);

        if(isset($payload['password'])){
            // Bcrypt della password ---
            $bcrypt = new Bcrypt();
            $bcryptedPassword = $bcrypt->create($payload['password']);
            $user->setPassword($bcryptedPassword);
        }
       
        $user->setName($payload['name']);
        $user->setSurname($payload['surname']);
        $user->setPhone($payload['phone']);
        $user->setEmail($payload['email']);
        $user->setLanguage($payload['language']);
        $user->setEnabled($payload['enabled']);
        $user->setChangePassword(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return boolean
     * @throws ProblemDetailsException
     */
    public function deleteUser($id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('User not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
        return true;
    }

    /**
     * Returns all users, but without personal informations
     *
     * @return array of User
     */
    public function getAllUsernames()
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $user->setCompleteUser();
        }
        return $users;
    }

    /**
     * Returns all users
     *
     * @return array of User
     */
    public function getAll()
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $users;
    }

    /**
     * Get a user by id
     *
     * @param int $id
     * @return User
     * @throws ProblemDetailsException
     */
    public function get($id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if ($user) {
            return $user;
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('User not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     * Returns User by its username
     *
     * @param string $username
     * @return User
     * @throws ProblemDetailsException
     */
    public function getUserByUsername($username)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        if ($user) {
            return $user;
        } else {
            throw new ProblemDetailsException(
                404,
                sprintf(
                    $this->translator->translate(
                        'User with username %s not found in DB'
                    ),
                    $username
                ),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     * Return the FolderUser object given a folder and a user
     *
     * @param int $id
     *
     * @return FolderUser[]
     */
    public function listFoldersPermission(int $userId)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('fu')
            ->from(FolderUser::class, 'fu')
            ->join(Folder::class, 'folder', 'WITH', 'fu.folder=folder')
            ->join('fu.user', 'user')
            ->where('user.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Updates a user
     *
     * @param int $id
     * @param ServerRequestInterface $request
     * @return User
     * @throws ProblemDetailsException
     */
    public function updateUser($id, ServerRequestInterface $request)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if ($user) {
            $payload = $request->getParsedBody();
            if (isset($payload['username'])) {
                $user->setUsername($payload['username']);
            }
            if (isset($payload['password'])) {
                // checking ldap info. If active user can't change password.
                $token = $request->getAttribute("token", false);
                $authType = $token["data"]["ldap"];

                if ($authType) {
                    throw new ProblemDetailsException(
                        400,
                        'Ldap is active, it is not possible to change password',
                        $this->translator->translate('Bad Request'),
                        'https://httpstatus.es/400'
                    );
                    return $response;
                }

                // Bcrypt della password ---
                $bcrypt = new \Laminas\Crypt\Password\Bcrypt();
                $bcryptedPassword = $bcrypt->create($payload['password']);

                // checking who is making the request
                // if the user has 'manage_users', we do not need to check the actual password.
                $userRequest = $request->getAttribute('Authentication\User');
                $perm = $this->entityManager
                    ->getRepository(Permission::class)
                    ->findOneBy(['user' => $userRequest]);

                // if the user making the request does have 'manage_users'
                // we need to check for the actual_password field
                if (
                    $perm->getManageUsers() &&
                    $payload['actual_password'] == null
                ) {
                    $user->setPassword($bcryptedPassword);
                } else {
                    //check if actual_password field exist
                    if (!isset($payload['actual_password'])) {
                        throw new ProblemDetailsException(
                            400,
                            $this->translator->translate(
                                'Missing actual password'
                            ),
                            $this->translator->translate('Bad Request'),
                            'https://httpstatus.es/400'
                        );
                    }
                    //here
                    // check actual password
                    if (
                        $this->checkPassword($user, $payload['actual_password'])
                    ) {
                        $user->setPassword($bcryptedPassword);
                    } else {
                        throw new ProblemDetailsException(
                            404,
                            $this->translator->translate(
                                'Wrong actual password'
                            ),
                            $this->translator->translate('Resource not found'),
                            'https://httpstatus.es/404'
                        );
                    }
                }
            }

            if (isset($payload['name'])) {
                $user->setName($payload['name']);
            }
            if (isset($payload['surname'])) {
                $user->setSurname($payload['surname']);
            }
            if (isset($payload['language'])) {
                $user->setLanguage($payload['language']);
            }
            if (isset($payload['phone'])) {
                $user->setPhone($payload['phone']);
            }
            if (isset($payload['email'])) {
                $user->setEmail($payload['email']);
            }
            if (isset($payload['enabled'])) {
                $user->setEnabled($payload['enabled']);
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $user = $this->entityManager
                ->getRepository(User::class)
                ->find($user->getUserId());
            return $user;
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('User not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     * Check wether user's password is correct
     *
     * @param User $user
     * @param string $password
     * @return boolean
     */
    private function checkPassword($user, $password)
    {
        $check = false;
        $bcrypt = new \Laminas\Crypt\Password\Bcrypt();
        $securePass = $user->getPassword();
        $check = $bcrypt->verify($password, $securePass);
        return $check;
    }

    /**
     * Change the value in the database that check is the user need to change his password to false
     *
     * @param User $user
     */
    public function userChangedPassword(User $user)
    {
        $user->setChangePassword(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
