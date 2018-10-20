<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPackage
 *
 * @ORM\Table(name="product_package")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductPackageRepository")
 */
class ProductPackage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idpdt", type="integer")
     */
    private $idpdt;

    /**
     * @var string
     *
     * @ORM\Column(name="libellePdt", type="string", length=255)
     */
    private $libellePdt;

    /**
     * @var string
     *
     * @ORM\Column(name="taille", type="string", length=255)
     */
    private $taille;

    /**
     * @var integer
     *
     * @ORM\Column(name="taille_id", type="integer")
     */
    private $tailleId;


    /**
     * @var int
     *
     * @ORM\Column(name="qty", type="integer")
     */
    private $qty;

    /**
     * @var int
     *
     * @ORM\Column(name="yearPaquetage", type="integer")
     */
    private $yearPaquetage;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="productsPackage")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="idpdtUnique", type="string")
     */
    private $idpdtUnique;

    /**
     * @return string
     */
    public function getIdpdtUnique()
    {
        return $this->idpdtUnique;
    }

    /**
     * @param string $idpdtUnique
     */
    public function setIdpdtUnique($idpdtUnique)
    {
        $this->idpdtUnique = $this->getIdpdt().$this->getTaille();
    }


    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idpdt.
     *
     * @param int $idpdt
     *
     * @return ProductPackage
     */
    public function setIdpdt($idpdt)
    {
        $this->idpdt = $idpdt;

        return $this;
    }

    /**
     * Get idpdt.
     *
     * @return int
     */
    public function getIdpdt()
    {
        return $this->idpdt;
    }

    /**
     * Set libellePdt.
     *
     * @param string $libellePdt
     *
     * @return ProductPackage
     */
    public function setLibellePdt($libellePdt)
    {
        $this->libellePdt = $libellePdt;

        return $this;
    }

    /**
     * Get libellePdt.
     *
     * @return string
     */
    public function getLibellePdt()
    {
        return $this->libellePdt;
    }

    /**
     * Set taille.
     *
     * @param string $taille
     *
     * @return ProductPackage
     */
    public function setTaille($taille)
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * Get taille.
     *
     * @return string
     */
    public function getTaille()
    {
        return $this->taille;
    }

    /**
     * @return int
     */
    public function getTailleId()
    {
        return $this->tailleId;
    }

    /**
     * @param int $tailleId
     */
    public function setTailleId($tailleId)
    {
        $this->tailleId = $tailleId;
    }

    /**
     * Set qty.
     *
     * @param int $qty
     *
     * @return ProductPackage
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty.
     *
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set yearPaquetage.
     *
     * @param string $yearPaquetage
     *
     * @return ProductPackage
     */
    public function setYearPaquetage($yearPaquetage)
    {
        $this->yearPaquetage = $yearPaquetage;

        return $this;
    }

    /**
     * Get yearPaquetage.
     *
     * @return string
     */
    public function getYearPaquetage()
    {
        return $this->yearPaquetage;
    }
}
