<?php
namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;

class SimpleFormRow extends AbstractHelper {
	
	private $template; 
	
	
	protected function getClass($element) {
	   if($element->getAttribute('class')){
	       return $element->getAttribute('class') . ' form-control';
		}
		return 'form-control';
	}
	/**
	 * append_addon
	 * prepend_addon
	 * append_html
	 * prepend_html
	 * element_colunms
	 * label_colunms
	 *
	 * TODO change templates in runtime
	 * @param unknown $element
	 * @param array $options
	 */
	public function __invoke(ElementInterface $element, $options = null) {
		$elementColunms = 9;
		$labelColunms = 3;
		if (isset ( $options['label_colunms'] )) {
			$labelColunms = $options['label_colunms'];
		}
		if(isset($options['element_colunms'])) {
			$elementColunms = $options['element_colunms'];
		}
		
		$prependAddon = $appendAddon = $inputGroup = $hasError = $label = '';
		$display = 'display: none;';
		if ($errors = $this->view->formElementErrors ( $element )) {
			$hasError = 'has-error';
			$display = '';
		}
		$element->setAttribute('class', $this->getClass($element));
			
		if($element->getLabel()){
		    $labelClass = sprintf ( 'col-lg-%s control-label', $labelColunms );
			$element->setLabelAttributes( ['class' => $labelClass] );
			$label = $this->view->formlabel($element);
		}

		if(($prependAddon = $element->getOption('prepend_addon')) || ($appendAddon = $element->getOption('append_addon'))){
			$inputGroup = 'input-group';
		}
		return $this->view->partial('form-bs3-horizontal-simple-row', [
				'label' => $label,
				'element' => $element,
				'colunms' => $elementColunms,
				'display' => $display,
				'hasError' => $hasError,
				'errors' => $errors,
				'input_group' => $inputGroup,
				'append_html' => isset($options['append_html'])?$options['append_html']:'', //TODO use element option
				'prepend_html' => isset($options['prepend_html'])?$options['prepend_html']:'',
				'append_addon' => $appendAddon,
				'prepend_addon' => $prependAddon,
		]);
	}
}