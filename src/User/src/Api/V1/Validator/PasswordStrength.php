<?php

/**
 * Description of PasswordStrength
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Validator;

use Laminas\Validator\AbstractValidator;

class PasswordStrength extends AbstractValidator
{
    //const LENGTH = 'length';
    const UPPER = 'upper';
    const LOWER = 'lower';
    const DIGIT = 'digit';

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['min'] = array_shift($options);
            if (!empty($options)) {
                $temp['max'] = array_shift($options);
            }
            $options = $temp;
        }
        parent::__construct($options);
    }

    protected $messageTemplates = [
        //self::LENGTH => "'%value%' must be at least 9 characters in length",
        self::UPPER => "Password must contain at least one uppercase letter",
        self::LOWER => "Password must contain at least one lowercase letter",
        self::DIGIT => "Password must contain at least one digit character"
    ];

    /**
     * Function that gets called by the Interface
     *
     * @param string $value
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $isValid = true;

        if (!preg_match('/[A-Z]/', $value)) {
            $this->error(self::UPPER);
            $isValid = false;
        }

        if (!preg_match('/[a-z]/', $value)) {
            $this->error(self::LOWER);
            $isValid = false;
        }

        if (!preg_match('/\d/', $value)) {
            $this->error(self::DIGIT);
            $isValid = false;
        }

        return $isValid;
    }
}
