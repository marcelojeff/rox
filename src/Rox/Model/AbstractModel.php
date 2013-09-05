<?php

namespace Rox\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

abstract class AbstractModel implements InputFilterAwareInterface{
	const INT = 'Zend\I18n\Validator\Int';
	const ALPHA = 'Zend\I18n\Validator\Alpha';
	const EMAIL = 'Zend\Validator\EmailAddress';
	const ALPHA_NUM = 'Zend\I18n\Validator\Alnum';
	
	const TYPE = 0;
	const LENGTH = 1;
	const REQUIRED = 2;

	protected $inputFilter;
	protected $_fields;
	
	public function getFields(){
		return array_keys($this->_fields);
	}
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}

	public function __get($key){
		if(isset($this->_fields[$key])){
			return isset($this->_fields[$key]['value'])?$this->_fields[$key]['value']:null;
		}
		return null;
	}
	/**
	 * TODO validate fields, allow dynamic fields (form NoSQL)
	 * @param unknown $key
	 * @param unknown $value
	 * @throws \Exception
	 */
	public function __set($key, $value){
		if(isset($this->_fields[$key])){
			//if($this->isValid($key, $value)){
				$this->_fields[$key]['value'] = $value;
			//} else {
				//throw new \Exception(self::INVALID_VALUE);
			//}
		} else {
			throw new \Exception(self::INVALID_FIELD);
		}
	}
	public function getInputFilter(){
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			foreach ($this->_fields as $name => $options){
	
				$input = new Input($name);
				$inputValidators = $input->getValidatorChain();
				$inputFilters = $input->getFilterChain();
	
				$type = $options[self::TYPE];
				if($type){
					if(is_array($type)){
						$inputValidators->addValidator(new $type[0]($type[1]));
					} else {
						$inputValidators->addValidator(new $type);
					}
				}
	
				$length = $options[self::LENGTH];
				if($length){
					$inputValidators->addValidator(new StringLength(['encoding'=>'UTF-8','min'=>$length[0],'max'=>$length[1]]));
				}
				
				if($options[self::REQUIRED]){
					$input->setRequired(true);
				} else {
					$input->setRequired(false);
				}
				$inputFilter->add($input);
			}
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}

?>