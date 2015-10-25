<?php

namespace Nerick\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArtworkUpload
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ArtworkUpload
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
     * @ORM\Column(name="artworkId", type="integer")
     */
    private $artworkId;

    /**
     * @var integer
     *
     * @ORM\Column(name="uploadId", type="integer")
     */
    private $uploadId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


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
     * Set artworkId
     *
     * @param integer $artworkId
     * @return ArtworkUpload
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
     * Set uploadId
     *
     * @param integer $uploadId
     * @return ArtworkUpload
     */
    public function setUploadId($uploadId)
    {
        $this->uploadId = $uploadId;

        return $this;
    }

    /**
     * Get uploadId
     *
     * @return integer 
     */
    public function getUploadId()
    {
        return $this->uploadId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ArtworkUpload
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
}
