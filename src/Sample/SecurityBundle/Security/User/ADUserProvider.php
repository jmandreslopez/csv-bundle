<?php

namespace Sample\SecurityBundle\Security\User;

use Doctrine\ORM\EntityManager;
use Sample\SecurityBundle\Entity\ADUser;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ADUserProvider implements UserProviderInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('SampleSecurityBundle:ADUser')->findOneBy(array('username' => $username));
        if ($user) {
            return $user;
        } else {
            $user = $this->createUser($username);
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('No record found for user %s', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof ADUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class == 'Sample\SecurityBundle\Entity\ADUser';
    }

    public function createUser($username)
    {
        $user = new ADUser();
        $user->setUsername($username);
        $role = $this->em->getRepository('SampleSecurityBundle:ADRole')->findOneBy(array('name' => 'user'));
        $user->addRole($role);
        $this->em->persist($user);
        $this->em->flush();
        
        return $user;
    }
} 