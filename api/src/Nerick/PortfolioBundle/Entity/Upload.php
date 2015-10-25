<?php

namespace Nerick\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Upload
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Upload
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
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255)
     */
    private $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="temporary", type="boolean")
     */
    private $temporary;

    
    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     */
    private $public;
    
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
     * Set type
     *
     * @param string $type
     * @return Upload
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Upload
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
     * Set temporary
     *
     * @param boolean $temporary
     * @return Upload
     */
    public function setTemporary($temporary)
    {
        $this->temporary = $temporary;

        return $this;
    }

    
    /**
     * Get temporary
     *
     * @return boolean 
     */
    public function getTemporary()
    {
        return $this->temporary;
    }
    
    /**
     * Set public
     *
     * @param boolean $public
     * @return Upload
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }
    
    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Upload
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Upload
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return \Upload
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return String
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    
    public function getPath(){
	return self::getFileRoot().$this->file;
    }
    
    public function deleteFile(){
	return unlink($this->getPath());
    }
    
    public static function getFileRoot(){
	$dir = dirname(__FILE__).'/../../../../var/uploads/';
	if (!is_dir($dir))
	    mkdir($dir, 0777, true);
	return $dir;
    }
}
