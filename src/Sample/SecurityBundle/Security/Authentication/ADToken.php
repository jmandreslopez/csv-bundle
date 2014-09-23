<?php

namespace Sample\SecurityBundle\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\RoleInterface;

class ADToken extends UsernamePasswordToken
{
    private $area;

    public function __construct($user, $credentials, $providerKey, array $roles = array(), $area)
    {
        $this->area = $area;
        parent::__construct($user, $credentials, $providerKey, $roles);
    }

    public function serialize()
    {
        return serialize(array($this->area, parent::serialize()));
    }

    public function unserialize($serialized)
    {
        list($this->area, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }

    public function getArea()
    {
        return $this->area;
    }
} 