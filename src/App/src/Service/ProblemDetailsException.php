<?php

/**
 * ProblemDetailsException
 *
 * @package App\Service
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 */

namespace App\Service;

use Zend\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;

/**
 *
 */
class ProblemDetailsException extends \Exception implements
    \Zend\ProblemDetails\Exception\ProblemDetailsExceptionInterface
{
    use CommonProblemDetailsExceptionTrait;

    /**
     *
     * @param int $status
     * @param string $detail
     * @param string $title
     * @param string $type
     * @param array $additional
     */
    public function __construct(
        int $status,
        string $detail,
        string $title = '',
        string $type = '',
        array $additional = []
    ) {
        $this->status = $status;
        $this->title = $title;
        $this->detail = $detail;
        $this->type = $type;
        $this->additional = $additional;
    }
}
