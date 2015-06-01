<?php
namespace Rox\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;

class SimpleFormRow extends AbstractHelper
{

    private $template = 'form-bs3-horizontal-simple-row';

    protected function getClass($element)
    {
        if ($element->getAttribute('class')) {
            return $element->getAttribute('class') . ' form-control';
        }
        return 'form-control';
    }
    
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * append_addon
     * prepend_addon
     * append_html
     * prepend_html
     * element_colunms
     * label_colunms
     * @param unknown $element
     * @param array $options
     */
    public function __invoke(ElementInterface $element, $options = null)
    {
        $elementColunms = 9;
        $labelColunms = 3;
        if (isset($options['label_colunms'])) {
            $labelColunms = $options['label_colunms'];
        }
        if (isset($options['element_colunms'])) {
            $elementColunms = $options['element_colunms'];
        }
        $prependAddon = $appendAddon = $inputGroup = $hasError = $label = '';
        $display = 'display: none;';
        if ($errors = $this->view->formElementErrors($element)) {
            $hasError = 'has-error';
            $display = '';
        }
        $label = $inputGroup = '';
        if ($element->getLabel()) {
            if($element instanceof \Zend\Form\Element\Radio){
                $labelClass = 'radio-inline';
                $inputGroup = 'radio-height-hack';
            } else {
                $labelClass = 'control-label';
                $element->setAttribute('class', $this->getClass($element));
            }
            if ($this->template == 'form-bs3-horizontal-simple-row') {
                $labelClass .= sprintf(' col-lg-%s', $labelColunms);
            }
            $element->setLabelAttributes([
                'class' => $labelClass
            ]);
            // Is there a better way to do this?
            $label = str_replace('radio-inline', '', $this->view->formlabel($element));
        }
        if (($prependAddon = $element->getOption('prepend_addon'))) {
            $inputGroup .= 'input-group';
        }
        if (($appendAddon = $element->getOption('append_addon'))) {
            $inputGroup .= 'input-group';
        }
        return $this->view->partial($this->template, [
            'element' => $element,
            'label' => $label,
            'colunms' => $elementColunms,
            'display' => $display,
            'hasError' => $hasError,
            'errors' => $errors,
            'input_group' => $inputGroup,
            'append_html' => $element->getOption('append_html'),
            'prepend_html' => $element->getOption('prepend_html'),
            'append_addon' => $appendAddon,
            'prepend_addon' => $prependAddon
        ]);
    }
}