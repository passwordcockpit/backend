<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Abstracts;

use Doctrine\ORM\EntityManager;
use App\Abstracts\FacadeInterface;
use Zend\Hydrator\Reflection;
use Zend\Mvc\I18n\Translator;

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
     * @param type $entityName
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
     */
    function persist($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     *
     * @param type $entity
     */
    function remove($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     *
     * @return type
     */
    function getRepository()
    {
        return $this->entityManager->getRepository($this->entityName);
    }
}
