<?php
namespace Rox\Gateway\Mongo;

use Rox\Model\AbstractModel;
use PhlyMongo\HydratingMongoCursor;
use Zend\Crypt\PublicKey\Rsa\PublicKey;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Use this class to implement collection especific methods
 * @todo Review docs and transactions checking success and thow exceptions in negative cases
 * TODO implements a interface for common methods
 * TODO call colectionName as just name, it'll be usable for all gateways
 * @author Marcelo Araújo
 */
class AbstractGateway
{
    protected $model;
	protected $db;
	protected $hydrator;
	protected $collectionName;
	/**
	 * 
	 * @param \MongoDB $db
	 * @param Rox\Model\AbstractModel $model
	 * @param Zend\Stdlib\Hydrator\HydratorInterface $hydrator
	 */
	public function __construct($db, AbstractModel $model, HydratorInterface $hydrator)
	{
		$this->db = $db;
		$this->model = $model;
		$this->hydrator = $hydrator;
		$this->collectionName = $this->model->getName();
	}
	/**
	 * Proxy for Rox\Model\AbstractModel::getInputFilter()
	 * @return Zend\InputFilter\InputFilter
	 */
    public function getInputFilter($fields = null)
    {
        return $this->model->getInputFilter($fields);
    }
    /**
     * 
     * @param \MongoCursor $cursor
     * @return \PhlyMongo\HydratingMongoCursor
     */
    public function hydrateCollection(\MongoCursor $cursor){
    	return new HydratingMongoCursor(
    			$cursor,
    			$this->hydrator,
    			$this->model
    	);
    }
    /**
     * Find all documents from especific colection   
     * @return \PhlyMongo\HydratingMongoCursor
     */
    public function findAll(){
    	return new HydratingMongoCursor(
    			$this->db->{$this->collectionName}->find(),
    			$this->hydrator,
    			$this
    	);
    }
    /**
     * @param string $label
     * @return array An associative array with [value => label] format
     */
    public function getAssocArray($label = 'name'){
    	$assoc = [];
    	$data = $this->db->{$this->collectionName}->find([],['_id', $label]);
    	foreach ($data as $record){
    		$assoc[$record['_id']->{'$id'}] = $record[$label];
    	}
    	return $assoc;
    }
    /**
     * Find and return a document by its mongoId
     * @param mixed $id of document
     * @return array
     */
    public function findById($id){
    	return $this->db->{$this->collectionName}->findOne(['_id' => $this->getMongoId($id)]);
    }
    /**
     * 
     * @param array $data
     */
    public function filterData(array $data){
    	$model = $this->hydrator->hydrate($data, $this->model);
    	return $this->hydrator->extract($model);
    }
    /**
     * @param array $data
     * @return mixed
     */
    public function save(array $data){
        $data = $this->filterData($data);
    	if(!$data['_id']){
    		unset($data['_id']);
    	} else {
    		$data['_id'] = $this->getMongoId($data['_id']);
    	}
    	return $this->db->{$this->collectionName}->save($data);
    }
    /**
     * Convert a string into a \MongoId
     * @param mixed $id
     * @return \MongoId
     */
    public function getMongoId($id){
    	if($id instanceof \MongoId){
    		return $id;
    	}else{
    		return new \MongoId($id);
    	}
    }
    /**
     * @param mixed $id
     * @return mixed
     */
    public function delete($id){
    	return $this->db->{$this->collectionName}->remove(['_id' => $this->getMongoId($id)]);
    }
}