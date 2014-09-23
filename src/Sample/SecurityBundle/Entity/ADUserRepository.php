<?php

namespace Sample\SecurityBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ADUserRepository extends EntityRepository
{
    /**
     * 
     * @param type $search
     * @return type
     */
    public function getAllWithRoles($search = null)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('u, r')
            ->from($this->_entityName, 'u')
            ->innerJoin('u.roles', 'r');
        if($search) {
            $q->where('u.username LIKE :search')
                ->setParameter('search', $search);

        }
        return $q->getQuery()->getArrayResult();
    }
}
