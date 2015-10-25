<?php

namespace Nerick\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Artwork
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Artwork
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
     * @ORM\Column(name="mediaType", type="integer")
     */
    private $mediaType;

    /**
     * @var string
     *
     * @ORM\Column(name="medium", type="string", length=255)
     */
    private $medium;

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
     * @var integer
     *
     * @ORM\Column(name="uploadId", type="integer")
     */
    private $uploadId;

    /**
     * @var string
     *
     * @ORM\Column(name="yearCompleted", type="string", length=4)
     */
    private $yearCompleted;
    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;
    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    //media types
    const IMAGE = 0;
    const VIDEO = 1;

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
     * Set mediaType
     *
     * @param integer $mediaType
     * @return Artwork
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    /**
     * Get mediaType
     *
     * @return integer 
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }
    
    /**
     * Get getMediaTypeLabel
     *
     * @return string 
     */
    public function getMediaTypeLabel()
    {
	$labels = array(
	    self::IMAGE => 'Image',
	    self::VIDEO => 'Video'
	);
	return $labels[$this->mediaType];
    }

    /**
     * Set medium
     *
     * @param string $medium
     * @return Artwork
     */
    public function setMedium($medium)
    {
        $this->medium = $medium;

        return $this;
    }

    /**
     * Get medium
     *
     * @return string 
     */
    public function getMedium()
    {
        return $this->medium;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Artwork
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
     * @return Artwork
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
     * Set uploadId
     *
     * @param integer $uploadId
     * @return Artwork
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
     * Set yearCompleted
     *
     * @param string $yearCompleted
     * @return Artwork
     */
    public function setYearCompleted($yearCompleted)
    {
        $this->yearCompleted = $yearCompleted;

        return $this;
    }

    /**
     * Get yearCompleted
     *
     * @return string 
     */
    public function getYearCompleted()
    {
        return $this->yearCompleted;
    }
    
    /**
    * Set isVisible
    *
    * @param boolean $visible
    * @return Artwork
    */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Artwork
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
