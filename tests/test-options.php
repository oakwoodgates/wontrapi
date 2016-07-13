<?php

class Wontrapi_Options_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'Wontrapi_Options') );
	}

	function test_class_access() {
		$this->assertTrue( wontrapi()->options instanceof Wontrapi_Options );
	}
}
