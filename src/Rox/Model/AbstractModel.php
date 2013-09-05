<?php

namespace Rox\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use PhlyMongo\HydratingMongoCursor;
use Rox\Hydrator\MagicMethods;

/**
 * TODO It has some mongodb especific methods, to correct this we can use a especific service provider
 * @author marcelo
 *
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
	protected $db;
	protected $name; //for mongo it's the collection name
	
	public function __construct($db){
		$this->db = $db;
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
			return isset($this->fields[$key]['value'])?$this->fields[$key]['value']:null;
		}
		return null;
	}
	public function __set($key, $value){
	    $this->fields[$key]['value'] = $value;
	    /*if(isset($this->fields[$key])){
			//if($this->isValid($key, $value)){
				$this->fields[$key]['value'] = $value;
			//} else {
				//throw new \Exception(self::INVALID_VALUE);
			//}
		} else {
			//throw new \Exception(self::INVALID_FIELD);
		}*/
	}
	public function getInputFilter(){
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			foreach ($this->fields as $name => $options){
	
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
	/**
	 * TODO It's especific for MongoDB
	 * FIXME allow the use of conditional and especific columns
	 * @return \PhlyMongo\HydratingMongoCursor
	 */
	public function findAll(){
		return new HydratingMongoCursor(
				$this->db->{$this->name}->find(),
				new MagicMethods,
				$this
		);
	}
	/**
	 * TODO It's especific for MongoDB
	 * @param string $label
	 * @return array An associative array with [value => label] format
	 */
	public function getAssocArray($label = 'name'){
		$assoc = [];
		$data = $this->db->{$this->name}->find([],['_id', $label]);
		foreach ($data as $record){
			$assoc[$record['_id']->{'$id'}] = $record[$label];
		}
		return $assoc;
	}
	/**
	 * TODO It's especific for MongoDB
	 * @param string $_id of document
	 * @return array
	 */
	public function findById($_id){
		return $this->db->{$this->name}->findOne(['_id' => new \MongoId($_id)]);
	}
}