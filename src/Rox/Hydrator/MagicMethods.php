<?php

namespace Rox\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Rox\Model\ModelAbstract;
use Zend\Stdlib\Hydrator\AbstractHydrator;

class MagicMethods implements HydratorInterface {
	public function extract($object) {
		$data = [];
		$fields = $object->getFields();
		foreach ($fields as $field){
			$data[] = $object->$field;
		}
		return $data;
	}
	public function hydrate(array $data, $object) {
		foreach ($data as $property => $value){
			$object->$property = $value;
		}
		return $object;
	}
}