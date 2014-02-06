<?php

namespace Rox\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

/**
 * This class represents a data model, for exemple a Mongo document or a Mysql row
 * @author Marcelo Araújo
 */
abstract class AbstractModel {
	const INT = 'Zend\I18n\Validator\Int';
	const ALPHA = 'Zend\I18n\Validator\Alpha';
	const EMAIL = 'Zend\Validator\EmailAddress';
	const ALPHA_NUM = 'Zend\I18n\Validator\Alnum';
	const IDENTICAL = 'Zend\Validator\Identical';
	const POST_CODE = 'Zend\I18n\Validator\PostCode';
	const FLOAT = 'Zend\I18n\Validator\Float';
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
	public function isIgnorable($key){
		return isset($this->fields[$key]['ignore'])?$this->fields[$key]['ignore']:false;
	}
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}

	public function __get($key){
		if(isset($this->fields[$key])){
            if(isset($this->fields[$key]['value'])){
                /*if(is_array($this->fields[$key]['value'])){
                    $obj = new \ArrayObject($this->fields[$key]['value']);
                    return $obj;
                }else{
                    return $this->fields[$key]['value'];
                }*/
            	return $this->fields[$key]['value'];
		    } elseif(isset($this->fields[$key]['default'])) {
	    		return $this->fields[$key]['default'];
	    	}		    
		}
		return null;
	}
	/**
	 * TODO this $key === '_id' is for mongo, refactor
	 * @param unknown $key
	 * @param unknown $value
	 */
	public function __set($key, $value){
	    if(isset($this->fields[$key]) || $key === '_id' || isset($this->fields['dynamic'])){
	        /*if(isset($this->fields[$key]['embedded']) && !isset($this->fields[$key]['value'])){	            
	           $this->fields[$key]['value'] = new $this->fields[$key]['embedded']; 
	        }else{
	            $this->fields[$key]['value'] = $value;
	        }*/
	    	if(isset($value) && !empty($value)){
	    		$this->fields[$key]['value'] = $value;
	    	} elseif(isset($this->fields[$key]['default'])) {
	    		$this->fields[$key]['value'] = $this->fields[$key]['default'];
	    	}
	    	
	    }
	}
	/*
	 * TODO implement filters
	 * TODO how about files?
	 */
	public function getInputFilter($fields = null)
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            foreach ($this->fields as $name => $options) {
            	if(!$fields || in_array($name, $fields)){
	                if (! empty($options)) {
	                    if (! isset($options['embedded']) && !isset($options['skip_validation'])) {
	                        $input = new Input($name);
	                        $inputValidators = $input->getValidatorChain();
	                        $inputFilters = $input->getFilterChain();
	                        
	                        $type = isset($options[self::TYPE])?$options[self::TYPE]:null;
	                        if ($type) {
	                            if (is_array($type)) {
	                                $inputValidators->attach(new $type[0]($type[1]));
	                            } else {
	                                $inputValidators->attach(new $type());
	                            }
	                        }
	                        $length = isset($options[self::LENGTH])?$options[self::LENGTH]:null;
	                        if ($length) {
	                        	if(!is_array($length)){
	                        		$length = [0 => $length, 1=> $length];
	                        	}
	                            $inputValidators->attach(new StringLength([
	                                'encoding' => 'UTF-8',
	                                'min' => $length[0],
	                                'max' => $length[1]
	                            ]));
	                        }
	                        $required = isset($options[self::REQUIRED])?$options[self::REQUIRED]:null;
	                        if ($required) {
	                            $input->setRequired(true);
	                        } else {
	                            $input->setRequired(false);
	                        }
	                        $inputFilter->add($input);
	                    } elseif(!isset($options['skip_validation']) && !$options['skip_validation']) {
	                        $embedded = new $options['embedded'];
	                    	$inputFilter->add($embedded->getInputFilter(), $name);
	                    }
	                }
            	}
            }
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}