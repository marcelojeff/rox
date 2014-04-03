<?php

namespace Rox\Adapter;

interface AuthInterface {
	/**
	 * 
	 * @param string $username
	 * @return Rox\Model\AbstractModel
	 */
	public function findByUsername($username);
}