<?php

namespace Nerick\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gallery
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Gallery
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="mainSlider", type="boolean")
     */
    private $mainSlider;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="menuVisible", type="boolean")
     */
    private $menuVisible;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Gallery
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Gallery
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set mainSlider
     *
     * @param boolean $mainSlider
     * @return Gallery
     */
    public function setMainSlider($mainSlider)
    {
        $this->mainSlider = $mainSlider;

        return $this;
    }

    /**
     * Get mainSlider
     *
     * @return boolean 
     */
    public function getMainSlider()
    {
        return $this->mainSlider;
    }
    
    /**
     * Set menuVisible
     *
     * @param boolean $menuVisible
     * @return Gallery
     */
    public function setMenuVisible($menuVisible)
    {
        $this->menuVisible = $menuVisible;

        return $this;
    }

    /**
     * Get menuVisible
     *
     * @return boolean 
     */
    public function getMenuVisible()
    {
        return $this->menuVisible;
    }
    
    
     /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Gallery
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}
