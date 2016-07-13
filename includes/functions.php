<?php
/**
 * Wontrapi Functions
 *
 * @since 0.1.1
 * @package Wontrapi
 */
/**
 * Wrapper to get wontrapi_options from database
 * @since  0.1.1
 * @param  string $key [description]
 * @return mixed      [description]
 */
function wontrapi_get_option( $key = '' ) {
	return cmb2_get_option( 'wontrapi_options', $key );
}
