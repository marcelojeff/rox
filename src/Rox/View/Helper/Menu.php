<?php

namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;


class Menu extends AbstractHelper {

	private $menuArray;
	
	public function __construct($menuArray) {
		$this->menuArray = $menuArray;
	}
	public function __invoke() {
		return $this->view->partial('nav-left-bs3', ['pages' => $this->menuArray]);
	}
}