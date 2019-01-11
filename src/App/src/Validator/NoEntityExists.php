<?php

/**
 * @package App\Validator
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace App\Validator;

use Zend\Validator\AbstractValidator;
use User\Api\V1\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Util\Debug;

class NoEntityExists extends AbstractValidator
{
    const RECORD_FOUND = 'record_found';

    /**
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     *
     * @var string
     */
    private $field;

    /**
     *
     * @var string
     */
    private $entity;

    /**
     *
     * @var int
     */
    private $exclude;

    /**
     * @var array Message templates
     */
    protected $messageTemplates = [
        self::RECORD_FOUND => "A record matching the input was found"
    ];

    public function __construct(
        $options = [],
        EntityManagerInterface $entityManager
    ) {
        $this->entity = $options['entity'];
        $this->field = $options['field'];
        $this->exclude = $options['exclude'];
        $this->entityManager = $entityManager;
        parent::__construct($options);
    }

    public function isValid($value)
    {
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->eq($this->field, $value));

        //if excludeId is set, it means we are updating the entity.
        // we need to filter out the user on which the request is made
        if ($this->exclude) {
            $criteria->andWhere(
                $criteria
                    ->expr()
                    ->neq($this->exclude['field'], $this->exclude['value'])
            );
        }
        $users = $this->entityManager
            ->getRepository($this->entity)
            ->matching($criteria);

        if (count($users) > 0) {
            $this->error(self::RECORD_FOUND);
            return false;
        }
        return true;
    }
}
