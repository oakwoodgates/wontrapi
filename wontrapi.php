<?php
/**
 * Plugin Name: Wontrapi
 * Plugin URI:  https://wontrapi.com
 * Description: A radical new plugin for WordPress!
 * Version:     1.0.0
 * Author:      Wontrapi
 * Author URI:  https://wpguru4u.com
 * Donate link: https://wontrapi.com
 * License:     GPLv2
 * Text Domain: wontrapi
 * Domain Path: /languages
 *
 * @link    https://wontrapi.com
 *
 * @package Wontrapi
 * @version 1.0.0
 *
 */

/**
 * Copyright (c) 2017 WPGuru4u (email : wpguru4u@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'WONTRAPI_STORE_URL', 'https://wontrapi.com/' ); 

// the download ID for the product in Easy Digital Downloads
define( 'WONTRAPI_ITEM_ID', 19 ); 

// the name of the product in Easy Digital Downloads
define( 'WONTRAPI_ITEM_NAME', 'Wontrapi Core Plugin' ); 

// the name of the settings page for the license input to be displayed
define( 'WONTRAPI_PLUGIN_LICENSE_PAGE', 'pluginname-license' );

define( 'WONTRAPI_VERSION', '1.0.0' );

/**
 * Autoloads files with classes when needed.
 *
 * @since  0.3.0
 * @param  string $class_name Name of the class being requested.
 */
function wontrapi_autoload_classes( $class_name ) {

	// If our class doesn't have our prefix, don't load it.
	if ( 0 !== strpos( $class_name, 'Wontrapi_' ) ) {
		return;
	}

	// Set up our filename.
	$filename = strtolower( str_replace( '_', '-', substr( $class_name, strlen( 'Wontrapi_' ) ) ) );

	// Include our file.
	Wontrapi::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'wontrapi_autoload_classes' );

/**
 * Main initiation class.
 *
 * @since  0.3.0
 */
final class Wontrapi {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  0.3.0
	 */
	const VERSION = '1.0.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  0.3.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  0.3.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  0.3.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  0.3.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Wontrapi
	 * @since  0.3.0
	 */
	protected static $single_instance = null;

	/**
	 * Ontraport App ID.
	 *
	 * @var    Wontrapi
	 * @since  0.3.0
	 */
	protected $id;

	/**
	 * Ontraport App Key.
	 *
	 * @var    Wontrapi
	 * @since  0.3.0
	 */
	protected $key;

	/**
	 * Slug for admin urls and database keys.
	 *
	 * @var    Wontrapi
	 * @since  0.5.0
	 */
	public $slug = 'wontrapi_options';

	/**
	 * Instance of Wontrapi_Options
	 *
	 * @since0.3.0
	 * @var Wontrapi_Options
	 */
	public $options;

	/**
	 * Instance of Wontrapi_Go
	 *
	 * @since0.3.0
	 * @var Wontrapi_Go
	 */
	public $go;

	/**
	 * Instance of Wontrapi_Go
	 *
	 * @since 0.4.0
	 * @var Wontrapi_Actions
	 */
	protected $actions;

	/**
	 * Instance of Wontrapi_Core
	 *
	 * @since 0.4.0
	 * @var Wontrapi_Core
	 */
	protected $core;

	/**
	 * Instance of Wontrapi_User
	 *
	 * @since 0.4.0
	 * @var Wontrapi_User
	 */
	protected $user;

	public static $fs;
	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   0.3.0
	 * @return  Wontrapi A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  0.3.0
	 */
	protected function __construct() {

	//	self::$fs = wontrapi_fs();
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.3.0
	 */
	public function plugin_classes() {
		$this->go = WontrapiGo::init( $this->id, $this->key );
		$this->core = new Wontrapi_Core( $this );
		require( self::dir( 'includes/functions.php' ) );
		$this->options = new Wontrapi_Options( $this );
		$this->actions = new Wontrapi_Actions( $this );
		$this->user = new Wontrapi_User( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  0.3.0
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'init' ], 0 );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  0.3.0
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  0.3.0
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
	}

