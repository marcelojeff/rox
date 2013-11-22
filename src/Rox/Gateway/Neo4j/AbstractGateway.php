<?php
namespace Rox\Gateway\Neo4j;

use Rox\Gateway\RoxGateway;

abstract class AbstractGateway extends RoxGateway
{
    protected $nodeLabel;
    function __construct($db) {
        parent::__construct($db);
        $this->nodeLabel = $this->db->makeLabel($this->getModelName());
    }
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
    	return $node->delete();
    }
}