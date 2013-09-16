<?php

namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;


class LoggedUser extends AbstractHelper {

	public function __invoke() {
		$container = new Container('loggedUser');
		if(isset($container->name)){
			return $container->name;
		} else {
			return null;
		}
	}
}