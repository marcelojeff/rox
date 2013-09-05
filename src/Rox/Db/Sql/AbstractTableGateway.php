<?php

namespace Rox\Db\Sql;

use Zend\Db\TableGateway\AbstractTableGateway as ZendTableGateway;
use Zend\Db\ResultSet\HydratingResultSet;
use Rox\Hydrator\MagicMethods;

class AbstractTableGateway extends ZendTableGateway {

	protected $prototype;
	
	public function __construct($adapter, $prototype)
	{
		$this->adapter = $adapter;
		$this->prototype = $prototype;
		$this->resultSetPrototype = new HydratingResultSet(
			new MagicMethods, $prototype	
		);		
		$this->resultSetPrototype->buffer();
		$this->initialize();
	}
	public function getInputFilter(){
		return $this->prototype->getInputFilter();
	}
	
	
}

?>