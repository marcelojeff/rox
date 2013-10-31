<?php

namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;


class InlineCheckbox extends AbstractHelper {
	public function __invoke($property, $record) {
		$checked = '';
		if($record->$property){
			$checked = 'checked="checked"';
		}
		return sprintf('<input class="inline-checkbox" name="%s" type="checkbox" data-id="%s" %s>', $property, $record->_id->{'$id'}, $checked);
	}
}