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

	protected static $endpoint = 'object';
	protected static $multiple_endpoint = 'objects';
	protected static $tag_endpoint = 'objects_tag';
	protected static $object_id = 0;
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

	public function search ( $field, $value, $operand = '=', $type = 'auto' ) {
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

		$params = array (
			'objectID' => static::$object_id,
			'condition' => $condition
		);

		return json_decode ( $this->wontrapi->send_request ( static::$multiple_endpoint, 'get', $params ) );
	}

	public function create ( $object ) {
		$params = array (
			'objectID' => static::$object_id
		);

		$object = json_decode ( json_encode ( $object ), true );

		$params = array_merge ( $params, $object );

		return json_decode ( $this->wontrapi->send_request ( static::$multiple_endpoint, 'post', $params ) );
	}

	public function read ( $id ) {
		$params = array (
			'objectID' => static::$object_id,
			'id' => $id
		);
	//	echo '<pre>'; print_r($this->wontrapi); echo '</pre>';
		return json_decode ( $this->wontrapi->send_request ( static::$endpoint, 'get', $params ) );
	}

	public function get ( $ids = array () ) {
		$params = array (
			'objectID' => static::$object_id
		);

		if ( count ( $ids ) > 1 ) {
			$params [ 'ids' ] = implode ( ',', $ids );
		}

		return json_decode ( $this->wontrapi->send_request ( static::$multiple_endpoint, 'get', $params ) );
	}

	public function update ( $object ) {
		$params = array (
			'objectID' => static::$object_id
		);

		$object = json_decode ( json_encode ( $object ), true );

		if ( isset ( $object [ 'id' ] ) ) {
			$params = array_merge ( $params, $object );
			return json_decode (  $this->wontrapi->send_request ( static::$multiple_endpoint, 'put', $params ) );
		} else
			return false;
	}

	public function delete ( $id ) {

	}

	protected function add_tag_to_object ( $ids, $tag_ids ) {
		$params = array (
			'objectID' => static::$object_id,
			'add_list' => implode ( ',', $tag_id ),
			'ids' => implode ( ',', $ids )
		);

		return json_decode (  $this->wontrapi->send_request ( static::$tag_endpoint, 'put', $params ) );
	}

	protected function remove_tag_to_object ( $ids, $tag_ids ) {
		$params = array (
			'objectID' => static::$object_id,
			'remove_list' => implode ( ',', $tag_id ),
			'ids' => implode ( ',', $ids )
		);

		return json_decode (  $this->wontrapi->send_request ( static::$tag_endpoint, 'delete', $params ) );
	}
}
