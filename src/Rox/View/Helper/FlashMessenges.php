<?php

namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class FlashMessenges extends AbstractHelper
{
  protected $flashMessenger;

  public function __construct(FlashMessenger $flashMessenger)
  {
    $this->flashMessenger = $flashMessenger;
  }

  public function __invoke()
  {
  		$formatedMessages = '';
  		//FIXME get all messages
		foreach ($this->flashMessenger->getSuccessMessages() as $message){
  			$formatedMessages .= sprintf('<p>%s</p>', $message);
		}
		$this->flashMessenger->clearMessages();

  	//TODO use a loop partial as template
  	//TODO tests with/out current messages
    return $formatedMessages;
  }
} 


?>