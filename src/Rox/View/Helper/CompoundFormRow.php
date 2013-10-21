<?php
namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CompoundFormRow extends AbstractHelper {
	
	private $template; 
	const LABEL_COLUMN = 0;
	const ELEMENT_COLUMN = 1;
	
	/**
	 * TODO appends and prepends
	 * @param unknown $elements
	 * @param unknown $options
	 */
	public function __invoke($elements, $options = null) {
		foreach ($elements as &$element){
			$element['label'] = $element['has_error'] = '';
			$element['element']->setAttribute ( 'class', 'form-control' );
			if ($element['element']->getLabel () && $element['colunms'][self::LABEL_COLUMN]) {
				$element['element']->setLabelAttributes ( [
						'class' => sprintf ( 'col-lg-%s control-label', $element['colunms'][self::LABEL_COLUMN])
						] );
				$element['label'] = $this->view->formlabel ( $element['element'] );
			}
			$element['display_errors'] = 'display: none;';
			if ($element['errors'] = $this->view->formElementErrors ( $element['element'] )) {
				$element['has_error'] = 'has-error';
				$element['display_errors'] = '';
			}
		}
		return $this->view->partial('form-bs3-horizontal-compound-row', ['elements' => $elements]);
	}
}