<?php
/**
 * Collab Mendeley Plugin
 *
 * @package   CollabMendeleyPlugin
 * @author    Davide Parisi <davideparisi@gmail.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 --
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-collab-mendeley-plugin-admin.php`
 *
 * @package CollabMendeleyPlugin
 * @author  Davide Parisi <davideparisi@gmail.com>
 *
 */

date_default_timezone_set( get_option( 'timezone_string' ) != '' ? get_option( 'timezone_string' ) : 'Europe/Rome' );

class CollabMendeleyPlugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'collab-mendeley-plugin';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'register_shortcode' ) );

		add_action( 'mendeley_download', array( $this, 'download_file' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	public function download_file(){
		/*
		$curl = curl_init("https://api.mendeley.com:443/files/".$idfile);
  
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer MSwxNDMyNTkyMTIxNTgxLDMxNzQwODg1MSwxODQxLGFsbCwsb212eTM1ckZLTThKR3BDSG9jQnVKd1VEUjFv"));
		$auth = curl_exec($curl);
		echo $auth;
		*/
		echo "TEST ACTION";
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		//todo: define single activation functionality
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/*----------------------------------------------------------------------------/
	 *
	 * Process Shortcode
	 *
	 *---------------------------------------------------------------------------*/
	public function register_shortcode() {
		add_shortcode( 'mendeley', array( $this, 'authored_publications' ) );
	}

	public function authored_publications( $atts, $content = null ) {
		$titletag   = $atts['titletag'];
		$sectiontag = $atts['sectiontag'];
		if ( isset( $titletag ) ) {
			$return_string = '<' . $titletag . '>' . $content . '</' . $titletag . '>';
			$return_string .= '<br/>';
		}
		$publications = $this->get_publications();
		$main_author  = $this->get_account_info();
		//print_r($main_author); echo $main_author;

		$author       = explode( " ", ucwords( $main_author['display_name'] ) );
		

		if ( ! isset( $publications['data'] ) || $publications == false ) {
			$publications = $this->get_remote_publications();
		}
		$types   = array_keys( $publications['data'] );
		$weights = $this->get_document_type_weight( $types );
		asort( $weights );
		foreach ( $weights as $type => $order ) {

			$documents = $publications['data'][ $type ];
			usort( $documents, function ( $a, $b ) {
				return strcmp( $b->year, $a->year );
			} );

			$formatted_alt = DocumentFormatter::custom_format( $documents, $author );
			//$formatted_alt = DocumentFormatter::format( $documents, null, null, $author );


			if ( $type != 'Conference Proceedings' ) {
				$return_string .= '<' . $sectiontag . '>' . $type . $this->plural( sizeof( $documents ) ) . '</' . $sectiontag . '><br/>';
			} else {
				$return_string .= '<' . $sectiontag . '>' . $type . '</' . $sectiontag . '><br/>';
			}


			$return_string .= $formatted_alt;
		}

		return $return_string;
	}

	private function get_document_type_weight( $types ) {
		$weights = array();
		if ( ! isset( $this->weights ) ) {
			$this->weights = array(
				'Journal Article'        => 1,
				'Magazine Article'       => 2,
				'Book'                   => 3,
				'Book Section'           => 4,
				'Conference Proceedings' => 5
			);
		}

		foreach ( $types as $type ) {
			if ( isset( $this->weights[ $type ] ) ) {
				$weights[ $type ] = $this->weights[ $type ];
			} else {
				$weights[ $type ] = 999;
			}
		}

		return $weights;

	}

	private function get_account_info() {
		$info = get_option($this->plugin_slug . '-account-info');
		
		return $info;
	}

	private function get_access_token() {
		$options = $this->get_options();

		return $options['access_token']['result']['access_token'];
	}

	private function get_publications() {
		// get the stored options
		$options = $this->get_options();

		
		// if publications in cache
		if ( isset( $options['cache'] ) ) {
			if ( $options['cache'] == true ) // return the cached publications
			{ 
				return $this->get_cached_publications();
			}
			return $this->get_remote_publications();
		}

		return $this->get_remote_publications();
	}

	private function get_cached_publications() {
		//$publications = get_option( $this->plugin_slug . '-cache' );
		$publications = get_option( $this->plugin_slug . '-cache' );

		return $publications;
	}

	private function get_remote_publications() {
 
		$options = $this->get_options();
		if ( false == $options ) { // if cannot get options
			return; // exit and do nothing
		}
		$token_data_array = $options['access_token']['result'];
		if ( ! isset( $token_data_array ) ) {
			//@todo: perhaps returning an empty string should be better...
			return "you must set up mendeley plugin before using this shortcode...";
		}

		$token = $token_data_array['access_token'];

		$client = MendeleyApi::get_instance();
		$client->set_up(
			$options['client_id'],
			$options['client_secret'],
			admin_url( 'options-general.php?page=' . $this->plugin_slug )
		);

		if ( time() > $options['expire_time'] ) {
			$response                = $client->refresh_access_token( $token_data_array['refresh_token'] );
			$options['access_token'] = $response;
			$this->update_options( $options );

			if ( $response['code'] != 200 ) { // if there is a problem with the response
				// @FIXME: Manage this situation
				return ''; // return a void string and do no harm...
			}

			$token_data = $response['result'];
			$token      = $token_data['access_token'];
		}


		$client->set_client_access_token( $token );
		$publications = $client->get_authored_publications();
		// set the cache
		//print_r($publications);
		$options['account-info'] = $client->get_account_info();
		$options['cache']        = false;  // true, forzato a false
		$this->update_options( $options );
		add_option( $this->plugin_slug . '-cache', $publications );

		return $publications;
	}

	/*----------------------------------------------------------------------------/
	 *
	 * Utilities
	 *
	 *---------------------------------------------------------------------------*/

	private function get_options() {

		$opts = get_option( $this->plugin_slug );

		return $opts;
	}

	private function update_options( $options ) {

		update_option( $this->plugin_slug, $options );
	}

	private function plural( $amount, $singular = '', $plural = 's' ) {
		if ( $amount == 1 ) {
			return $singular;
		} else {
			return $plural;
		}
	}
}
