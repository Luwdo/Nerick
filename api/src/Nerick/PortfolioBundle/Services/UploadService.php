<?php
namespace Nerick\PortfolioBundle\Services;
use \Symfony\Component\DependencyInjection\ContainerAware;
/**
 * Description of UploadService
 *
 * @author Luwdo
 */
class UploadService extends ContainerAware{
    private $uploadRepository;
    private $artworkUploadRepository;
    
    public function __construct($uploadRepository, $artworkUploadRepository, $container) {
	$this->uploadRepository = $uploadRepository;
	$this->artworkUploadRepository = $artworkUploadRepository;
	$this->setContainer($container);
    }
    
    public function deleteArkworkLinks($upload){
	$qb = $this->artworkUploadRepository->createQueryBuilder('l');
	$qb->delete()->Where('l.uploadId = :uploadId');
	$qb->setParameter('uploadId', $upload->getId());
	$qb->getQuery()->execute();
    }
    
}
