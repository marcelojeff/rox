<?php

namespace Rox\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Authentication\Result;

class AuthAdapter implements AdapterInterface {
	
	protected $username;
	protected $password;
	protected $gateway;
	protected $container;
	protected $user;
	
	public function __construct($gateway, $container = null) {
		$this->gateway = $gateway;
		$this->container = $container;
	}
	public function setCredentials($username, $password){
		$this->username = $username;
		$this->password = $password;
	}
	public function authenticate() {
		if($this->verifyCredentials()){ 
				$this->container->username = $this->user->email;
				$this->container->name = $this->user->name;
				$this->container->type = $this->user->type;
				return new Result(Result::SUCCESS, $this->username);
		}
		return new Result(Result::FAILURE, null);
	}
	public function verifyCredentials(){
		$this->user = $this->gateway->findByUsername($this->username);
		if ($this->user) {
			$bcrypt = new Bcrypt();
			return $bcrypt->verify ( $this->password, $this->user->password );
		}
		return false;
	}
	public function getContainer(){
	    return $this->container;
	}
}