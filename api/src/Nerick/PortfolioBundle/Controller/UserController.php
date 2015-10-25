<?php
namespace Nerick\PortfolioBundle\Controller;
use \FOS\RestBundle\Controller\FOSRestController;
use \FOS\RestBundle\Controller\Annotations as Rest;
use \Symfony\Component\HttpFoundation\Request;
use \Nerick\PortfolioBundle\API\Response;

/**
 * 
 */
class UserController extends BaseController
{
    /**
     * @Rest\Get(
     *     "/users/table"
     * )
     */
    public function getUsersTableAction(Request $request)//get
    {
	//die('test');
	//return $request->query->all();
	
	//die('haha test');
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$columns = $request->get('columns');
	$draw = $request->get('draw');
	$limit = $request->get('length');
	$start = $request->get('start');
	$order = $request->get('order');
	$search = $request->get('search');

	$repository = $this->getDoctrine()->getRepository('Nerick\PortfolioBundle\Entity\User');
	$query = $repository->createQueryBuilder('u');
	
	//---LIMIT---
	if ($start && $limit != -1) {
	    $query->setFirstResult($start)->setMaxResults($limit);
	}
	//--ORDER--
	foreach($order as $index => $columnOrder){
	    $columnIndex = intval($columnOrder['column']);
	    if($columns[$columnIndex]['orderable'] == 'true'){
		$columnName = $columns[$columnIndex]['data'];
		if($columnOrder['dir'] === 'asc'){
		   $query->orderBy('u.'.$columnName, 'ASC');
		}else{
		   $query->orderBy('u.'.$columnName, 'DESC');
		}
	    }
	}
	//--SEARCH--
	$searchs = array();
	
	if($search && $search['value'] != ''){
	    $query->setParameter('gobalSearch', '%'.$search['value'].'%');
	    foreach($columns as $index => $column){
		if($column['searchable'] == 'true'){
		    $columnName = $column['data'];
		    $searchs[] = 'u.'.$columnName.' LIKE :gobalSearch';
		}
	    }
	    //andWhere(new Expr\Orx($aLike));
	    $gString = '( '.implode(' OR ', $searchs).' )';
	    
	    $query->where($gString);
	}
	
	//these are ands
//	// Individual column filtering
//	for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
//		$requestColumn = $request['columns'][$i];
//		$columnIdx = array_search( $requestColumn['data'], $dtColumns );
//		$column = $columns[ $columnIdx ];
//		$str = $requestColumn['search']['value'];
//		if ( $requestColumn['searchable'] == 'true' &&
//		 $str != '' ) {
//			$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
//			$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
//		}
//	}
	$query = $query->getQuery();
	$users = $query->getResult();
	
	//return $users;
	
	$data = array();
	foreach($users as $user){
	    $row = array();
	    $row['id'] = $user->getId();
	    $row['username'] = $user->getUsername();
	    $row['email'] = $user->getEmail();
	    $data[] = $row;
	}
	
	$totalRecords = $repository->createQueryBuilder('u')->select('COUNT(u)')->getQuery()->getSingleScalarResult();
	
	return array(
		"draw" => intval($draw),
		"recordsTotal" => intval($totalRecords),
		"recordsFiltered" => count($users),
		"data" => $data
	);
    }
    
    /**
     * @Rest\Get(
     *     "/users/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function getUserAction($id)
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$repository = $this->getDoctrine()->getRepository('Nerick\PortfolioBundle\Entity\User');
	$user = $repository->findOneById($id);
	if (!$user) {
	    $response->addError('User not found.');
	    $response->setBadRequest();
	}
	else{
	    $user->setPassword(null);
	    $response->setData($user);
	}
	return $response;
    }
    
    /**
     * @Rest\Put(
     *     "/users"
     * )
     */
    public function addUserAction(Request $request)//save
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	
	$userManager = $this->container->get('fos_user.user_manager');
	$validator = $this->get('Nerick.PortfolioBundle.API.Validator');
	
