<?php 
function wontrapi_user(){
	$p = 'won_';
	$cmb = new_cmb2_box( array(
		'id'               => $p . 'edit',
		'title'            => __( 'User Profile Metabox', 'wontrapi' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
		'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
	) );

	$cmb->add_field( array(
		'name'    => 'Ontraport Contact ID',
		'id'      => $p . 'cid',
		'type'    => 'text',
	) );
/*
	$cmb->add_field( array(
		'name'    => 'Website Subscriber ID',
		'id'      => $p . 'wsid',
		'type'    => 'text',
	) );
*/
}
add_action( 'cmb2_admin_init', 'wontrapi_user' );
