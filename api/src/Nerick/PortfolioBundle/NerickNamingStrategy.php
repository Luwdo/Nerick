<?php
namespace Nerick\PortfolioBundle;
use \JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use \JMS\Serializer\Metadata\PropertyMetadata;
/**
 * Description of NerickNamingStrategy
 *
 * @author Luwdo
 */
class NerickNamingStrategy implements PropertyNamingStrategyInterface
{
    public function translateName(PropertyMetadata $metadata)
    {
        return $metadata->name;
    }
}