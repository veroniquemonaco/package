<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * userOrderByCategory
 *
 * @ORM\Table(name="user_order_by_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserOrderByCategoryRepository")
 */
class UserOrderByCategory
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
     * @ORM\Column(name="yearPaquetage", type="integer")
     */
    private $yearPaquetage;

    /**
     * @var array
     *
     * @ORM\Column(name="arrayByCategory", type="array")
     */
    private $arrayByCategory;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

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
     * Set yearPaquetage.
     *
     * @param int $yearPaquetage
     *
     * @return userOrderByCategory
     */
    public function setYearPaquetage($yearPaquetage)
    {
        $this->yearPaquetage = $yearPaquetage;

        return $this;
    }

    /**
     * Get yearPaquetage.
     *
     * @return int
     */
    public function getYearPaquetage()
    {
        return $this->yearPaquetage;
    }

    /**
     * Set arrayByCategory.
     *
     * @param array $arrayByCategory
     *
     * @return userOrderByCategory
     */
    public function setArrayByCategory($arrayByCategory)
    {
        $this->arrayByCategory = $arrayByCategory;

        return $this;
    }

    /**
     * Get arrayByCategory.
     *
     * @return array
     */
    public function getArrayByCategory()
    {
        return $this->arrayByCategory;
    }
}
