<?php
namespace Nerick\PortfolioBundle\API;


class Notification {
    //put your code here
    public $content;
    public $type;
    public $target;
    
    //types
    const ERROR = 'error';
    const WARNING = 'warning';
    const MESSAGE = 'message';
    
    public function __construct($content = '', $type = self::SUCCESS, $target = null) {
	$this->content = $content;
	$this->type = $type;
	$this->target = $target;
    }
}
