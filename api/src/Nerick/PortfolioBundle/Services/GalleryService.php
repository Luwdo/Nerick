<?php
namespace Nerick\PortfolioBundle\Services;
use \Symfony\Component\DependencyInjection\ContainerAware;
/**
 * Description of UploadService
 *
 * @author Luwdo
 */
class GalleryService extends ContainerAware{
    private $entityManager;
    private $galleryRepository;
    private $artworkRepository;
    private $galleryArtworkRepository;
    
    public function __construct($entityManager, $galleryRepository, $artworkRepository, $galleryArtworkRepository, $container) {
	$this->entityManager = $entityManager;
	$this->galleryRepository = $galleryRepository;
	$this->artworkRepository = $artworkRepository;
	$this->galleryArtworkRepository = $galleryArtworkRepository;
	$this->setContainer($container);
    }
    
    public function linkArtwork($galleryId, $artworkId){
	$artwork = $this->artworkRepository->findOneById($artworkId);
	if (!$artwork) {
	    return false;
	}
	$link = new \Nerick\PortfolioBundle\Entity\GalleryArtwork();
	$link->setGalleryId($galleryId);
	$link->setArtworkId($artworkId);
	$this->entityManager->persist($link);
	$this->entityManager->flush();
    }
    
    public function unlinkArtwork($galleryId, $artworkId){
	$link = $this->galleryArtworkRepository
		->createQueryBuilder('l')
		->where('l.galleryId = :galleryId')->setParameter('galleryId', $galleryId)
		->andWhere('l.artworkId = :artworkId')->setParameter('artworkId', $artworkId)
		->getQuery()
		->getSingleResult();

	if (!$link) {
	    return false;
	}
	
	$this->entityManager->remove($link);
	$this->entityManager->flush();
    }
    
    
    public function getArtworkLinkByOrder($galleryId, $order){
	$link = $this->galleryArtworkRepository
		->createQueryBuilder('l')
		->where('l.galleryId = :galleryId')->setParameter('galleryId', $galleryId)
		->andWhere('l.order = :order')->setParameter('order', $order)
		->getQuery()
		->getSingleResult();

	return $link;
    }
    
    public function getArtworkLink($galleryId, $artworkId){
	$link = $this->galleryArtworkRepository
		->createQueryBuilder('l')
		->where('l.galleryId = :galleryId')->setParameter('galleryId', $galleryId)
		->andWhere('l.artworkId = :artworkId')->setParameter('artworkId', $artworkId)
		->getQuery()
		->getSingleResult();
	
	return $link;
    }
    
    public function getArtworkLinks($galleryId){
	$links = $this->galleryArtworkRepository
		->createQueryBuilder('l')
		->where('l.galleryId = :galleryId')
		->setParameter('galleryId', $galleryId)
		->orderBy('l.order', 'ASC')
		->getQuery()
		->getResult();
	
	return $links;
    }
    
    public function getNextArtwork($galleryId, $artworkId){
	$link = $this->getArtworkLink($galleryId, $artworkId);
	$count = $this->artworkCount($galleryId);
	$nextOrder = $link->getOrder() + 1;
	if($nextOrder > ($count-1)){
	    $nextOrder = 0;
	}
	
	$nextLink = $this->getArtworkLinkByOrder($galleryId, $nextOrder);
	
	return $this->artworkRepository->findOneById($nextLink->getArtworkId());
    }
    
    public function getPreviousArtwork($galleryId, $artworkId){
	$link = $this->getArtworkLink($galleryId, $artworkId);
	$count = $this->artworkCount($galleryId);
	$previousOrder = $link->getOrder() - 1;
	
	if($previousOrder < 0){
	    $previousOrder = ($count-1);
	}
	$previousLink = $this->getArtworkLinkByOrder($galleryId, $previousOrder);
	return $this->artworkRepository->findOneById($previousLink->getArtworkId());
    }
    
    public function artworkCount($galleryId){
	$count = $this->galleryArtworkRepository
		->createQueryBuilder('l')
		->select('count(l.id)')
		->where('l.galleryId = :galleryId')
		->setParameter('galleryId', $galleryId)
		->orderBy('l.order', 'ASC')
		->getQuery()
		->getSingleScalarResult();
	return $count;
    }
    
    
    public function getGalleryArtwork($galleryId){
	$links = $this->galleryArtworkRepository
		->createQueryBuilder('l')
		->where('l.galleryId = :galleryId')
		->setParameter('galleryId', $galleryId)
		->orderBy('l.order', 'ASC')
		->getQuery()
		->getResult();
	
	$artwork = array();
	
	foreach($links as $link){
	    $artwork[] = $this->artworkRepository->findOneById($link->getArtworkId());
	}
	return $artwork;
    }
    
    public function getMainSliderGalleryArtkwork(){
	$galleries = $this->galleryRepository
		    ->createQueryBuilder('g')
		    ->where('g.mainSlider = :mainSlider')
		    ->setParameter('mainSlider', true)
		    ->getQuery()
		    ->getResult();
	$artwork = array();
	foreach($galleries as $gallery){
	    $artwork = array_merge($artwork , $this->getGalleryArtwork($gallery->getId()));
	}
	return $artwork;
    }
    
    public function updateArtworkOrder($galleryId, $order = array()){
	foreach($order as $key => $artworkId){
	    $link = $this->galleryArtworkRepository
		->createQueryBuilder('l')
		->where('l.galleryId = :galleryId')->setParameter('galleryId', $galleryId)
		->andWhere('l.artworkId = :artworkId')->setParameter('artworkId', $artworkId)
		->getQuery()
		->getSingleResult();
	    
	    $link->setOrder($key);	    
	    $this->entityManager->persist($link);
	}
	$this->entityManager->flush();
    }
    
}