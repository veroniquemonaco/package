<?php
/**
 * Created by PhpStorm.
 * User: veronique
 * Date: 29/01/19
 * Time: 22:10
 */

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class PaquetageQualification
{
    private $em;

    /**
     * @return EntityManagerInterface
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param mixed $em
     * @return PaquetageQualification
     */
    public function setEm(EntityManagerInterface $em)
    {
        $this->em = $em;
        return $this;
    }

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->setEm($entityManager);
    }

    public function getPaquetageType(User $user)
    {
        $qualificationId = $user->getQualification()->getId();

        $produits = $this->getEm()->getRepository('AppBundle:Product')->searchBy($qualificationId);

        $idProduits=[];

        foreach ($produits as $produit) {
                $idProduits[]=$produit->getId();
        }

        foreach ($idProduits as $index=>$value) {
            if ($value===30){
                unset($idProduits[$index]);
            } else if ($value===31) {
                unset($idProduits[$index]);
            } else if ($value===32) {
                unset($idProduits[$index]);
            }
        }

        return $idProduits;
    }

}