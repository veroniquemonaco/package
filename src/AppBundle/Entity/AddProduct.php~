<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AddProduct
 *
 * @ORM\Table(name="add_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddProductRepository")
 */
class AddProduct
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
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\OneToOne(targetEntity="Product")
     */
    private $idProduct;

    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="Taille")
     */
    private $taille;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return AddProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set idProduct
     *
     * @param \AppBundle\Entity\Product $idProduct
     *
     * @return AddProduct
     */
    public function setIdProduct(\AppBundle\Entity\Product $idProduct = null)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return \AppBundle\Entity\Product
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set taille
     *
     * @param \AppBundle\Entity\Taille $taille
     *
     * @return AddProduct
     */
    public function setTaille(\AppBundle\Entity\Taille $taille = null)
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * Get taille
     *
     * @return \AppBundle\Entity\Taille
     */
    public function getTaille()
    {
        return $this->taille;
    }
}
