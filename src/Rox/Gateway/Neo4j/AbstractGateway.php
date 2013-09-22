<?php
namespace Rox\Gateway\Neo4j;

abstract class AbstractGateway
{
    protected $model;
    protected $db;
    protected $hydrator;
    protected $collectionName;
    /**
     *
     * @param $db
     * @param Rox\Model\AbstractModel $model
     * @param Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     */
    public function __construct($db, AbstractModel $model, HydratorInterface $hydrator)
    {
    	$this->db = $db;
    	$this->model = $model;
    	$this->hydrator = $hydrator;
    	//$this->collectionName = $this->model->getName();
    }
}

?>