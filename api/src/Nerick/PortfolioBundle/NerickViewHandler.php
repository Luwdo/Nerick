<?php
namespace Nerick\PortfolioBundle;
use \FOS\RestBundle\View\ViewHandler;
use \FOS\RestBundle\View\View;
use \FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;

class NerickViewHandler extends ViewHandler{
    
//    /**
//     * Constructor
//     *
//     * @param array  $formats              the supported formats as keys and if the given formats uses templating is denoted by a true value
//     * @param int    $failedValidationCode The HTTP response status code for a failed validation
//     * @param int    $emptyContentCode     HTTP response status code when the view data is null
//     * @param bool   $serializeNull        Whether or not to serialize null view data
//     * @param array  $forceRedirects       If to force a redirect for the given key format, with value being the status code to use
//     * @param string $defaultEngine        default engine (twig, php ..)
//     */
//    public function __construct(
//        array $formats = null,
//        $failedValidationCode = Codes::HTTP_BAD_REQUEST,
//        $emptyContentCode = Codes::HTTP_NO_CONTENT,
//        $serializeNull = false,
//        array $forceRedirects = null,
//        $defaultEngine = 'twig'
//    ) {
//	var_dump($formats);die;
//        parent::__construct($formats, $failedValidationCode, $emptyContentCode, $serializeNull, $forceRedirects, $defaultEngine);
//    }
    
    
    /**
     * Handles creation of a Response using either redirection or the templating/serializer service.
     *
     * @param View    $view
     * @param Request $request
     * @param string  $format
     *
     * @return Response
     */
    public function createResponse(View $view, Request $request, $format)
    {
	$response = parent::createResponse($view, $request, $format);
	$data = $view->getData();
	if(isset($data->meta)){
	    if(isset($data->meta->statusCode)){
		$response->setStatusCode($data->meta->statusCode);
	    }
	    if(isset($data->meta->headers) && count($data->meta->headers)){
		foreach($data->meta->headers as $key => $value){
		    $response->headers->set($key, $value);
		}
	    }
	}
	return $response;
    }

    
}
