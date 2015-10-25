<?php

namespace Nerick\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GalleryArtwork
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class GalleryArtwork
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
     * @var integer
     *
     * @ORM\Column(name="galleryId", type="integer")
     */
    private $galleryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="artworkId", type="integer")
     */
    private $artworkId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="integer", nullable=true)
     */
    private $order;


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
     * Set galleryId
     *
     * @param integer $galleryId
     * @return GalleryArtwork
     */
    public function setGalleryId($galleryId)
    {
        $this->galleryId = $galleryId;

        return $this;
    }

    /**
     * Get galleryId
     *
     * @return integer 
     */
    public function getGalleryId()
    {
        return $this->galleryId;
    }

    /**
     * Set artworkId
     *
     * @param integer $artworkId
     * @return GalleryArtwork
     */
    public function setArtworkId($artworkId)
    {
        $this->artworkId = $artworkId;

        return $this;
    }

    /**
     * Get artworkId
     *
     * @return integer 
     */
    public function getArtworkId()
    {
        return $this->artworkId;
    }
    
    /**
     * Set order
     *
     * @param integer $order
     * @return GalleryArtwork
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
