<?php

namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;


class Menu extends AbstractHelper {

	private $menuArray;
	
	public function __construct($menuArray) {
		$this->menuArray = $menuArray;
	}
	public function __invoke($partial) {
		return $this->view->partial($partial, ['pages' => $this->menuArray]);
	}
}