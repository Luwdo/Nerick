<?php
namespace Nerick\SecurityBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of User
 *
 * @author Luwdo
 */
abstract class User {
	protected $id;
	
	protected $username;
	
	protected $password;
	
	protected $nonce;
	
	protected $failedAttempts;
	
	protected $lastUsed;
	
	protected $locked;
	
	protected $created;
	
	
	
	
}
