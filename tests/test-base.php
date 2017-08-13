<?php
/**
 * Wontrapi.
 *
 * @since   0.3.0
 * @package Wontrapi
 */
class Wontrapi_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.3.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'Wontrapi') );
	}

	/**
	 * Test that our main helper function is an instance of our class.
	 *
	 * @since  0.3.0
	 */
	function test_get_instance() {
		$this->assertInstanceOf(  'Wontrapi', wontrapi() );
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
