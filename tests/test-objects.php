<?php

class Wontrapi_Objects_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'Wontrapi_Objects') );
	}

	function test_class_access() {
		$this->assertTrue( wontrapi()->objects instanceof Wontrapi_Objects );
	}
}
