<?php
namespace Nerick\PortfolioBundle\Controller;
use \FOS\RestBundle\Controller\FOSRestController;
use \FOS\RestBundle\Controller\Annotations as Rest;
use \Symfony\Component\HttpFoundation\Request;
use \FOS\RestBundle\Request\ParamFetcherInterface;
use \Nerick\PortfolioBundle\API\Response;

/**
 * 
 */
class ArtworkController extends BaseController
{    
    
    /**
     * @Rest\Get(
     *     "/artwork/table"
     * )
     */
    public function getArtworkTableAction(Request $request)//get
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	
	$tableService = $this->get('Nerick.PortfolioBundle.TableService');
	
	$repository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Artwork');
	$qb = $repository->createQueryBuilder('a');
	$excludeArtworks = $request->get('excludeArtworkIds', array());
	$excludeGallery = $request->get('excludeGallery');
	if($excludeGallery){
	    $galleryService = $this->get('Nerick.PortfolioBundle.GalleryService');
	    $galleryArtwork = $galleryService->getGalleryArtkwork($excludeGallery);
	    foreach($galleryArtwork as $artwork){
		if(in_array($artwork->getId(), $excludeArtworks)){
		    continue;
		}
		$excludeArtworks[] = $artwork->getId();
	    }
	}

