<?php
namespace Nerick\PortfolioBundle;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer as JMSserialize;
use Nerick\PortfolioBundle\NerickNamingStrategy;
/**
 * Description of serializer
 *
 * @author Luwdo
 */
class NerickSerializer implements JMSserialize\SerializerInterface{
    
    public function serialize($data, $format,  JMSserialize\SerializationContext $context = null){
	if(is_object($data) && get_class($data) == 'stdClass'){
	    return json_encode($data);
	}
	$serializer = JMSserialize\SerializerBuilder::create()
		->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new NerickNamingStrategy()))
		->build();
	$jsonContent = $serializer->serialize($data, $format, $context);
	return $jsonContent;
    }
    
    public function deserialize($data, $type, $format, JMSserialize\DeserializationContext $context = null){	
//	$serializer = JMSserialize\SerializerBuilder::create()
//			->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new NerickNamingStrategy()))
//			->build();
	$serializer = JMSserialize\SerializerBuilder::create()->build();
	$jsonContent = $serializer->deserialize($data, $type, $format, $context);
	return $jsonContent;
    }
}
