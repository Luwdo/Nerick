<?php
namespace Nerick\PortfolioBundle\API;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response {
    public $data;
    public $notifications;
    /**
     * @Exclude
     * @var object 
     */
    public $meta;
    
    //const OK = 200;
    //const BAD_REQUEST = 400;
    
    public function __construct($data = null, $statusCode = HttpResponse::HTTP_OK, $notifications = array()) {
	$this->data = $data;
	$this->meta = (object)array();
	$this->meta->statusCode = $statusCode;
	$this->meta->headers = (object)array();
	$this->notifications = $notifications;
    }
    
    public function addError($content, $target = null){
	$this->notifications[] = new Notification($content, $type = Notification::ERROR, $target);
    }
    
    public function addWarning($content, $target = null){
	$this->notifications[] = new Notification($content, $type = Notification::WARNING, $target);
    }
    
    public function addMessage($content, $target = null){
	$this->notifications[] = new Notification($content, $type = Notification::MESSAGE, $target);
    }
    
    public function setStatus($statusCode){
	$this->meta->statusCode = $statusCode;
    }
    
    public function setBadRequest(){
	$this->meta->statusCode =  HttpResponse::HTTP_BAD_REQUEST;
    }
    
    public function setForbidden(){
	$this->meta->statusCode =  HttpResponse::HTTP_FORBIDDEN;
    }
    
    public function setUnauthorized(){
	$this->meta->statusCode =  HttpResponse::HTTP_UNAUTHORIZED;
    }
    
    public function successful(){
	return $this->meta->statusCode == HttpResponse::HTTP_OK;
    }
    
    public function setHeader($key, $value){
	$this->meta->headers->$key = $value;
    }
    
    public function setData($data){
	$this->data = $data;
    }
    
}
