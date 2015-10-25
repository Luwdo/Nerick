<?php
namespace Nerick\PortfolioBundle\Security;
use \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class NerickPasswordEncoder implements PasswordEncoderInterface{
    /**
     * Encodes the raw password.
     *
     * @param string $raw  The password to encode
     * @param string $salt The salt
     *
     * @return string The encoded password
     */
    public function encodePassword($raw, $salt){
	$numberOfHashLoops = (strlen($raw)+1)*42;
	$hash = $raw;
        for($i = 0; $i < $numberOfHashLoops; $i++){
            if ($i % 2 != 0) {
                $hash = hash('sha512', $hash.$salt);
            }
            else{
                $hash = hash('sha512', $hash);
            }
        }
	return $hash;
    }

    /**
     * Checks a raw password against an encoded password.
     *
     * @param string $encoded An encoded password
     * @param string $raw     A raw password
     * @param string $salt    The salt
     *
     * @return bool true if the password is valid, false otherwise
     */
    public function isPasswordValid($encoded, $raw, $salt){
	$encodedRaw = $this->encodePassword($raw, $salt);	
	return $encodedRaw == $encoded;
    }
}
