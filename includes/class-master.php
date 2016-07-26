<?php
/**
 * Wontrapi Master
 * @since 0.1.2
 */
class Wontrapi_Master {
	protected $plugin = null;
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
	 * [__construct description]
	 * @param [type] $plugin  [description]
	 * @since 0.1.2
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$wo = get_option( 'wontrapi_options' );
		$this->app_id = $wo['api_appid'];
		$this->app_key = $wo['api_key'];
		$this->default_endpoints ();
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
	 * [default_endpoints description]
	 * @return [type] [description]
	 * @since 0.1.2
	 */
	protected function default_endpoints () {
		$url = 'https://api.ontraport.com/1/';
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
		array_push ( $headers, 'Api-Appid:' . $this->app_id );
		array_push ( $headers, 'Api-Key:' . $this->app_key );

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
			$name = 'Wontrapi_' . $name;
			if ( class_exists( $name ) ) {
				return new $name( $this );
			}
		} catch ( Exception $e ) {
			throw $e;
		}
	}
}
