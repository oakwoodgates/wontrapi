<?php
/**
 * Wontrapi Cache Tests.
 *
 * @since   0.3.0
 * @package Wontrapi
 */
class Wontrapi_Cache_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.3.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'Wontrapi_Cache' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.3.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'Wontrapi_Cache', wontrapi()->cache );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  0.3.0
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
