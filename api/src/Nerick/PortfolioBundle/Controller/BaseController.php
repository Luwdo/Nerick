<?php
namespace Nerick\PortfolioBundle\Controller;
use \FOS\RestBundle\Controller\FOSRestController;
use \Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of BaseController
 *
 * @author Luwdo
 */
class BaseController extends FOSRestController{
    
    public function setContainer(ContainerInterface $container = null) {
	$result = parent::setContainer($container);
	$this->init();
	return $result;
    }
    
    public function init()
    {
    }
}
