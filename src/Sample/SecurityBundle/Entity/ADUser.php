<?php

namespace Sample\SecurityBundle\Entity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * ADUser
 */
class ADUser implements UserInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $username;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return ADUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $roles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add roles
     *
     * @param \Sample\SecurityBundle\Entity\ADRole $role
     * @return ADUser
     */
    public function addRole(\Sample\SecurityBundle\Entity\ADRole $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Sample\SecurityBundle\Entity\ADRole $role
     */
    public function removeRole(\Sample\SecurityBundle\Entity\ADRole $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Get roles
     * @return array
     */
    public function getRoles()
    {
        $roles = array();
        foreach ($this->roles as $role)
        {
            $roles[] = $role->getRole();
        }
        return $roles;
    }

    public function getSalt() {}

    public function getPassword() {}

    public function eraseCredentials() {}
}
