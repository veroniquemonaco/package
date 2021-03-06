<?php

namespace AppBundle\Repository;

/**
 * TailleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TailleRepository extends \Doctrine\ORM\EntityRepository
{
    public function searchTailleByCategory($categoryId)
    {
        $qb = $this->createQueryBuilder('t');

            $qb->select('t')
                ->leftJoin('t.products','p')
                ->leftJoin('p.category','c')
                ->where('c.id = :idpdt')
                ->setParameter('idpdt',$categoryId);

            return $qb->getQuery()->getResult();
    }
}
