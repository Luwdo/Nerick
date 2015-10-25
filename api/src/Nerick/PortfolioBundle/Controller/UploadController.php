<?php
namespace Nerick\PortfolioBundle\Controller;
use \FOS\RestBundle\Controller\FOSRestController;
use \FOS\RestBundle\Controller\Annotations as Rest;
use \Symfony\Component\HttpFoundation\Request;
use \Nerick\PortfolioBundle\API\Response;
use \Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use \Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

/**
 * Description of UploadController
 *
 * @author Luwdo
 */
class UploadController extends BaseController
{
    const MAX_FILE_SIZE = 1048576;
//    private $uploadRepository;
//    private $uploadService;
//    private $securityController;
//    
//    public function __contruct(){
//	$this->securityController = $this->get('Nerick.PortfolioBundle.SecurityController');
//	$this->uploadService = $this->get('Nerick.PortfolioBundle.UploadService');
//	$this->uploadRepository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
//    }
    
    public function getFileRoot(){
	return \Nerick\PortfolioBundle\Entity\Upload::getFileRoot();
    }
    
    public function getFileTempRoot(){
	$dir = dirname(__FILE__).'/../../../../var/temp/';
	if (!is_dir($dir))
	    mkdir($dir, 0777, true);
	return $dir;
    }
    
    public function sanitizeValue($value){
	$value = str_replace('\\', '_', $value);
	$value = str_replace('/', '_', $value);
	$value = str_replace('..', '_', $value);
	return $value;
    }
    
    public function validMimeTypes(){
	  return array(
	      'image/jpeg', 
	      'image/gif', 
	      'image/png',
	      'image/tiff',
	      'image/svg+xml',
	      'video/mp4',
	      'video/ogg',
	      'video/webm',
	      'application/octet-stream');
    }
    
    
    /**
     * returns true if file was created false if it needs more chunks
     * @param type $temp_dir
     * @param type $wholeFileName
     * @param type $clientFileName
     * @param type $chunkSize
     * @param type $totalSize
     * @return boolean
     */
    public function createFileFromChunks($temp_dir, $wholeFilePath, $clientFileName, $chunkSize, $totalSize) {

	// count all the parts of this file
	$total_files = 0;
	foreach(scandir($temp_dir) as $file) {
	    if (stripos($file, $clientFileName) !== false) {
		$total_files++;
	    }
	}

	// check that all the parts are present
	// the size of the last part is between chunkSize and 2*$chunkSize
	if ($total_files * $chunkSize >=  ($totalSize - $chunkSize + 1)) {

	    // create the final destination file
	    //if (($fp = fopen('temp/'.$fileName, 'w')) !== false) {
	    if (($fp = fopen($wholeFilePath, 'w')) !== false) {
		for ($i=1; $i<=$total_files; $i++) {
		    fwrite($fp, file_get_contents($temp_dir.'/'.$clientFileName.'.part'.$i));
		    //_log('writing chunk '.$i);
		}
		fclose($fp);
	    } else {
		//_log('cannot create the destinatqion file');
		return false;
	    }

	    // rename the temporary directory (to avoid access from other 
	    // concurrent chunks uploads) and than delete it
	    if (rename($temp_dir, $temp_dir.'_UNUSED')) {
		$this->rrmdir($temp_dir.'_UNUSED');
	    } else {
		$this->rrmdir($temp_dir);
	    }
	    return true;
	}
	return false;
    }
    
    public function rrmdir($dir) {
	if (is_dir($dir)) {
	    $objects = scandir($dir);
	    foreach ($objects as $object) {
		if ($object != "." && $object != "..") {
		    if (filetype($dir . "/" . $object) == "dir") {
			$this->rrmdir($dir . "/" . $object); 
		    } else {
			unlink($dir . "/" . $object);
		    }
		}
	    }
	    reset($objects);
	    rmdir($dir);
	}
    }
    
