<?php

namespace AppBundle\Repository;

/**
 * ProductPackageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductPackageRepository extends \Doctrine\ORM\EntityRepository
{
    public function searchOrderLineBy($agence,$paquetageType)
    {

        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.user','u');

            if (!$agence == null) {
                $qb
                    ->leftJoin('u.agence','a')
                    ->andWhere('a.name = :agence')
                    ->setParameter('agence',$agence);
            }

            if (!$paquetageType == null) {
                $qb
                    ->leftJoin('u.qualification', 'q')
                    ->andWhere('q.name = :qualification')
                    ->setParameter('qualification',$paquetageType);
            }

            return $qb->getQuery()->getResult();


        return $qb->getQuery()->getResult();
    }
}
