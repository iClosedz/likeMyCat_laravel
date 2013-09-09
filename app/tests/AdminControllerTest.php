<?php

class AdminControllerTest extends TestCase {

/*
	public function mock($class){
	  $mock = Mockery::mock($class);
	  
	  $this->app->instance($class, $mock);
	  
	  return $mock;
	}

	public function setUp(){
		parent::setUp();

		$this->mock = $this->mock('AdminController');
	}
*/

	public function testUserIndex(){
		$this->callSecure('GET', '/admin/users');
	}

	public function testUploadsIndex(){
		$this->callSecure('GET', '/admin/uploads');
	}

}

?>