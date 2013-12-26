<?php
namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;

class RenderFieldsets extends AbstractHelper {
	
	private function renderFieldsets($fieldsets, $options) {
	    foreach ($fieldsets as $fieldset){
	        foreach ($fieldset->getElements() as $element){
	            //FIXME is there a better way?
	    		echo $this->view->simpleFormRow($element, $options);
	    	}
	    	$this->renderFieldsets($fieldset->getFieldsets(), $options);
	    }
	}
	public function __invoke($fieldsets, $options){
	    $this->renderFieldsets($fieldsets, $options);
	}
}