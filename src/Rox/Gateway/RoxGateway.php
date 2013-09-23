<?php

namespace Rox\Gateway;

use Doctrine\Common\Inflector\Inflector;
use Rox\Hydrator\MagicMethods;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Rox\Model\AbstractModel;

class RoxGateway {
	
	protected $model;
	protected $db;
	protected $hydrator;
	protected $name;
	protected $reflectionClass;
	/**
	 *
	 * @param \MongoDB $db
	 * @param Rox\Model\AbstractModel $model
	 * @param Zend\Stdlib\Hydrator\HydratorInterface $hydrator
	 */
	public function __construct($db, AbstractModel $model = null, HydratorInterface $hydrator = null)
	{
		$this->db = $db;
		$this->reflectionClass = new \ReflectionClass(get_class($this));
		$this->name = $this->reflectionClass->getShortName();
		$this->setModel($model);
		$this->setHydrator($hydrator);
	
	}
	public function setModel($model){
		if($model){
			$this->model = $model;
		} else {
			$inflector = new Inflector();
			$name = $this->reflectionClass->getShortName();
			$namespace = $this->reflectionClass->getNamespaceName();
			$modelName = $inflector->singularize($name);
			$modelNamespace = substr($namespace, 0, strpos($namespace,"\\"));
			$modelClassName = sprintf('\%s\Model\%s',$modelNamespace, $modelName);
			$this->model = new $modelClassName;
		}
	}
	public function setHydrator($hydrator){
		if($hydrator){
			$this->hydrator = $hydrator;
		} else {
			$this->hydrator = new MagicMethods();
		}
	}
	/**
	 * Proxy for Rox\Model\AbstractModel::getInputFilter()
	 * @return Zend\InputFilter\InputFilter
	 */
	public function getInputFilter($fields = null)
	{
		return $this->model->getInputFilter($fields);
	}
}