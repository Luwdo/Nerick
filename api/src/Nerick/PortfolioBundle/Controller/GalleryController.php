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
class GalleryController extends BaseController
{
    /**
     * @Rest\Get(
     *     "/gallery/table"
     * )
     */
    public function getGalleryTableAction(Request $request)//get
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	
	$tableService = $this->get('Nerick.PortfolioBundle.TableService');
	
	$repository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Gallery');
	$qb = $repository->createQueryBuilder('a');
	$tableService->setQueryBuilder($qb, 'a');
	$tableService->setSerializeData(function($results){
	    $data = array();
	    foreach($results as $result){
		$row = array();
		$row['id'] = $result->getId();
		$row['title'] = $result->getTitle();
		$data[] = $row;
	    }
	    return $data;
	});
	return $tableService->getTable($request);
    }
    
//    /**
//     * @Rest\Get(
//     *     "/gallery/artwork/table"
//     * )
//     */
//    public function getArtworkTableAction(Request $request)//get
//    {
//	$response = new Response();
//	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
//	   $response->setForbidden();
//	   return $response;
//	}
//	
//	$artwordId = $request->get('artworkId', 0);
//	$uploadIds = $request->get('uploadIds');
//	
//	$tableService = $this->get('Nerick.PortfolioBundle.TableService');
//	
//	$repository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
//	$qb = $repository->createQueryBuilder('u');
//	$qb->leftJoin('NerickPortfolioBundle:ArtworkUpload', 'uta', \Doctrine\ORM\Query\Expr\Join::WITH, 'u.id = uta.uploadId');
//	$qb->andWhere('uta.artworkId = :artworkId');
//	$qb->setParameter('artworkId', $artwordId);
//	if($uploadIds){
//	    $newUploads = array();
//	    foreach($uploadIds as $key => $value){
//		$newUploads[] = $value;
//	    }
//	    $qb->orWhere($qb->expr()->in('u.id', $newUploads));
//	}
//	$tableService->setQueryBuilder($qb, 'u');
//	$tableService->setSerializeData(function($results){
//	    $data = array();
//	    foreach($results as $result){
//		$row = array();
//		$row['id'] = $result->getId();
//		$row['name'] = $result->getName();
//		$row['type'] = $result->getType();
//		$data[] = $row;
//	    }
//	    return $data;
//	});
//	return $tableService->getTable($request);
//    }
    
    
    
    
    
    /**
     * @Rest\Get(
     *     "/gallery/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function getGalleryAction($id)
    {
	$response = new Response();
//	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
//	   $response->setForbidden();
//	   return $response;
//	}
	$repository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Gallery');
	$gallery = $repository->findOneById($id);
	if (!$gallery) {
	    $response->addError('Gallery not found.');
	    $response->setBadRequest();
	}
	else{
	    $response->setData($gallery);
	}
	return $response;
    }
    
        /**
     * @Rest\Get(
     *     "/gallery/mainslider"
     * )
     */
    public function getMainSliderGalleryArtkwork()
    {
	$response = new Response();
	$galleryService = $this->get('Nerick.PortfolioBundle.GalleryService');
	$artwork = $galleryService->getMainSliderGalleryArtkwork();
	$response->setData($artwork);
	return $response;
    }
    
    /**
     * @Rest\Get(
     *     "/gallery",
     * )
     */
    public function getGalleriesAction(Request $request)
    {
	$response = new Response();
//	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
//	   $response->setForbidden();
//	   return $response;
//	}
	$galleryService = $this->get('Nerick.PortfolioBundle.GalleryService');
	$galleryRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Gallery');
	$qb = $galleryRepository->createQueryBuilder('g');
	if($request->get('menuVisible', false)){
	    $qb->andWhere('g.menuVisible = :menuVisible')->setParameter('menuVisible', true);
	}
	$galeries = $qb->getQuery()->getResult();
	$response->setData($galeries);
	return $response;
    }
    
    /**
     * @Rest\Get(
     *     "/gallery/{id}/artwork",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function getGalleryArtworkAction($id, Request $request)
    {
	//TODO: Gaurd Clauses
	$response = new Response();
//	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
//	   $response->setForbidden();
//	   return $response;
//	}
	$galleryService = $this->get('Nerick.PortfolioBundle.GalleryService');
	$artworkRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Artwork');
	$artwork = $galleryService->getGalleryArtwork($id);
	
	//request spefic artwork ontop of the ones in the galler (usefull? maybe maybe not)
	if($request->get('artworkIds')){
	     foreach($request->get('artworkIds', array()) as $artworkId){
		$artwork[] = $artworkRepository->findOneById($artworkId);
	    }
	}
	$response->setData($artwork);
	return $response;
    }
    
    public function galleryValidate($response, Request $request){
	if(!$request->get('title')){
	    $response->addError('Title is required.', 'title');
	    $response->setBadRequest();
	}
	
	if($request->get('artworkIds')){
	    foreach($request->get('artworkIds', array()) as $artworkId){
		if(!is_numeric($artworkId)){
		    $response->addError('Added Artwork must be valid.');
		    $response->setBadRequest();
		}
	    }
	}
	
	return $response;
    }
    
    
    /**
     * @Rest\Put(
     *     "/gallery"
     * )
     */
    public function addGalleryAction(Request $request)//save
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	//$validator = $this->get('Nerick.PortfolioBundle.API.Validator');
	$response = $this->galleryValidate($response, $request);
	if($response->successful()){
	    $galleryService = $this->get('Nerick.PortfolioBundle.GalleryService');
	    $em = $this->getDoctrine()->getManager();
	    $gallery = new \Nerick\PortfolioBundle\Entity\Gallery();
	    $gallery->setTitle($request->get('title'));
	    $gallery->setDescription($request->get('description'));
	    $gallery->setMainSlider($request->get('mainSlider', false));
	    $gallery->setMenuVisible($request->get('menuVisible', false));
	    $gallery->setDateCreated(new \DateTime('now'));
	    $em->persist($gallery);
	    $em->flush();
	    foreach($request->get('artworkIds', array()) as $artworkId){
		$galleryService->linkArtwork($gallery->getId(), $artworkId);
	    }
	    $galleryService->updateArtworkOrder($gallery->getId(), $request->get('artworkOrder', array()));
	    $response->addMessage('Gallery created successfully.');
	    $response->setData($gallery->getId());
	}
	return $response;
    }
    
    /**
     * @Rest\Post(
     *     "/gallery/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function editGalleryAction($id, Request $request)//edit
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$galleryRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Gallery');
	$gallery = $galleryRepository->findOneById($id);
	if (!$gallery) {
	    $response->addError('Gallery not found.');
	    $response->setBadRequest();
	}
	//$validator = $this->get('Nerick.PortfolioBundle.API.Validator');
	$response = $this->galleryValidate($response, $request);
	if($response->successful()){
	    $galleryService = $this->get('Nerick.PortfolioBundle.GalleryService');
	    $em = $this->getDoctrine()->getManager();
	    $gallery->setTitle($request->get('title'));
	    $gallery->setDescription($request->get('description'));
	    $gallery->setMainSlider($request->get('mainSlider', false));
	    $gallery->setMenuVisible($request->get('menuVisible', false));
	    $em->persist($gallery);
	    $em->flush();
	    foreach($request->get('artworkIds', array()) as $artworkId){
		$galleryService->linkArtwork($gallery->getId(), $artworkId);
	    }
	    $galleryService->updateArtworkOrder($gallery->getId(), $request->get('artworkOrder', array()));
	    $response->addMessage('Gallery updated successfully.');
	    $response->setData($gallery->getId());
	}
	return $response;
    }
    
    /**
     * @Rest\Get(
     *     "/gallery/{galleryId}/artwork/{artworkId}",
     *     requirements = {
     *         "galleryId": "\d+",
     *	       "artworkId": "\d+"
     *     }
     * )
     */
    public function getGallerySingleArtworkAction($galleryId, $artworkId)
    {
	$response = new Response();
	$galleryService = $this->get('Nerick.PortfolioBundle.GalleryService');
	
	$link = $galleryService->getArtworkLink($galleryId, $artworkId);
	
	if(!$link){
	    $response->setBadRequest();
	    $response->addError('No link found.');
	    return $response;
	}
	
	$artworkRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Artwork');
	$artwork = $artworkRepository->findOneById($artworkId);
	
	$response->setData(array(
	    'artwork' => $artwork,
	    'nextArtworkId' => $galleryService->getNextArtwork($galleryId, $artworkId)->getId(),
	    'previousArtworkId' => $galleryService->getPreviousArtwork($galleryId, $artworkId)->getId()
	));
	return $response;
    }
    
    /**
     * @Rest\Delete(
     *     "/gallery/{id}/artwork/{artworkId}",
     *     requirements = {
     *         "id": "\d+",
     *	       "artworkId": "\d+"
     *     }
     * )
     */
    public function deleteGalleryArtworkAction($id, $artworkId)//delete
    {
	$response = new Response();
	
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$galleryService = $this->get('Nerick.PortfolioBundle.GalleryService');
	
	$galleryService->unlinkArtwork($id, $artworkId);
	$response->addMessage('Artwork removed successfully.');
	return $response;
    }
    
    /**
     * @Rest\Delete(
     *     "/gallery/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function deleteGalleryAction($id)//de;ete
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