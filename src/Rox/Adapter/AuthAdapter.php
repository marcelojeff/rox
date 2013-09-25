<?php

namespace Rox\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Authentication\Result;

class AuthAdapter implements AdapterInterface {
	
	private $username;
	private $password;
	private $gateway;
	private $container;
	
	public function __construct($gateway, $container) {
		$this->gateway = $gateway;
		$this->container = $container;
	}
	public function setCredentials($username, $password){
		$this->username = $username;
		$this->password = $password;
	}
	public function authenticate() {
		$user = $this->gateway->findByUsername($this->username);
		if ($user) {
			$bcrypt = new Bcrypt();
			if ($bcrypt->verify ( $this->password, $user->password )) {
				$this->container->username = $user->email;
				$this->container->name = $user->name;
				$this->container->type = $user->type;
				return new Result(Result::SUCCESS, $this->username);
			}
		}
		return new Result(Result::FAILURE, null);
	}
}