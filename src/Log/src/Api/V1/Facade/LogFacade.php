<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Facade;

use Doctrine\ORM\EntityManager;
use Zend\I18n\Translator\Translator;
use App\Service\ProblemDetailsException;
use Password\Api\V1\Entity\Password;
use User\Api\V1\Entity\User;
use Log\Api\V1\Entity\Log;

/**
 * Description of LogFacade
 */
class LogFacade
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
     * Contructor
     *
     * @param EntityManager $entityManager
     * @param Translator $translator
     */
    public function __construct(
        EntityManager $entityManager,
        Translator $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Returns logs for specified password
     *
     * @param int $id
     * @return array of Log
     */
    public function getPasswordLog($id)
    {
        $password = $this->entityManager
            ->getRepository(Password::class)
            ->find($id);
        if ($password) {
            $logs = $this->entityManager
                ->getRepository(Log::class)
                ->findBy(['password' => $password]);
            return $logs;
        } else {
            // check if it's a deleted password
            $log = $this->entityManager
                ->getRepository(Log::class)
                ->findBy(['action' => "Password " . $id . " deleted"]);
            if ($log) {
                return $log;
            } else {
                // else password did not even exist
                throw new ProblemDetailsException(
                    404,
                    $this->translator->translate('Password not found'),
                    $this->translator->translate('Resource not found'),
                    'https://httpstatus.es/404'
                );
            }
        }
    }

    /**
     * Same as above, but get password as parameter
     *
     * @param Password $pass
     * @return array of Log
     */
    public function getPasswordLogFromPass($password)
    {
        if ($password) {
            $logs = $this->entityManager
                ->getRepository(Log::class)
                ->findBy(['password' => $password]);
            return $logs;
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Password not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     * Returns log by its id
     *
     * @param int $id
     * @return Log
     */
    public function getLog($id)
    {
        $log = $this->entityManager->getRepository(Log::class)->find($id);
        if ($log) {
            return $log;
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Log not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     *
     * @param type $id
     * @return type
     * @throws ProblemDetailsException
     */
    public function getUserLog($id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if ($user) {
            $logs = $this->entityManager
                ->getRepository(Log::class)
                ->findBy(['user' => $user]);
            return $logs;
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
     * Create a new Log
     *
     * @param int $passwordId
     * @param string $action
     * @param int $userId
     *
     * @return bool true
     */
    public function updateLog($passwordId, $action, $userId)
    {
        $log = new Log();
        $log->setAction($action);
        $pass = $this->entityManager->getReference(
            Password::class,
            $passwordId
        );
        $log->setPassword($pass);
        $log->setActionDate(new \DateTime());
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $log->setUser($user);
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return true;
    }

    /**
     *
     * Create a log that shows who deleted a password.
     *
     * @param int $passwordId
     * @param User $user
     *
     */
    public function createDeletedLog($passwordId, $user)
    {
        $log = new Log();

        $action = "Password " . $passwordId . " deleted";
        $log->setAction($action);
        $log->setUser($user);
        $log->setPassword(null);
        $log->setActionDate(new \DateTime());

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return true;
    }
}
