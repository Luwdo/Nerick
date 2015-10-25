<?php
namespace Nerick\PortfolioBundle\API;
use \Nerick\PortfolioBundle\API\Response;
use \Symfony\Component\Validator\Validator\LegacyValidator as DefaultValidator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Validator{
    protected $validator;

    function __construct(DefaultValidator $validator)
    {
	$this->validator = $validator;
    }
    
     /** 
     * This function wraps the validator adding errors and seting the response status appropriately;
     * @param type $entity
     */
    public function validate($response, $entity){
	//this is an array of Symfony\\Component\\Validator\\ConstraintViolation
	$errors = $this->validator->validate($entity);
	if(count($errors) > 0){
	    $response->setStatus(HttpResponse::HTTP_BAD_REQUEST);
	    foreach($errors as $error){
		$response->addError($error->getMessage(), $error->getPropertyPath());
	    }    
	}
	return $response;
    }
}
