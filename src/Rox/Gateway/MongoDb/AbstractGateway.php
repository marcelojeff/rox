<?php
namespace Rox\Gateway\MongoDb;

use PhlyMongo\HydratingMongoCursor;
use Zend\Crypt\PublicKey\Rsa\PublicKey;
use Rox\Gateway\RoxGateway;

/**
 * Use this class to implement collection especific methods
 * @todo Review docs and transactions checking success and thow exceptions in negative cases
 * TODO implements a interface for common methods
 * @author Marcelo Araújo
 */
class AbstractGateway extends RoxGateway
{
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
    			$this->db->{$this->name}->find(),
    			$this->hydrator,
    			$this->model
    	);
    }
    /**
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
     * Find and return a document by its mongoId
     * @param mixed $id of document
     * @return array
     */
    public function findById($id){
    	return $this->db->{$this->name}->findOne(['_id' => $this->getMongoId($id)]);
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
    	return $this->db->{$this->name}->save($data);
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
    	return $this->db->{$this->name}->remove(['_id' => $this->getMongoId($id)]);
    }
}