    /**
     * @Rest\Get(
     *     "/uploads/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function getUploadAction($id)
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$repository =  $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
	$upload = $repository->findOneById($id);
	if (!$upload) {
	    $response->addError('Upload not found.');
	    $response->setBadRequest();
	}
	else{
	    $response->setData($upload);
	}
	return $response;
    }
    
    /**
     * @Rest\Get(
     *     "/uploads/{id}/serve",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     * @Rest\Head(
     *     "/uploads/{id}/serve",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function getUploadServeAction($id, Request $request)
    {	
	$repository = $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
	$upload = $repository->findOneById($id);
	if (!$upload) {
	    header('HTTP/1.1 404 Not Found');
	    echo 'File not found.';
	    exit();
	}
	if($upload->getPublic() == false && !$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	    header('HTTP/1.1 403 Forbidden');
	    echo 'File not found.';
	    exit();
	}
	$path = $upload->getPath();
	//pre(realpath($path));die;
	ob_start();
	header('Content-Type: '.$upload->getType());
	header('Content-Disposition: inline; filename="'.$upload->getFile().'"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . filesize($path));
	ob_clean();
	flush();
	if($request->getRealMethod() == Request::METHOD_GET){
	    readfile($path);
	}
	exit();
    }
    
    
    /**
     * @Rest\Post(
     *     "/uploads"
     * )
     */
    public function addUploadAction(Request $request)//save
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	
	$files = $request->files->all();
	$filesCount = $request->files->count();
	
	
	$flowFilename = $request->get('flowFilename');
	$flowChunkNumber = $request->get('flowChunkNumber');
	$flowChunkSize = $request->get('flowChunkSize');
	$flowTotalSize = $request->get('flowTotalSize');
	
	if(!$flowFilename){
	    $response->addError('File name missing.');
	    $response->setBadRequest();
	}
	
	if(!$flowChunkNumber){
	    $response->addError('Chunk number missing.');
	    $response->setBadRequest();
	}
	
	if(!$flowChunkSize){
	    $response->addError('Chunk size missing.');
	    $response->setBadRequest();
	}
	
	if(!$flowTotalSize){
	    $response->addError('File size missing.');
	    $response->setBadRequest();
	}
	
	if(!is_numeric($flowTotalSize)){
	    $response->addError('Invalid file size.');
	    $response->setBadRequest();
	}
	
	if($response->successful()){
	    if(($flowTotalSize/ 1024) > self::MAX_FILE_SIZE){
		$response->addError('File size cannot exceed '.(self::MAX_FILE_SIZE/1024).' mb.');
		$response->setBadRequest();
	    }
	}
	