	$password = $request->get('password');
	$confirmPassword = $request->get('confirmPassword');
	if(!$password){
	    $response->setBadRequest();
	    $response->addError('Password is required.', 'password');
	}
	if(!$confirmPassword){
	   $response->setBadRequest();
	   $response->addError('Confirm Password is required.', 'confirmPassword');
	}
	if($password != $confirmPassword){
	    $response->setBadRequest();
	    $response->addError('Password and Confirm Password do not match.');
	}
	
	if(!$request->get('username')){
	    $response->setBadRequest();
	    $response->addError('Username is required.', 'username');
	}
	
	if(!$request->get('email')){
	    $response->setBadRequest();
	    $response->addError('Email is required.', 'email');
	}
	$checkUser = $userManager->findUserByUsername($request->get('username'));
	if($checkUser){
	    $response->setBadRequest();
	    $response->addError('User with that username already exists.');
	}
	$checkUser = $userManager->findUserByEmail($request->get('email'));
	if($checkUser){
	    $response->setBadRequest();
	    $response->addError('User with that email already exists.');
	}
	if($response->successful()){
	    $user = $userManager->createUser();
	    $user->setUsername($request->get('username'));
	    $user->setEmail($request->get('email'));
	    $user->setPlainPassword($password);
	    $user->setEnabled(true);
	    $user->addRole('ROLE_SUPER_ADMIN');
	    $userManager->updateUser($user, true);
	    $response->setData($user->getId());
	    $response->addMessage('User created successfully.');
	}
	return $response;
    }
    
    /**
     * @Rest\Post(
     *     "/users/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function editUserAction($id, Request $request)//edit
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	
	$userManager = $this->container->get('fos_user.user_manager');
	$validator = $this->get('Nerick.PortfolioBundle.API.Validator');
	
	$password = $request->get('password');
	$confirmPassword = $request->get('confirmPassword');
	
	if($password){	    
	    if(!$confirmPassword){
	       $response->setBadRequest();
	       $response->addError('Confirm Password is required.', 'confirmPassword');
	    }
	    if($password != $confirmPassword){
		$response->setBadRequest();
		$response->addError('Password and Confirm Password do not match.');
	    }
	}
	
	if(!$request->get('username')){
	    $response->setBadRequest();
	    $response->addError('Username is required.', 'username');
	}
	
	if(!$request->get('email')){
	    $response->setBadRequest();
	    $response->addError('Email is required.', 'email');
	}
	
	$checkUser = $userManager->findUserByUsername($request->get('username'));
	if($checkUser){
	    $response->setBadRequest();
	    $response->addError('User with that username already exists.');
	}
	$checkUser = $userManager->findUserByEmail($request->get('email'));
	if($checkUser){
	    $response->setBadRequest();
	    $response->addError('User with that email already exists.');
	}
	
	$user = $userManager->findUserBy(array('id' => $request->get('id')));
	if(!$user){
	    $response->setBadRequest();
	    $response->addError('User not found.');
	}
	if($response->successful()){
	    $user->setUsername($request->get('username'));
	    $user->setEmail($request->get('email'));
	    if($password){
		$user->setPlainPassword($password);
	    }
	    //$user->setEnabled(true);
	    //$user->addRole('ROLE_SUPER_ADMIN');
	    $userManager->updateUser($user, true);
	    $response->addMessage('User updated successfully.');
	}
	return $response;
    }
    
    /**
     * @Rest\Delete(
     *     "/users/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function deleteUserAction($id)//de;ete
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$userManager = $this->container->get('fos_user.user_manager');
	$user = $userManager->findUserBy(array('id' => $id));
	if(!$user){
	    $response->setBadRequest();
	    $response->addError('User not found.');
	}
	if($response->successful()){
	    $userManager->deleteUser($user);
	    $response->addMessage('User deleted successfully.');
	}
	return $response;
    }
}