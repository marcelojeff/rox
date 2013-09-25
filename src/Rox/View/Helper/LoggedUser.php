<?php

namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;


class LoggedUser extends AbstractHelper {

	private $container;
	
	public function __construct($container) {
		$this->container = $container;
	}
	public function __invoke() {
		if(isset($this->container->name)){
			return $this->container->name;
		} else {
			return null;
		}
	}
}