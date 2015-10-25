<?php
namespace Nerick\PortfolioBundle\Controller;
use \FOS\RestBundle\Controller\FOSRestController;
use \FOS\RestBundle\Controller\Annotations as Rest;
//use \JMS\Serializer\SerializerBuilder as JMSserialize;
//use \Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use \Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Symfony\Component\HttpFoundation\Request;
//use \Symfony\Component\HttpFoundation\JsonResponse;
//use \FOS\RestBundle\View\View;
use \Nerick\PortfolioBundle\Entity\User;
use \Nerick\PortfolioBundle\API\Response;

//use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use \Symfony\Component\Security\Core\Exception\AccessDeniedException;
use \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use \Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Description of SecurityController
 *
 * @author Luwdo
 */
class SecurityController extends BaseController
{
    protected function getUserManager()
    {
        return $this->get('fos_user.user_manager');
    }
    
    protected function loginUser(User $user)
    {
        $security = $this->get('security.context');
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $roles = $user->getRoles();
        $token = new UsernamePasswordToken($user, null, $providerKey, $roles);
        $security->setToken($token);
    }
 
    protected function logoutUser()
    {
        $security = $this->get('security.context');
        $token  = new  AnonymousToken (null, new  User());
        $security->setToken($token);
        $this->get('session')->invalidate();
    }
     
    protected function checkUser()
    {
        $security = $this->get('security.context');
        if ($token = $security->getToken()) {
            $user = $token->getUser();
            if ($user instanceof User) {
                return $user;
            }
        }
         
        return  false;
    }
    /**
     * Easy way to check if a user is logged in.
     * @return type
     */
    public function isLoggedin(){
	return $this->checkUser();
    }

    protected function checkUserPassword(User $user, $password)
    {
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        if(!$encoder){
            return  false;
        }
        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }
    
    /**
     * Logs a user in by username and password
     * 
     * @Rest\Post(
     *     "/login"
     * )
     */
    public function loginUserAction(Request $request)//edit
    {
	$response = new Response();
        $username = $request->get('username');
        $password = $request->get('password');
         
        $um = $this->getUserManager();
        $user = $um->findUserByUsername($username);
//        if(!$user){
//            $user = $um->findUserByEmail($username);
//        }
         
        if(!$user instanceof User){
	    $response->setBadRequest();
	    $response->addError('Username is invalid', 'username');
        }else if(!$this->checkUserPassword($user, $password)){
	    $response->setBadRequest();
	    $response->addError('Password is invalid', 'password');
	}
	
	if($response->successful()){
	    $response->addMessage('Login Successfull');
	    $this->loginUser($user);
	    $user->setPassword(null);
	    $response->setData($user);
	} 
	return $response;
    }
    
    
    /**
     * @Rest\Post(
     *     "/logout"
     * )
     */
    public function logoutUserAction()
    {
	$response = new Response();
	$this->logoutUser();
	$response->addMessage('Logout Successfull');
	return $response;
    }
    
    /**
     * Checks if a user is logged in
     * 
     * @Rest\Post(
     *     "/login_check"
     * )
     */
    public function loginCheckAction()
    {
	$response = new Response();
        if ($user = $this->checkUser()) {
	    $user->setPassword(null);
	    $response->setData($user);
	    return $response;
        }
	$response->setData(false);
//	$response->setUnauthorized();
//	$response->addError('Access Denied');
	return $response;
    }
    
    /**
     * @Rest\Post(
     *     "/get_session"
     * )
     */
    public function checkSession()
    {
	print_r($_SESSION);die;
    }
}
