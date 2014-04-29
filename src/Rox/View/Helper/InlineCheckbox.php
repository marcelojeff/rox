<?php

namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;


class InlineCheckbox extends AbstractHelper {
	public function __invoke($property, $record, $class = "inline-checkbox") {
		$checked = '';
		if(isset($record[$property]) && $record[$property] != 0){
			$checked = 'checked="checked"';
		}
		return sprintf('<input class="%s" name="%s" type="checkbox" value="1" data-id="%s" %s>', $class, $property, $record['_id']->{'$id'}, $checked);
	}
}