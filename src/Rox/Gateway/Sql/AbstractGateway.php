<?php

namespace Rox\Db\Sql;

use Zend\Db\TableGateway\AbstractTableGateway as ZendTableGateway;
use Rox\Model\AbstractModel;

/**
 * TODO rewrite construtor
 * @author marcelo
 *
 */
class AbstractTableGateway extends ZendTableGateway
{
	protected $prototype;
	protected $db;
	protected $hydrator;
	
	public function __construct($adapter, AbstractModel $prototype, $hydrator)
	{
		$this->adapter = $adapter;
		$this->prototype = $prototype;
		$this->hydrator = $hydrator;
	}
	public function getInputFilter(){
		return $this->prototype->getInputFilter();
	}
}