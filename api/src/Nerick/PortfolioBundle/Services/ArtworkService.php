<?php
namespace Nerick\PortfolioBundle\Services;
use \Symfony\Component\DependencyInjection\ContainerAware;
/**
 * Description of UploadService
 *
 * @author Luwdo
 */
class ArtworkService extends ContainerAware{
    private $entityManager;
    private $artworkRepository;
    private $uploadRepository;
    private $artworkUploadRepository;
    
    public function __construct($entityManager, $artworkRepository, $uploadRepository, $artworkUploadRepository, $container) {
	$this->entityManager = $entityManager;
	$this->artworkRepository = $artworkRepository;
	$this->uploadRepository = $uploadRepository;
	$this->artworkUploadRepository = $artworkUploadRepository;
	$this->setContainer($container);
    }
    
    public function linkUpload($artworkId, $uploadId, $description = ''){
	$upload = $this->uploadRepository->findOneById($uploadId);
	if (!$upload) {
	    return false;
	}
	$link = new \Nerick\PortfolioBundle\Entity\ArtworkUpload();
	$link->setArtworkId($artworkId);
	$link->setUploadId($uploadId);
	$link->setDescription($description);
	$this->entityManager->persist($link);
	$upload->setTemporary(false);
	$this->entityManager->persist($upload);
	$this->entityManager->flush();
    }
    
    public function setPrimaryUpload($artworkId, $uploadId){
	$primaryUpload = $this->uploadRepository->findOneById($uploadId);
	$primaryUpload->setPublic(true);
	$this->entityManager->persist($primaryUpload);
	
	$artwork = $this->artworkRepository->findOneById($artworkId);
	$artwork->setUploadId($uploadId);
	$artwork->setMediaType($this->getMediaType($primaryUpload->getType()));
	$this->entityManager->persist($artwork);
	
	$links = $this->artworkUploadRepository->createQueryBuilder('l')
		->where('l.artworkId = :artworkId')->setParameter('artworkId', $artworkId)
		->getQuery()->getResult();
	
	$uploadIds = array();
	foreach($links as $link){
	    $uploadIds[] = $link->getUploadId();
	}
	
	$qb = $this->uploadRepository->createQueryBuilder('u');
	$qb->update()->set('u.public', ':public')->setParameter('public', false)
	->andWhere($qb->expr()->in('u.id', $uploadIds))
	->getQuery()->execute();
	
	$this->entityManager->flush();
    }
    
    
    
    public function getMediaType($mimeType){
	$imageList = array(
	    'image/jpeg', 
	    'image/gif', 
	    'image/png',
	    'image/tiff',
	    'image/svg+xml',
	);
	$videoList = array(
	    'video/mp4',
	    'video/ogg',
	    'video/webm',
	);
	if(in_array($mimeType, $imageList)){
	    return \Nerick\PortfolioBundle\Entity\Artwork::IMAGE;
	}
	if(in_array($mimeType, $videoList)){
	    return \Nerick\PortfolioBundle\Entity\Artwork::VIDEO;
	}
	return null;
    }
    
}