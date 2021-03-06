<?php
namespace App\Service;

use Laminas\Permissions\Rbac\AssertionInterface;
use Laminas\Permissions\Rbac\Rbac;

/**
 * Description of RbacAssertionManager
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */
class RbacAssertionManager implements AssertionInterface
{
    protected $userId;
    protected $access;
    protected $method;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function setAccess($access)
    {
        $this->access = $access;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function assert(Rbac $rbac)
    {
        if (is_null($this->access)) {
            return false;
        }
        if ($this->access) {
            return true;
        } else {
            return $this->method == 'GET';
        }
    }
}
