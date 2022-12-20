<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace App\Abstracts;

use Doctrine\ORM\EntityManager;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\I18n\Translator\Translator;

abstract class AbstractFacade implements FacadeInterface
{
    protected ReflectionHydrator $reflectionHydrator;

    /**
     *
     * @param Translator $translator
     * @param EntityManager $entityManager
     * @param string $entityName
     */
    public function __construct(
        protected Translator $translator,
        protected EntityManager $entityManager,
        protected string $entityName
    ) {
        $this->reflectionHydrator = new ReflectionHydrator();
    }

    /**
     *
     * @param type $entity
     * @return null
     */
    public function persist($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     *
     * @param type $entity
     * @return null
     */
    public function remove($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     *
     * @return type
     */
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->entityName);
    }

    /**
     * Return object reference
     *
     * @param type $id
     * @return type
     */
    public function getReference($id)
    {
        return $this->entityManager->getReference($this->entityName, $id);
    }

    /**
     * Return the EntityManager object
     *
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
}
