<?php
namespace Nerick\PortfolioBundle\Security;
use \Nerick\PortfolioBundle\API\Response as NerickResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use \Symfony\Component\Security\Core\Exception\AuthenticationException;
use \Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use \Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
//this doesn't really work, could but is it worth it?
class AuthenticationRestHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface {
    //TODO BIG ISSUE FIXY
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
	$response = new NerickResponse();
	$response->setUnauthorized();
	$response->setData('fail test');
	//die('test');
	//print_r($exception);die;
	
	//JMS is never called on this response because it happens outside of the fosrest handler
        return new Response('what the hell i got to authentication failed???', Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
	$response = new NerickResponse();
	$response->setStatus(Response::HTTP_NO_CONTENT);
	$response->setData('Nothing');
	
	$user = $token->getUser();
	//continue testing, i have a feeling this will infact come into play
        return new Response('Authentication Success', Response::HTTP_NO_CONTENT);
    }
}