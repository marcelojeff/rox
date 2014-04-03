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
			$element['label'] = $element['has_error'] = $element['prependAddon'] = $element['inputGroup'] = '';
			$prependAddon = $appendAddon = $inputGroup = $hasError = $label = '';
			
			if(!$element['element']->getAttribute('class')){
				$element['element']->setAttribute ( 'class', 'form-control' );
			} else {
				$class = $element['element']->getAttribute('class') . ' form-control';
				$element['element']->setAttribute('class', $class);
			}
			
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
			if($element['prependAddon'] = $element['element']->getOption('prepend_addon')){
				$element['inputGroup'] = 'input-group';
			}
			if($element['append_html'] = $element['element']->getOption('append_html')){
				$element['inputGroup'] = 'input-group';
			}
			
		}
		return $this->view->partial('form-bs3-horizontal-compound-row', ['elements' => $elements]);
	}
}