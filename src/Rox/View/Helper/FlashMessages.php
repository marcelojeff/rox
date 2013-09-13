<?php

namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class FlashMessages extends AbstractHelper {
	protected $flashMessenger;
	public function __construct(FlashMessenger $flashMessenger) {
		$this->flashMessenger = $flashMessenger;
	}
	private function getMessages($namespace) {
		$this->flashMessenger->setNamespace($namespace);
		$renderedMessages = '';
		if ($this->flashMessenger->hasMessages() || $this->flashMessenger->hasCurrentMessages()){
			$messages = array_merge($this->flashMessenger->getCurrentMessages(),
									$this->flashMessenger->getMessages()
			);
			foreach ($messages as $message){
				$messagesModel[] = ['message' => $message];
			}
			//TODO change template dynamicaly
			$renderedMessages = $this->view->partialLoop("message-bs3-$namespace", $messagesModel);
		}
		return $renderedMessages;
	}
	
	public function __invoke() {
		$formatedMessages = '';
		$formatedMessages .= $this->getMessages(FlashMessenger::NAMESPACE_ERROR);
		$formatedMessages .= $this->getMessages(FlashMessenger::NAMESPACE_SUCCESS);
		$formatedMessages .= $this->getMessages(FlashMessenger::NAMESPACE_INFO);
		$formatedMessages .= $this->getMessages(FlashMessenger::NAMESPACE_DEFAULT);
		$this->flashMessenger->clearCurrentMessagesFromContainer();
		$this->flashMessenger->clearMessagesFromContainer();		
		return $formatedMessages;
	}
}