<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2019 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Abstracts;

use Doctrine\ORM\EntityManager;
use Zend\Hydrator\Reflection;
use Zend\I18n\Translator\Translator;

abstract class AbstractFacade implements FacadeInterface
{
    /**
     *
     * @var Translator
     */
    protected $translator;

    /**
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $entityName;

    /**
     *
     * @var Reflection
     */
    protected $reflectionHydrator;

    /**
     *
     * @param Translator $translator
     * @param EntityManager $entityManager
     * @param string $entityName
     */
    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        $entityName
    ) {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
        $this->reflectionHydrator = new Reflection();
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
    public function getEntityManager() : EntityManager
    {
        return $this->entityManager;
    }
}
