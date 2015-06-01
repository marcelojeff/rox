<?php
namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CompoundFormRow extends AbstractHelper {
	
	private $template = 'form-bs3-horizontal-compound-row'; 
	const LABEL_COLUMN = 0;
	const ELEMENT_COLUMN = 1;
	
	public function setTemplate($template)
	{
	    $this->template = $template;
	}
	
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
			    $labelClass = 'control-label';
			    if ($this->template == 'form-bs3-horizontal-compound-row') {
			        $labelClass .= sprintf ( ' col-md-%s', $element['colunms'][self::LABEL_COLUMN]);
			    }
				$element['element']->setLabelAttributes ( [
						'class' => $labelClass
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
		return $this->view->partial($this->template, ['elements' => $elements]);
	}
}