	/**
	 * Init hooks
	 *
	 * @since  0.3.0
	 */
	public function init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'wontrapi', false, dirname( $this->basename ) . '/languages/' );

		// Setup OP
		$this->ontraport_keys();
		$this->include_dependencies();
		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.3.0
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.3.0
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  0.3.0
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		return true;
	}

	public function ontraport_keys() {
		$data = get_option( 'wontrapi_options', [] );
		$this->id = ( !empty( $data['api_appid'] ) ) ? $data['api_appid'] : 0;
		$this->key = ( !empty( $data['api_key'] ) ) ? $data['api_key'] : 0;
	}

	public function include_dependencies() {
		require( self::dir( 'vendor/CMB2/init.php' ) );
		require( self::dir( 'vendor/WontrapiGo/WontrapiGo.php' ) );
		// Init Freemius.
		// wontrapi_fs();
		// Signal that SDK was initiated.
		// do_action( 'wontrapi_fs_loaded' );
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  0.3.0
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'Wontrapi is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wontrapi' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.3.0
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'options':
			case 'go':
			case 'actions':
			case 'core':
			case 'user':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since  0.3.0
	 *
	 * @param  string $filename Name of the file to be included.
	 * @return boolean          Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since  0.3.0
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since  0.3.0
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path.
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the Wontrapi object and return it.
 * Wrapper for Wontrapi::get_instance().
 *
 * @since  0.3.0
 * @return Wontrapi  Singleton instance of plugin class.
 */
function wontrapi() {
	return Wontrapi::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( wontrapi(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( wontrapi(), '_activate' ) );
register_deactivation_hook( __FILE__, array( wontrapi(), '_deactivate' ) );

// Create a helper function for easy SDK access.
function wontrapi_fs() {
	global $wontrapi_fs;

	if ( ! isset( $wontrapi_fs ) ) {
		// Include Freemius SDK.
		require_once dirname(__FILE__) . '/vendor/freemius/start.php';

		$wontrapi_fs = fs_dynamic_init( array(
			'id'                  => '1284',
			'slug'                => 'wontrapi',
			'premium_slug'        => 'wontrapi',
			'type'                => 'plugin',
			'public_key'          => 'pk_f3f99e224cd062ba9d7fda46ab973',
			'is_premium'          => false,
			'is_premium_only'     => false,
			'has_premium_version' => true,
			'has_addons'          => true,
			'has_paid_plans'      => true,
			'bundle_id'           => '10544',
			'bundle_public_key'   => 'pk_73d21efcf48b1c6aa2b43e4f9c27a',
			'bundle_license_auto_activation' => true,
			'menu'                => array(
				'slug'           => 'wontrapi_options',
				'support'        => false,
			),
			'secret_key'          => 'sk_7pYN.l]gEC:UR=oxfa:.C;o9O$)Vg',

		) );
	}

	return $wontrapi_fs;
}

// Init Freemius.
// wontrapi_fs();
// Signal that SDK was initiated.
do_action( 'wontrapi_fs_loaded' );




if ( ! class_exists( 'Wontrapi_Updater' ) ) {
	// load our custom updater
	include dirname( __FILE__ ) . './inc/class-updater.php';
}

/**
 * Initialize the updater. Hooked into `init` to work with the
 * wp_version_check cron job, which allows auto-updates.
 */
function wontrapi_plugin_updater() {

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'wontrapi_license_key' ) );

	// setup the updater
	$wontrapi_updater = new Wontrapi_Updater(
		WONTRAPI_STORE_URL,
		__FILE__,
		array(
			'version' => WONTRAPI_VERSION,                    // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => WONTRAPI_ITEM_ID,       // ID of the product
			'author'  => 'Easy Digital Downloads', // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'init', 'wontrapi_plugin_updater' );


/************************************
* the code below is just a standard
* options page. Substitute with
* your own.
*************************************/

/**
 * Adds the plugin license page to the admin menu.
 *
 * @return void
 */
function wontrapi_license_menu() {
	add_plugins_page(
		__( 'Plugin License' ),
		__( 'Plugin License' ),
		'manage_options',
		WONTRAPI_PLUGIN_LICENSE_PAGE,
		'wontrapi_license_page'
	);
}
add_action( 'admin_menu', 'wontrapi_license_menu' );

function wontrapi_license_page() {
	add_settings_section(
		'wontrapi_license',
		__( 'Plugin License' ),
		'wontrapi_license_key_settings_section',
		WONTRAPI_PLUGIN_LICENSE_PAGE
	);
	add_settings_field(
		'wontrapi_license_key',
		'<label for="wontrapi_license_key">' . __( 'License Key' ) . '</label>',
		'wontrapi_license_key_settings_field',
		WONTRAPI_PLUGIN_LICENSE_PAGE,
		'wontrapi_license',
	);
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Plugin License Options' ); ?></h2>
		<form method="post" action="options.php">

			<?php
			do_settings_sections( WONTRAPI_PLUGIN_LICENSE_PAGE );
			settings_fields( 'wontrapi_license' );
			submit_button();
			?>

		</form>
	<?php
}

/**
 * Adds content to the settings section.
 *
 * @return void
 */
function wontrapi_license_key_settings_section() {
	esc_html_e( 'This is where you enter your license key.' );
}

/**
 * Outputs the license key settings field.
 *
 * @return void
 */
function wontrapi_license_key_settings_field() {
	$license = get_option( 'wontrapi_license_key' );
	$status  = get_option( 'wontrapi_license_status' );

	?>
	<p class="description"><?php esc_html_e( 'Enter your license key.' ); ?></p>
	<?php
	printf(
		'<input type="text" class="regular-text" id="wontrapi_license_key" name="wontrapi_license_key" value="%s" />',
		esc_attr( $license )
	);
	$button = array(
		'name'  => 'wontrapi_license_deactivate',
		'label' => __( 'Deactivate License' ),
	);
	if ( 'valid' !== $status ) {
		$button = array(
			'name'  => 'wontrapi_license_activate',
			'label' => __( 'Activate License' ),
		);
	}
	wp_nonce_field( 'wontrapi_nonce', 'wontrapi_nonce' );
	?>
	<input type="submit" class="button-secondary" name="<?php echo esc_attr( $button['name'] ); ?>" value="<?php echo esc_attr( $button['label'] ); ?>"/>
	<style>
		#wpbody-content p.submit {
			display: none;
		}
	</style>
	<?php
}

/**
 * Registers the license key setting in the options table.
 *
 * @return void
 */
function wontrapi_register_option() {
	register_setting( 'wontrapi_license', 'wontrapi_license_key', 'wontrapi_sanitize_license' );
}
add_action( 'admin_init', 'wontrapi_register_option' );

/**
 * Sanitizes the license key.
 *
 * @param string  $new The license key.
 * @return string
 */
function wontrapi_sanitize_license( $new ) {
	$old = get_option( 'wontrapi_license_key' );
	if ( $old && $old !== $new ) {
		delete_option( 'wontrapi_license_status' ); // new license has been entered, so must reactivate
	}

	return sanitize_text_field( $new );
}

/**
 * Activates the license key.
 *
 * @return void
 */
function wontrapi_activate_license() {

	// listen for our activate button to be clicked
	if ( ! isset( $_POST['wontrapi_license_activate'] ) ) {
		return;
	}

	// run a quick security check
	if ( ! check_admin_referer( 'wontrapi_nonce', 'wontrapi_nonce' ) ) {
		return; // get out if we didn't click the Activate button
	}

	// retrieve the license from the database
//	$license = trim( get_option( 'wontrapi_license_key' ) );
//	if ( ! $license ) {
//		$license = ! empty( $_POST['wontrapi_license_key'] ) ? sanitize_text_field( $_POST['wontrapi_license_key'] ) : '';
//	}
	$license = ! empty( $_POST['wontrapi_license_key'] ) ? sanitize_text_field( $_POST['wontrapi_license_key'] ) : '';
	if ( ! $license ) {
		$license = trim( get_option( 'wontrapi_license_key' ) );
	}
	if ( ! $license ) {
		return;
	}

	// data to send in our API request
	$api_params = array(
		'edd_action'  => 'activate_license',
		'license'     => $license,
		'item_id'     => WONTRAPI_ITEM_ID,
		'item_name'   => rawurlencode( WONTRAPI_ITEM_NAME ), // the name of our product in EDD
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		WONTRAPI_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

		// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$message = __( 'An error occurred, please try again.' );
			print_r($response);
		}
	} else {

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success ) {

			switch ( $license_data->error ) {

				case 'expired':
					$message = sprintf(
						/* translators: the license key expiration date */
						__( 'Your license key expired on %s.', 'wontrapi' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'disabled':
				case 'revoked':
					$message = __( 'Your license key has been disabled.', 'wontrapi' );
					break;

				case 'missing':
					$message = __( 'Invalid license.', 'wontrapi' );
					break;

				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', 'wontrapi' );
					break;

				case 'item_name_mismatch':
					/* translators: the plugin name */
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'wontrapi' ), WONTRAPI_ITEM_NAME );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'wontrapi' );
					break;

				default:
					$message = __( 'An error occurred, please try again.', 'wontrapi' );
					break;
			}
		}
	}

	// Check if anything passed on a message constituting a failure
	if ( ! empty( $message ) ) {
		$redirect = add_query_arg(
			array(
				'page'          => WONTRAPI_PLUGIN_LICENSE_PAGE,
				'sl_activation' => 'false',
				'message'       => rawurlencode( $message ),
			),
			admin_url( 'plugins.php' )
		);

		wp_safe_redirect( $redirect );
		exit();
	}

	// $license_data->license will be either "valid" or "invalid"
	if ( 'valid' === $license_data->license ) {
		update_option( 'wontrapi_license_key', $license );
		update_option( 'wontrapi_license_status', $license_data->license );

		$message = __( 'Success!', 'wontrapi' );
	
		$redirect = add_query_arg(
			array(
				'page'          => WONTRAPI_PLUGIN_LICENSE_PAGE,
				'sl_activation' => 'true',
				'message'       => rawurlencode( $message ),
			),
			admin_url( 'plugins.php' )
		);
		wp_safe_redirect( $redirect );
		exit();
	}

	wp_safe_redirect( admin_url( 'plugins.php?page=' . WONTRAPI_PLUGIN_LICENSE_PAGE ) );
	exit();

}
add_action( 'admin_init', 'wontrapi_activate_license' );

