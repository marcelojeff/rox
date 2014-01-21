<?php
namespace Rox\Gateway\Neo4j;

use Rox\Gateway\RoxGateway;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Node;

abstract class AbstractGateway extends RoxGateway
{
    protected $nodeLabel;
    function __construct($db) {
        parent::__construct($db);
        $this->nodeLabel = $this->db->makeLabel($this->getModelName());
    }
    /**
     * 
     * @param array $data
     * @return Node
     */
    public function create($data) {
    	$node = $this->db->makeNode();
    	$data = $this->filterData($data);
    	foreach ($data as $key => $value) {
    		$node->setProperty($key, $value);
    	}
    	$node->save();
    	$node->addLabels([$this->nodeLabel]);
    	return $node;
    }
    //FIXME refactoring duplicated code
    public function edit($data, $id) {
    	$node = $this->findById($id);
    	$data = $this->filterData($data);
    	foreach ($data as $key => $value) {
    		$node->setProperty($key, $value);
    	}
    	return $node->save();
    }
    public function findAll() {
    	return $this->nodeLabel->getNodes();
    }
    public function findById($id) {
    	return $this->db->getNode($id);
    }
    public function delete($id){
    	$node = $this->db->getNode($id);
    	$relationships = $node->getRelationships();
    	foreach ($relationships as $relationship){
    	    $relationship->delete();
    	}
    	return $node->delete();
    }
    public function executeQuery($query) {
        $query = new Query($this->db, $query);
        return $query->getResultSet();
    	//return $this->db->execute($query);
    }
    public function getAssocArray(){
    	$assoc = [];
    	$data = $this->nodeLabel->getNodes();
    	foreach ($data as $record){
    		$assoc[$record->getId()] = $record->name;
    	}
    	return $assoc;
    }
}