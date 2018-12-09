<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fournisseur", type="string", length=255)
     */
    private $fournisseur;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     *
     */
    private $category;

    /**
     * @var
     * @ORM\OneToOne(targetEntity="Media")
     *
     */
    private $image;

    /**
     * @var
     * @ORM\ManyToMany(targetEntity="Taille", inversedBy="products")
     *
     */
    private $tailles;

    /**
     * @var
     * @ORM\ManyToMany(targetEntity="Qualification", inversedBy="products")
     */
    private $qualifications;

    /**
     * @return bool
     */
    public function isActif()
    {
        return $this->actif;
    }

    /**
     * @param bool $actif
     */
    public function setActif($actif)
    {
        $this->actif = $actif;
    }

    /**
     * @var boolean
     *
     *  @ORM\Column(name="actif", type="boolean")
     */
    private $actif;

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getTailles()
    {
        return $this->tailles;
    }

    /**
     * @param mixed $tailles
     */
    public function setTailles($tailles)
    {
        $this->tailles = $tailles;
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fournisseur
     *
     * @param string $fournisseur
     *
     * @return Product
     */
    public function setFournisseur($fournisseur)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return string
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return Product
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix()
    {
        return $this->prix;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tailles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add taille
     *
     * @param \AppBundle\Entity\Taille $taille
     *
     * @return Product
     */
    public function addTaille(\AppBundle\Entity\Taille $taille)
    {
        $this->tailles[] = $taille;

        return $this;
    }

    /**
     * Remove taille
     *
     * @param \AppBundle\Entity\Taille $taille
     */
    public function removeTaille(\AppBundle\Entity\Taille $taille)
    {
        $this->tailles->removeElement($taille);
    }

    /**
     * Add qualification
     *
     * @param \AppBundle\Entity\Qualification $qualification
     *
     * @return Product
     */
    public function addQualification(\AppBundle\Entity\Qualification $qualification)
    {
        $this->qualifications[] = $qualification;

        return $this;
    }

    /**
     * Remove qualification
     *
     * @param \AppBundle\Entity\Qualification $qualification
     */
    public function removeQualification(\AppBundle\Entity\Qualification $qualification)
    {
        $this->qualifications->removeElement($qualification);
    }

    /**
     * Get qualifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQualifications()
    {
        return $this->qualifications;
    }
}
