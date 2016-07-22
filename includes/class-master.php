<?php
namespace Wontrapi;
/**
 * Wontrapi\Master
 *
 * @since 0.1.2
 * @package Wontrapi
 */
/**
 * class Master
 * @since 0.1.2
 */
class Master {
	/**
	 * App ID from Ontraport
	 * @var string
	 * @since 0.1.2
	 */
	protected $app_id;
	/**
	 * API Key from Ontraport
	 * @var string
	 * @since 0.1.2
	 */
	protected $app_key;
	/**
	 * [$endpoint description]
	 * @var [type]
	 * @since 0.1.2
	 */
	protected $endpoint;
	/**
	 * Ontraport API version
	 * @var integer
	 * @since 0.1.2
	 */
	protected $version;
	/**
	 * Namespace
	 * @var string
	 * @since 0.1.2
	 */
	protected $namespace;
	/**
	 * [__construct description]
	 * @param [type] $app_id  [description]
	 * @param [type] $app_key [description]
	 * @since 0.1.2
	 */
	public function __construct () {
		$this->set_app_id ( wontrapi_get_option( 'app_id' ) );
		$this->set_app_key ( wontrapi_get_option( 'app_key' ) );
		$this->set_version ( 1 );
		$this->set_namespace ( 'Wontrapi' );
		$this->default_endpoints ();
	}

	/**
	 * [set_version description]
	 * @param [type] $v [description]
	 * @since 0.1.2
	 */
	public function set_version ( $v ) {
		$this->version = $v;
	}

	/**
	 * [set_app_id description]
	 * @param [type] $id [description]
	 * @since 0.1.2
	 */
	public function set_app_id ( $id ) {
		$this->app_id = $id;
	}

	/**
	 * [set_app_key description]
	 * @param [type] $key [description]
	 * @since 0.1.2
	 */
	public function set_app_key ( $key ) {
		$this->app_key = $key;
	}

	/**
	 * [set_endpoint description]
	 * @param [type] $endpoint [description]
	 * @param [type] $subst    [description]
	 * @since 0.1.2
	 */
	public function set_endpoint ( $endpoint, $subst = false ) {
		if ( $subst == true ) {
			$this->endpoint = $endpoint;
		} else {
			foreach ( $endpoint as $k => $endp ) {
				$this->endpoint [ $k ] = $endp;
			}
		}
	}

	/**
	 * [set_namespace description]
	 * @param [type] $ns [description]
	 * @since 0.1.2
	 */
	public function set_namespace ( $ns ) {
		$this->namespace = $ns;
	}

	/**
	 * [get_version description]
	 * @return [type] [description]
	 * @since 0.1.2
	 */
	public function get_version () {
		return $this->version;
	}

	/**
	 * [get_app_id description]
	 * @return [type] [description]
	 * @since 0.1.2
	 */
	public function get_app_id () {
		return $this->app_id;
	}

	/**
	 * [get_app_key description]
	 * @return [type] [description]
	 * @since 0.1.2
	 */
	public function get_app_key () {
		return $this->app_key;
	}

	/**
	 * [get_endpoint description]
	 * @param  string $_id [description]
	 * @return [type]      [description]
	 * @since 0.1.2
	 */
	public function get_endpoint ( $_id = '' ) {
		if ( $_id != '' && isset ( $this->endpoint [ $_id ] ) )
			return $this->endpoint [ $_id ];
		return $this->endpoint;
	}

	/**
	 * [get_namespace description]
	 * @return [type] [description]
	 * @since 0.1.2
	 */
	public function get_namespace () {
		return $this->namespace;
	}

	/**
	 * [default_endpoints description]
	 * @return [type] [description]
	 * @since 0.1.2
	 */
	protected function default_endpoints () {
		$url = 'https://api.ontraport.com/' . $this->version . '/';
		$this->set_endpoint ( array (
			'object' 							=> $url . 'object',
			'objects' 							=> $url . 'objects',
			'objects_meta' 						=> $url . 'objects/meta',
			'objects_tag' 						=> $url . 'objects/tag',
			'form' 								=> $url . 'form',
			'message' 							=> $url . 'message',
			'task_cancel' 						=> $url . 'task/cancel',
			'task_complete' 					=> $url . 'task/complete',
			'transaction_processmanual' 		=> $url . 'transaction/processManual',
			'transaction_refund' 				=> $url . 'transaction/refund',
			'transaction_converttodecline' 		=> $url . 'transaction/convertToDecline',
			'transaction_converttocollections' 	=> $url . 'transaction/convertToCollections',
			'transaction_void' 					=> $url . 'transaction/void',
			'transaction_voidpurchase' 			=> $url . 'transaction/voidPurchase',
			'transaction_reruncommission' 		=> $url . 'transaction/rerunCommission',
			'transaction_markpaid' 				=> $url . 'transaction/markPaid',
			'transaction_rerun' 				=> $url . 'transaction/rerun',
			'transaction_writeoff' 				=> $url . 'transaction/writeOff',
			'transaction_order' 				=> $url . 'transaction/order',
			'transaction_resendinvoice' 		=> $url . 'transaction/resendInvoice'
		) );
	}

	/**
	 * [send_request description]
	 * @param  string $endpoint   HTTP endpoint for the REST call
	 * @param  string $method     HTTP verb to use
	 * @param  array $parameters  HTTP request-body content
	 * @return string             HTTP response-body content
	 * @since 0.1.2
	 */
	public function send_request ( $endpoint, $method, $parameters ) {
		/* instantiate HTTP headers with authentication data from Ontraport */
		$headers = array ();
		array_push ( $headers, 'Api-Appid: ' . $this->app_id );
		array_push ( $headers, 'Api-Key: ' . $this->app_key );

		/* istantiate querystring and postargs variables that will be used respectively in GET and POST/PUT requests */
		$querystring = '';
		$postargs = '';

		/* which method will we be using? */
		$method = strtoupper ( $method );
		if ( $method == 'GET' ) {
			/* we will use GET so let build the query string that we will append to the endpoint */
			$querystring = '?' . http_build_query ( $parameters );
		} else {
			/* we will use POST or PUT so we set up the request-body postargs */
			$postargs = http_build_query ( $parameters );
		}

		/* Setting up the cURL object */
		$session = curl_init ();
		curl_setopt ( $session, CURLOPT_URL, $this->endpoint [ $endpoint ] . $querystring );
		curl_setopt ( $session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ( $session, CURLOPT_CUSTOMREQUEST, $method );
		curl_setopt ( $session, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $session, CURLOPT_USERAGENT, 'LSPOAW/LSP Ontraport API Wrapper' );

		if ( $method != 'GET' ) {
			curl_setopt ( $session, CURLOPT_POSTFIELDS, $postargs );
		}

		/* Executing cURL call and return result */
		$response = curl_exec ( $session );
		curl_close ( $session );

		return $response;
	}

	/**
	 * [__get description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 * @since 0.1.2
	 */
	public function __get ( $name ) {
		try {
			$name = $this->get_namespace () . '\\' . $name;
			return new $name( $this );
		} catch ( Exception $e ) {
			throw $e;
		}
	}
}
