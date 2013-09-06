<?php

namespace Rox\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use PhlyMongo\HydratingMongoCursor;
use Zend\Stdlib\ArrayObject;

/**
 * TODO It has some mongodb especific methods, to correct this we can use a especific service provider
 * TODO Implements a interface to grant getFields()
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
	protected $db;
	protected $hydrator;
	protected $name; //for mongo it's the collection name
	
	public function __construct($db, $hydrator){
		$this->db = $db;
		$this->hydrator = $hydrator;
		$this->fields['_id'] = null;
		$currentClass = get_class($this);
		$refl = new \ReflectionClass($currentClass);
		$this->name = $refl->getShortName();
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
		        $value = $this->fields[$key]['value'];
		        if(is_array($value)){
                    return ($value['value']);
		        } else {
		        	return $value;
		        }    
		    }		    
		}
		return null;
	}
	public function __set($key, $value){
	    if(isset($this->fields[$key]) || $key === '_id'){
	        $this->fields[$key]['value'] = $value;
	    }
	}
	/*
	 * TODO implement filters
	 */
	public function getInputFilter(){
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			foreach ($this->fields as $name => $options){
	            if(!empty($options) && !is_object($options[self::TYPE])){
	                $input = new Input($name);
	                $inputValidators = $input->getValidatorChain();
	                $inputFilters = $input->getFilterChain();
	                
	                $type = $options[self::TYPE];
	                if($type){
	                	if(is_array($type)){
	                		$inputValidators->attach(new $type[0]($type[1]));
	                	} else {
	                		$inputValidators->attach(new $type);
	                	}
	                }
	                $length = $options[self::LENGTH];
	                if($length){
	                	$inputValidators->attach(new StringLength(['encoding'=>'UTF-8','min'=>$length[0],'max'=>$length[1]]));
	                }
	                
	                if($options[self::REQUIRED]){
	                	$input->setRequired(true);
	                } else {
	                	$input->setRequired(false);
	                }
	                $inputFilter->add($input);
   	            }
			}
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
	public function hydrateCollection(\MongoCursor $cursor){
	    return new HydratingMongoCursor(
	    		$cursor,
	    		$this->hydrator,
	    		$this
	    );
	}
	/**
	 * TODO It's especific for MongoDB
	 * FIXME allow the use of conditional and especific columns
	 * @return \PhlyMongo\HydratingMongoCursor
	 */
	public function findAll(){
		return new HydratingMongoCursor(
				$this->db->{$this->name}->find(),
				$this->hydrator,
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
	 * @param mixed $id of document
	 * @return array
	 */
	public function findById($id){
		return $this->db->{$this->name}->findOne(['_id' => $this->getMongoId($id)]);
	}
	/**
	 * TODO check edit
	 * @param array $data
	 */
	public function save($data){
	   $this->populate($data);
	   $data = $this->getData();
	   if(!$data['_id']){
	       unset($data['_id']);
	   } else {
	       $data['_id'] = $this->getMongoId($data['_id']);
	   }
	   return $this->db->{$this->name}->save($data);
	}
	private function getMongoId($id){
	    if($id instanceof \MongoId){
	       return $id;
	    }else{
	    	return new \MongoId($id);
	    }
	}
	/**
	 * TODO check and throw exception on error
	 * @param unknown $id
	 */
	public function delete($id){
	    return $this->db->{$this->name}->remove(['_id' => $this->getMongoId($id)]);
	}
	public function populate($data){
	   return $this->hydrator->hydrate($data, $this);
	}
	public function getData(){
	    return $this->hydrator->extract($this);
	}
}