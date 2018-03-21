<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 */
class Cart
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
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AddProduct", mappedBy="id")
     */
    private $addProduct;


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
     * Set amount
     *
     * @param float $amount
     *
     * @return Cart
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addProduct = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add addProduct
     *
     * @param \AppBundle\Entity\AddProduct $addProduct
     *
     * @return Cart
     */
    public function addAddProduct(\AppBundle\Entity\AddProduct $addProduct)
    {
        $this->addProduct[] = $addProduct;

        return $this;
    }

    /**
     * Remove addProduct
     *
     * @param \AppBundle\Entity\AddProduct $addProduct
     */
    public function removeAddProduct(\AppBundle\Entity\AddProduct $addProduct)
    {
        $this->addProduct->removeElement($addProduct);
    }

    /**
     * Get addProduct
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddProduct()
    {
        return $this->addProduct;
    }
}