	if($filesCount > 0 && $response->successful()){
	    $flowFilename = $this->sanitizeValue($flowFilename);
	    $flowChunkNumber = $this->sanitizeValue($flowChunkNumber);
	    
	    foreach ($files as $file) {
		
		// check the error status
		if ($file->getError() != 0) {
		    $response->addError('error '.$file->getError().' in file '.$flowFilename);
		    continue;
		}
		
		$mimeType = $file->getMimeType();
		
		if(!in_array($mimeType, $this->validMimeTypes())){
		    $response->addError('Mime Type:'.$mimeType.' is not allowed');
		    $response->setBadRequest();
		    continue;
		}
		if(($file->getClientSize()/ 1024) > self::MAX_FILE_SIZE){
		    $response->addError('File size cannot exceed '.(self::MAX_FILE_SIZE/1024).' mb.');
		    $response->setBadRequest();
		    continue;
		}
		
		$temp_dir = $this->getFileTempRoot().$request->get('flowIdentifier');
		$dest_file = $temp_dir.'/'.$flowFilename.'.part'.$flowChunkNumber;

		// create the temporary directory
		if (!is_dir($temp_dir)) {
		    mkdir($temp_dir, 0777, true);
		}
		
		// move the temporary file
		if (!move_uploaded_file($file->getPathname(), $dest_file)) {
		    $response->addError('Error saving (move_uploaded_file) chunk '.$flowChunkNumber.' for file '.$flowFilename);
		    $response->setBadRequest();
		}// check if all the parts present, and create the final destination file
		else{ 
		    $wholeFilePath = tempnam($this->getFileTempRoot(), 'upload');
		    if($this->createFileFromChunks($temp_dir, $wholeFilePath, $flowFilename, $flowChunkSize, $flowTotalSize)) {
			$mimeTypeGuesser = MimeTypeGuesser::getInstance();
			$extensionGuesser = ExtensionGuesser::getInstance();
			
			$wholeFileMimeType = $mimeTypeGuesser->guess($wholeFilePath);
			$wholeFileExtension = $extensionGuesser->guess($wholeFileMimeType);
			
			if(!in_array($wholeFileMimeType, $this->validMimeTypes())){
			    $response->addError('Mime Type:'.$wholeFileMimeType.' is not allowed');
			    $response->setBadRequest();
			    unlink($wholeFilePath);
			    continue;
			}

			//sha1_file($wholeFilePath)
			$newFileName = md5($wholeFilePath.date("Y-m-d H:i:s")).'.'.$wholeFileExtension;
			rename($wholeFilePath, $this->getFileRoot().$newFileName);
			
			$em = $this->getDoctrine()->getManager();
			$upload = new \Nerick\PortfolioBundle\Entity\Upload();
			$upload->setType($wholeFileMimeType);
			$upload->setName($flowFilename);
			$upload->setFile($newFileName);
			$upload->setSize($flowTotalSize);
			$upload->setTemporary(true);
			$upload->setPublic(false);
			$upload->setDateCreated(new \DateTime('now'));
			$em->persist($upload);
			$em->flush();
			$response->setData(array('uploadId' => $upload->getId()));
			$response->addMessage('Upload Successfull.');
		    }
		}
	    }
	}
	else{
	    $response->addError('No files found.');
	}
	
	return $response;
    }
    
    /**
     * @Rest\Get(
     *     "/uploads"
     * )
     */
    public function checkUploadAction(Request $request)//save
    {
	//TODO: gaurd clause this
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$flowIdentifier = $request->get('flowIdentifier');
	$flowFilename = $request->get('flowFilename');
	$flowChunkNumber = $request->get('flowChunkNumber');
	
	
	//$temp_dir = 'temp/'.$request->get('flowIdentifier');
	$temp_dir = $this->getFileTempRoot().$flowIdentifier;
	$chunk_file = $temp_dir.'/'.$flowFilename.'.part'.$flowChunkNumber;
	if (file_exists($chunk_file)) {
	    $response->setStatus(200);
	} else
	{
	    $response->setStatus(204);
	    return $response;
	}
	return $response;
	//return $this->addUploadAction($request);
    }
    
    /**
     * @Rest\Delete(
     *     "/uploads/{id}",
     *     requirements = {
     *         "id": "\d+"
     *     }
     * )
     */
    public function deleteUploadAction($id)//de;ete
    {
	$response = new Response();
	if(!$this->get('Nerick.PortfolioBundle.SecurityController')->isLoggedin()){
	   $response->setForbidden();
	   return $response;
	}
	$repository =  $this->getDoctrine()->getRepository('NerickPortfolioBundle:Upload');
	$upload = $repository->findOneById($id);
	if (!$upload) {
	    $response->addError('Upload not found.');
	    $response->setBadRequest();
	}
	else{
	    $em = $this->getDoctrine()->getManager();
	    $this->get('Nerick.PortfolioBundle.UploadService')->deleteArkworkLinks($upload);
	    $upload->deleteFile();
	    $em->remove($upload);
	    $em->flush();
	    $response->addMessage('Upload deleted successfully.');
	}
	return $response;
    }
}