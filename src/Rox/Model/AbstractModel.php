<?php

namespace Rox\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

/**
 * This class represents a data model, for exemple a Mongo document or a Mysql row
 * @author Marcelo Araújo
 */
abstract class AbstractModel implements InputFilterAwareInterface{
	const INT = 'Zend\I18n\Validator\Int';
	const ALPHA = 'Zend\I18n\Validator\Alpha';
	const EMAIL = 'Zend\Validator\EmailAddress';
	const ALPHA_NUM = 'Zend\I18n\Validator\Alnum';
	
	const TYPE = 0;
	const LENGTH = 1;
	const REQUIRED = 2;

	protected $inputFilter;
	protected $fields;
	protected $name;
	
	public function __construct(){
		$this->fields['_id'] = null;
		$currentClass = get_class($this);
		$refl = new \ReflectionClass($currentClass);
		$this->name = $refl->getShortName();
	}
	public function getName(){
	    return $this->name;
	}
	public function getFields(){
		return array_keys($this->fields);
	}
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}

	public function __get($key){
		if(isset($this->fields[$key])){
            if(isset($this->fields[$key]['value'])){
                if(is_array($this->fields[$key]['value'])){
                    $obj = new \ArrayObject($this->fields[$key]['value']);
                    return $obj;
                }else{
                    return $this->fields[$key]['value'];
                }
		    }		    
		}
		return null;
	}
	public function __set($key, $value){
	    if(isset($this->fields[$key]) || $key === '_id'){
	        /*if(isset($this->fields[$key]['embedded']) && !isset($this->fields[$key]['value'])){	            
	           $this->fields[$key]['value'] = new $this->fields[$key]['embedded']; 
	        }else{*/
	            $this->fields[$key]['value'] = $value;
	        //}	        
	    }
	}
	/*
	 * TODO implement filters
	 * TODO how about files?
	 */
	public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            foreach ($this->fields as $name => $options) {
                if (! empty($options)) {
                    if (! isset($options['embedded'])) {
                        $input = new Input($name);
                        $inputValidators = $input->getValidatorChain();
                        $inputFilters = $input->getFilterChain();
                        
                        $type = $options[self::TYPE];
                        if ($type) {
                            if (is_array($type)) {
                                $inputValidators->attach(new $type[0]($type[1]));
                            } else {
                                $inputValidators->attach(new $type());
                            }
                        }
                        $length = $options[self::LENGTH];
                        if ($length) {
                            $inputValidators->attach(new StringLength([
                                'encoding' => 'UTF-8',
                                'min' => $length[0],
                                'max' => $length[1]
                            ]));
                        }
                        
                        if ($options[self::REQUIRED]) {
                            $input->setRequired(true);
                        } else {
                            $input->setRequired(false);
                        }
                        $inputFilter->add($input);
                    } else {
                        $embedded = new $options['embedded'];
                    	$inputFilter->add($embedded->getInputFilter(), $name);
                    }
                }
            }
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}