	if(count($excludeArtworks) > 0){
	    $qb->andWhere($qb->expr()->notIn('a.id', $excludeArtworks));
	}
	$tableService->setQueryBuilder($qb, 'a');
	$tableService->setSerializeData(function($results){
	    $data = array();
	    foreach($results as $result){
		$row = array();
		$row['id'] = $result->getId();
		$row['title'] = $result->getTitle();
		$row['medium'] = $result->getMedium();
		$row['mediaType'] = $result->getMediaTypeLabel();
		$row['yearCompleted'] = $result->getYearCompleted();
		$row['uploadId'] = $result->getUploadId();
		$data[] = $row;
	    }
	    return $data;
	});
	return $tableService->getTable($request);
    }
    
    /**
     * @Rest\Get(
     *     "/artwork/uploads/table"
     * )
     */
    public function getUploadTableAction(Request $request)//get
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	
	$artwordId = $request->get('artworkId', 0);
	$uploadIds = $request->get('uploadIds');
	
	$tableService = $this->get('Nerick.PortfolioBundle.TableService');
	
	$repository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
	$qb = $repository->createQueryBuilder('u');
	$qb->leftJoin('NerickPortfolioBundle:ArtworkUpload', 'uta', \Doctrine\ORM\Query\Expr\Join::WITH, 'u.id = uta.uploadId');
	$qb->andWhere('uta.artworkId = :artworkId');
	$qb->setParameter('artworkId', $artwordId);
	if($uploadIds){
	    $newUploads = array();
	    foreach($uploadIds as $key => $value){
		$newUploads[] = $value;
	    }
	    $qb->orWhere($qb->expr()->in('u.id', $newUploads));
	}
	$tableService->setQueryBuilder($qb, 'u');
	$tableService->setSerializeData(function($results){
	    $data = array();
	    foreach($results as $result){
		$row = array();
		$row['id'] = $result->getId();
		$row['name'] = $result->getName();
		$row['type'] = $result->getType();
		$data[] = $row;
	    }
	    return $data;
	});
	return $tableService->getTable($request);
    }
    
    /**
     * @Rest\Get(
     *     "/artwork/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function getArtworkAction($id)
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$repository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Artwork');
	$artwork = $repository->findOneById($id);
	if (!$artwork) {
	    $response->addError('Artwork not found.');
	    $response->setBadRequest();
	}
	else{
	    $response->setData($artwork);
	}
	return $response;
    }
    
    /**
     * @Rest\Get(
     *     "/artwork",
     * )
     */
    public function getArtworksAction(Request $request)
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$artworkRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Artwork');
	$artwork = array();
	if($request->get('artworkIds')){
	     foreach($request->get('artworkIds', array()) as $artworkId){
		$artwork[] = $artworkRepository->findOneById($artworkId);
	    }
	}
	else{
	    $artwork = $artworkRepository
	    ->createQueryBuilder('a')
	    ->getQuery()
	    ->getResult();
	}
	$response->setData($artwork);
	return $response;
    }
    
    
    public function artworkValidate($response, Request $request){
	if(!$request->get('title')){
	    $response->addError('Title is required.', 'title');
	    $response->setBadRequest();
	}
	
	if(!$request->get('medium')){
	    $response->addError('Medium is required.', 'medium');
	    $response->setBadRequest();
	}
	
	if(!$request->get('yearCompleted')){
	    $response->addError('Year produced is required.', 'yearCompleted');
	    $response->setBadRequest();
	}
	
	if(!$request->get('uploadId') || !is_numeric($request->get('uploadId'))){
	    $response->addError('Please select Primary display upload.');
	    $response->setBadRequest();
	}
	else{
	    $uploadRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
	    $upload = $uploadRepository->findOneById($request->get('uploadId'));
	    if (!$upload) {
		$response->addError('Primary display media cannot be found, please select another.');
		$response->setBadRequest();
	    }
	    $artworkService = $this->get('Nerick.PortfolioBundle.ArtworkService');
	    if($artworkService->getMediaType($upload->getType()) === null){
		$response->addError('Primary display media is not a web viewable type.');
		$response->setBadRequest();
	    }
	}
	
	if($request->get('uploadIds')){
	    foreach($request->get('uploadIds', array()) as $uploadId){
		if(!is_numeric($uploadId)){
		    $response->addError('Added Upload must be valid.');
		    $response->setBadRequest();
		}
	    }
	}
	
	return $response;
    }
    
    
    /**
     * @Rest\Put(
     *     "/artwork"
     * )
     */
    public function addArtworkAction(Request $request)//save
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	//$validator = $this->get('Nerick.PortfolioBundle.API.Validator');
	$response = $this->artworkValidate($response, $request);
	if($response->successful()){
	    //$uploadRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
	    //$primaryUpload = $uploadRepository->findOneById($request->get('uploadId'));
	    $artworkService = $this->get('Nerick.PortfolioBundle.ArtworkService');
	    $em = $this->getDoctrine()->getManager();
	    $artwork = new \Nerick\PortfolioBundle\Entity\Artwork();
	    $artwork->setTitle($request->get('title'));
	    $artwork->setMedium($request->get('medium'));
	    $artwork->setYearCompleted($request->get('yearCompleted'));
	    $artwork->setDescription($request->get('description'));
	    $artwork->setVisible(true);
	    $artwork->setDateCreated(new \DateTime('now'));
	    //$artwork->setUploadId($request->get('uploadId'));
	    //$artwork->setMediaType($artworkService->getMediaType($primaryUpload->getType()));
	    $em->persist($artwork);
	    $em->flush();
	    foreach($request->get('uploadIds', array()) as $uploadId){
		$artworkService->linkUpload($artwork->getId(), $uploadId);
	    }
	    $artworkService->setPrimaryUpload($artwork->getId(), $request->get('uploadId'));
	    $response->addMessage('Artwork created successfully.');
	    $response->setData($artwork->getId());
	}
	return $response;
    }
    
    /**
     * @Rest\Post(
     *     "/artwork/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function editArtworkAction($id, Request $request)//edit
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$artworkRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Artwork');
	$artwork = $artworkRepository->findOneById($id);
	if (!$artwork) {
	    $response->addError('Artwork not found.');
	    $response->setBadRequest();
	}
	//$validator = $this->get('Nerick.PortfolioBundle.API.Validator');
	$response = $this->artworkValidate($response, $request);
	if($response->successful()){
	   // $uploadRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
	   // $primaryUpload = $uploadRepository->findOneById($request->get('uploadId'));
	    $artworkService = $this->get('Nerick.PortfolioBundle.ArtworkService');
	    $em = $this->getDoctrine()->getManager();
	    $artwork->setTitle($request->get('title'));
	    $artwork->setMedium($request->get('medium'));
	    $artwork->setYearCompleted($request->get('yearCompleted'));
	    $artwork->setDescription($request->get('description'));
	    //$artwork->setUploadId($request->get('uploadId'));
	    $artwork->setVisible(true);
	    //$artwork->setMediaType($artworkService->getMediaType($primaryUpload->getType()));
	    $em->persist($artwork);
	    $em->flush();
	    foreach($request->get('uploadIds', array()) as $uploadId){
		$artworkService->linkUpload($artwork->getId(), $uploadId);
	    }
	    $artworkService->setPrimaryUpload($artwork->getId(), $request->get('uploadId'));
	    $response->addMessage('Artwork updated successfully.');
	    $response->setData($artwork->getId());
	}
	return $response;
    }
    
    /**
     * @Rest\Delete(
     *     "/artwork/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function deleteArtworkAction($id)//de;ete
    {
	$response = new Response();
	$response->setBadRequest();
	$response->addError('Not implemented yet.');
	return $response;
	
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