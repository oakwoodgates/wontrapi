<?php
/**
 * Wontrapi Objects
 *
 * @since 0.1.3
 * @package Wontrapi
 */
class Wontrapi_Objects {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.1.3
	 */
	protected $wontrapi;

	/**
	 * Constructor
	 *
	 * @since  0.1.3
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $var ) {
		$this->wontrapi = $var;
	}

	public function object_id( $type ) {
		switch( $type ) {
			case 'contacts':
				$id = 0;
				break;
			case 'notes':
				$id = 12;
				break;
			case 'products':
				$id = 16;
				break;
			case 'purchases':
				$id = 17;
				break;
			case 'shippings':
				$id = 64;
				break;
			case 'staff':
				$id = 2;
				break;
			case 'tags':
				$id = 14;
				break;
			case 'taxes':
				$id = 63;
				break;
			default:
				$id = 0;
				break;
		}
		$id = apply_filters( 'wontrapi_object_id', $id, $type );
		return $id;
	}

	/**
	 * OBJECTS
	 * @link http://api.ontraport.com/doc/#!/objects
	 */

	/**
	 * DELETE /object
	 * @link( http://api.ontraport.com/doc/#!/objects/deleteObject, deleteObject)
	 */
	public function delete_object ( $obj_type, $id ) {
		$params = array (
			'objectID' => self::object_id( $obj_type ),
			'id' => $id
		);
		return json_decode ( Wontrapi::send_request ( 'object', 'delete', $params ) );
	}

	/**
	 * GET /object
	 * @link( http://api.ontraport.com/doc/#!/objects/getObject, getObject)
	 */
	public function get_object ( $obj_type, $id ) {
		$params = array (
			'objectID' => self::object_id( $obj_type ),
			'id' => $id
		);
		return json_decode ( Wontrapi::send_request ( 'object', 'get', $params ) );
	}

	/**
	 * DELETE /objects
	 * @link( http://api.ontraport.com/doc/#!/objects/deleteObjects, deleteObjects)
	 */

	/**
	 * GET /objects
	 * @link( http://api.ontraport.com/doc/#!/objects/getObjects, getObjects)
	 */
	public function get_objects ( $obj_type, $ids = array (), $params = array() ) {
		$params['objectID'] = self::object_id( $obj_type );

		if ( count ( $ids ) > 0 ) {
			$params [ 'ids' ] = implode ( ',', $ids );
		} else {
			$params['performAll'] = 'true';
		}

		return json_decode ( Wontrapi::send_request ( 'objects', 'get', $params ) );
	}

	public function get_objects_by_condition ( $obj_type, $field, $value, $operand = '=', $type = 'auto', $params = array() ) {
		switch ( $type ) {
			case 'auto':
				if ( is_numeric ( $value ) )
					$condition = "{$field}{$operand}{$value}";
				else
					$condition = "{$field}{$operand}'{$value}'";
				break;
			case 'string':
				$condition = "{$field}{$operand}'{$value}'";
				break;
			case 'noquote':
			case 'numeric':
				$condition = "{$field}{$operand}{$value}";
				break;
		}

		$params['objectID'] = self::object_id( $obj_type );
		$params['condition'] = $condition;

		return json_decode ( Wontrapi::send_request ( 'objects', 'get', $params ) );
	}

	/**
	 * POST /objects
	 * @link( http://api.ontraport.com/doc/#!/objects/createObject, createObject)
	 */
	public function create_object ( $obj_type, $object ) {
		$params = array (
			'objectID' => self::object_id( $obj_type )
		);

		$object = json_decode ( json_encode ( $object ), true );

		$params = array_merge ( $params, $object );

		return json_decode ( Wontrapi::send_request ( 'objects', 'post', $params ) );
	}
	/**
	 * PUT /objects
	 * @link( http://api.ontraport.com/doc/#!/objects/updateObjects, updateObjects)
	 */
	public function update_object ( $obj_type, $object ) {
		$params = array (
			'objectID' => self::object_id( $obj_type )
		);

		$object = json_decode ( json_encode ( $object ), true );

		if ( isset ( $object [ 'id' ] ) ) {
			$params = array_merge ( $params, $object );
			return json_decode (  Wontrapi::send_request ( 'objects', 'put', $params ) );
		} else
			return false;
	}
	/**
	 * GET /objects/getInfo
	 * @link( http://api.ontraport.com/doc/#!/objects/getObjectsInfo, getObjectsInfo)
	 */

	/**
	 * GET /objects/meta
	 * @link( http://api.ontraport.com/doc/#!/objects/getMeta, getMeta)
	 */

	/**
	 * POST /objects/saveorupdate
	 * @link( http://api.ontraport.com/doc/#!/objects/saveorupdateObject, saveorupdateObject)
	 */
	public function update_or_create_object ( $obj_type, $email, $params = array() ) {
		$params['objectID'] = self::object_id( $obj_type );
		$params['email'] = $email;

		return json_decode (  Wontrapi::send_request ( 'objects/saveorupdate', 'post', $params ) );

	}
	/**
	 * DELETE /objects/tag
	 * @link( http://api.ontraport.com/doc/#!/objects/removeTag, removeTag)
	 */
	public function remove_tag_from ( $obj_type, $ids, $tag_ids ) {
		$params = array (
			'objectID' => self::object_id( $obj_type ),
			'remove_list' => implode ( ',', $tag_id ),
			'ids' => implode ( ',', $ids )
		);

		return json_decode (  Wontrapi::send_request ( 'objects/tag', 'delete', $params ) );
	}

	/**
	 * PUT /objects/tag
	 * @link( http://api.ontraport.com/doc/#!/objects/addTag, addTag)
	 */
	public function add_tag_to ( $obj_type, $ids, $tag_ids ) {
		$params = array (
			'objectID' => self::object_id( $obj_type ),
			'add_list' => implode ( ',', $tag_id ),
			'ids' => implode ( ',', $ids )
		);

		return json_decode (  Wontrapi::send_request ( 'objects/tag', 'put', $params ) );
	}
}
