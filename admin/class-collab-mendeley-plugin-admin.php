<?php
/**
 * Collab Mendeley Plugin
 *
 * @package   CollabMendeleyPluginAdmin
 * @author    Davide Parisi <davideparisi@gmail.com>, Gabriele Cianciaruso <infogabry@gmail.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 --
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-collab-mendeley-plugin.php`
 *
 * @package CollabMendeleyPluginAdmin
 * @author  Davide Parisi <davideparisi@gmail.com>, Gabriele Cianciaruso <infogabry@gmail.com>
 */
/*if ( ! class_exists( "MendeleyApi" ) ) {
	require_once plugin_dir_path( __DIR__ ) . "includes/class-mendeley-api.php";
}*/


class CollabMendeleyPluginAdmin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	protected $options = null;

	//protected $client = null;

	protected $callback_url = '';


	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		$this->init();


		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		add_action( 'admin_action_request_token', array( $this, 'request_access_token' ) );
		add_action( 'admin_action_import_publications', array( $this, 'import_authored_publications' ) );

		// add contextual help
		add_filter( 'contextual_help', array( $this, 'show_help' ) );

		add_action( 'admin_init', array( $this, 'initialize_options' ) );

		// tinyMCE buttons
		add_action( 'admin_head', array( $this, 'add_tinymce_buttons' ) );
	}


	public function init() {
		$plugin             = CollabMendeleyPlugin::get_instance();
		$this->plugin_slug  = $plugin->get_plugin_slug();
		$this->callback_url = admin_url( 'options-general.php?page=' . $this->plugin_slug );
		$options      		= $this->get_options();
		$actoken            = $options['access-token'];
		
		if ( empty( $actoken ) ) {
			$this->check_access_token();
		}
	}


	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), CollabMendeleyPlugin::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), CollabMendeleyPlugin::VERSION );
		}

	}

	/* ------------------------------------------------------------------------ *
    * Setting Registration
    * ------------------------------------------------------------------------ */

	public function default_keys_options() {
		$defaults = array(
			'client_id'     => '',
			'client_secret' => '',
		);

		return apply_filters( 'default_keys_options', $defaults );
	}

	public function initialize_options() {

		add_option( $this->plugin_slug, apply_filters( 'default_keys_options', $this->default_keys_options() ) );


		add_settings_section(
			'collab_mendeley_settings_section',
			'API Key Setting',
			array( $this, 'options_callback' ),
			$this->plugin_slug
		);

		add_settings_field(
			'client_id',
			'Client ID',
			array( $this, 'client_id_input_callback' ),
			$this->plugin_slug,
			'collab_mendeley_settings_section',
			array( 'Insert the client ID' )
		);

		add_settings_field(
			'client_secret',
			'Client Secret',
			array( $this, 'client_secret_input_callback' ),
			$this->plugin_slug,
			'collab_mendeley_settings_section',
			array( 'Insert the client secret' )
		);

		register_setting(
			$this->plugin_slug,
			$this->plugin_slug,
			array( $this, 'validate' )
		);
	}

	public function options_callback() {
		echo '<p class="description">Enter the <code>client ID</code> and <code>client secret</code> you have got from registering this plugin on <a href="http://dev.mendeley.com">Mendeley</a> (see contextual help tab above)</p>';
	}

	public function client_id_input_callback( $args ) {
		$options = $this->get_options();
		$html    = '<input type="text" id="client_id" name="' . $this->plugin_slug . '[client_id]" value="' . $options['client_id'] . '" />';
		echo $html;
	}

	public function client_secret_input_callback( $args ) {
		$options = $this->get_options();
		$html    = '<input type="password" id="client_secret" name="' . $this->plugin_slug . '[client_secret]" value="' . $options['client_secret'] . '" />';
		echo $html;
	}

	public function validate( $input ) {
		$output = array();
		foreach ( $input as $key => $value ) {
			if ( isset( $input[ $key ] ) ) {
				if ( $key == 'access_token' ) {
					$output[ $key ] = $input[ $key ];
				} else {
					$output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );
				}
			}
		}

		return apply_filters( 'validate', $output, $input );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Collab Mendeley Plugin', $this->plugin_slug ),
			__( 'Mendeley Settings', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		if ( isset( $_GET['code'] ) ) {
			$this->store_access_token( $_GET['code'] );
		}
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}


	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function show_help() {
		$screen = get_current_screen();
		$screen->add_help_tab( array(
			'id'       => 'plugin_options_help',
			'title'    => 'Setup',
			'content'  => "<h2>Mendeley Plugin Setup</h2>",
			'callback' => array( $this, 'show_help_content' )
		) );
	}

	public function show_help_content() {
		$html = '<p>All calls to the Mendeley API require authentication using the <a href="http://oauth.net/2/">OAuth 2.0
			protocol</a>.</p>';
		$html .= '<p>In order to begin using the API a client must first register their application with the <a href="http://dev.mendeley.com">authorization server</a>.</p>';
		$html .= '
			<p>
				You have to:
				<ol>
					<li>Copy the redirect url below</li>
					<li>Register this plugin on <a href="http://dev.mendeley.com">Mendeley</a></li>
					<li>Get the generated secret code and the client id</li>
					<li>Insert client id and secret in the form below and store them to db</li>
					<li>Click on request token</li>
				</ol>
				<p><em>Once you have token, Import Publications button, save on local cache documents details from your <b>Mendeley account</b></em></p>
			</p>
		';

		echo $html;
	}

	public function request_access_token() {
		$options = $this->get_options();
		if ( $options['client_id'] === '' || $options['client_secret'] === '' ) {
			//@todo: do something if keys are void, this can happen when someone clicks on "request access token" before id and secret are set
			return;
		}
		// get setted client instance
		$client = $this->set_up_client( $options );

		// Redirect to mendeley login page
		$client->start_authorization_flow();
	}

	public function import_authored_publications() {
		$url     = $_SERVER['HTTP_REFERER'];
		$options = $this->get_options();
		if ( ! isset( $options ) || false == $options ) { // if cannot get options
			return; // exit and do nothing
		}
		if ( time() > $options['expire_time'] ) {
			$this->refresh_token();
			$options = $this->get_options();
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
		$client->set_client_access_token( $token );
		$publications = $client->get_authored_publications();
		$author_info  = $client->get_account_info();
		// set the cache
		//$options['cache'] = true;  // DISABILITATO...vedi 5 righe sotto
		add_option( $this->plugin_slug . '-account-info', $author_info );
		$dt                     = new DateTime();
		$options['last-import'] = $dt->format( 'd-m-Y H:i:s' );
		$this->update_options( $options );
		add_option( $this->plugin_slug . '-cache', $publications );

		wp_redirect( $url );
		exit;

	}

	public function store_access_token( $auth_code ) {

		$options      = $this->get_options();
		$client       = $this->set_up_client( $options );
		$access_token = $client->get_access_token( $auth_code );
		//print_r($client);
		if ( $access_token['code'] === 200 ) {
			$options['access_token'] = $access_token;
			$access_token_data       = $options['access_token']['result'];
			$expire_time             = ( time() + $access_token_data['expires_in'] );
			$expire_time_humanized   = date( 'd-n-Y H:i:s', $expire_time );
			$options['expire_time']  = $expire_time;
			$options['et_humanized'] = $expire_time_humanized;
			$this->update_options( $options );
		}/*else{
			echo "Debug info:<code>";
			print_r($access_token);	
			echo "</code>";
		}*/

	}

	public function check_access_token() {
		$access_token_data = $this->get_options();
		
		if ( time() > $access_token_data['expire_time'] ) {
			$this->refresh_token();
		}
	}

	public function refresh_token() {
		$options       = $this->get_options();
		$client        = $this->set_up_client( $options );
		$result        = $options['access_token']['result'];
		$refresh_token = $result['refresh_token'];

		$client->set_up( $options['client_id'], $options['client_secret'], $this->callback_url );
		$new_token               = $client->refresh_access_token( $refresh_token );
		$options['access_token'] = $new_token;
		$access_token_data       = $options['access_token']['result'];
		$expire_time             = ( time() + $access_token_data['expires_in'] );
		$expire_time_humanized   = date( 'd-n-Y H:i:s', $expire_time );
		$options['expire_time']  = $expire_time;
		$options['et_humanized'] = $expire_time_humanized;
		$this->update_options( $options );
	}

	// register tinyMCE custom button(s)
	public function add_tinymce_buttons() {
		global $typenow;
		// check user permission
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		// verify post type
		if ( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
			return;
		}
		// check if WYSIWYG is enabled
		if ( get_user_option( 'rich_editing' ) == true ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_tinymce_buttons' ) );
		}
	}


	public function add_tinymce_plugin( $plugin_array ) {
		$plugin_array['collab_mendeley'] = plugins_url( 'assets/js/tinymce_plugin.js', __FILE__ );

		return $plugin_array;
	}

	public function register_tinymce_buttons( $buttons ) {
		array_push( $buttons, 'collab_mendeley_button' );

		return $buttons;
	}

	/*------------------------------------------------------------------------------
	 *
	 * Private Functions/utilities
	 *
	 -----------------------------------------------------------------------------*/

	/**
	 * Update options array with db data (if present)
	 *
	 * @return null
	 */
	public function get_options() {
		$opts = get_option( $this->plugin_slug );

		return $opts;
	}

	/**
	 * Simple wrapper for the update_option wordpress function
	 *
	 * @param $options
	 */
	private function update_options( $options ) {
		update_option( $this->plugin_slug, $options );

	}


	private function set_up_client( $options ) {
		$client = MendeleyApi::get_instance();
		$client->set_up(
			$options['client_id'],
			$options['client_secret'],
			$this->callback_url
		);
		$client->init();

		return $client;
	}
}
