<?php
namespace Nerick\PortfolioBundle\Entity;
use \FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use \Symfony\Component\Validator\Constraints as Assert;
use \JMS\Serializer\Annotation\Exclude;
//use \Symfony\Component\Security\Core\User\UserInterface;
//use \Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nerick\PortfolioBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    
//    /**
//     * The salt to use for hashing
//     * @Exclude
//     * @var string
//     */
//    protected $salt;
//
//    /**
//     * Encrypted password. Must be persisted.
//     * @Exclude
//     * @var string
//     */
//    protected $password;
    
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