/**
 * Deactivates the license key.
 * This will decrease the site count.
 *
 * @return void
 */
function wontrapi_deactivate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['wontrapi_license_deactivate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'wontrapi_nonce', 'wontrapi_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		// retrieve the license from the database
		$license = trim( get_option( 'wontrapi_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'  => 'deactivate_license',
			'license'     => $license,
			'item_id'     => WONTRAPI_ITEM_ID,
			'item_name'   => rawurlencode( WONTRAPI_ITEM_NAME ), // the name of our product in EDD
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post(
			WONTRAPI_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			$redirect = add_query_arg(
				array(
					'page'          => WONTRAPI_PLUGIN_LICENSE_PAGE,
					'sl_activation' => 'false',
					'message'       => rawurlencode( $message ),
				),
				admin_url( 'plugins.php' )
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( 'deactivated' === $license_data->license ) {
			delete_option( 'wontrapi_license_key' );
			delete_option( 'wontrapi_license_status' );
		}

		wp_safe_redirect( admin_url( 'plugins.php?page=' . WONTRAPI_PLUGIN_LICENSE_PAGE ) );
		exit();

	}
}
add_action( 'admin_init', 'wontrapi_deactivate_license' );

/**
 * Checks if a license key is still valid.
 * The updater does this for you, so this is only needed if you want
 * to do somemthing custom.
 *
 * @return void
 */
function wontrapi_check_license() {

	$license = trim( get_option( 'wontrapi_license_key' ) );

	$api_params = array(
		'edd_action'  => 'check_license',
		'license'     => $license,
		'item_id'     => WONTRAPI_ITEM_ID,
		'item_name'   => rawurlencode( WONTRAPI_ITEM_NAME ),
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		WONTRAPI_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( 'valid' === $license_data->license ) {
		echo 'valid';
		exit;
		// this license is still valid
	} else {
		echo 'invalid';
		exit;
		// this license is no longer valid
	}
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function wontrapi_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {
		$message = urldecode( $_GET['message'] );

		switch ( $_GET['sl_activation'] ) {

			case 'false':
				// $message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo wp_kses_post( $message ); ?></p>
				</div>
				<?php
				break;

			case 'true':
				?>
				<div class="notice notice-success">
					<p><?php echo wp_kses_post( $message ); ?></p>
				</div>
				<?php
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}

	if ( 'valid' !== get_option( 'wontrapi_license_status' ) ) { 
		?>
		<div class="notice notice-warning">
			<p><?php _e( 'Please enter your license for Wontrapi or get a new one', 'wontrapi' ); ?></p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'wontrapi_admin_notices' );

