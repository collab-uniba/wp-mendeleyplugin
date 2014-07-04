<?php
/**
 * Collab Mendeley Plugin
 *
 * @package   CollabMendeleyPluginAdmin
 * @author    Davide Parisi <davideparisi@gmail.com>
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
 * @author  Davide Parisi <davideparisi@gmail.com>
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/vendor/autoload.php';
define( 'AUTHORIZE_ENDPOINT', "https://api-oauth2.mendeley.com/oauth/authorize" );
define( 'TOKEN_ENDPOINT', "https://api-oauth2.mendeley.com/oauth/token" );

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

    protected $client_config = null;

    protected $api = null;


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

		$plugin = CollabMendeleyPlugin::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
        $this->options = $this->get_options();


		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'admin_action_set_keys', array( $this, 'store_keys' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

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
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), CollabMendeleyPlugin::VERSION );
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
        if ( $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET['code'] ) ) {
            $this->get_access_token();
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
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function store_keys() {
		$client_id = $_POST['client-id'];
        $client_secret = $_POST['client-secret'];
        $this->options['client-id'] = $client_id;
        $this->options['client-secret'] = $client_secret;
        update_option( $this->plugin_slug, $this->options );
        $this->send_authorization_request();
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
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

    /*------------------------------------------------------------------------------
     *
     * Private Functions
     *
     -----------------------------------------------------------------------------*/

    /**
     * @return null
     */
    private function get_options() {
        // if $options is already present return $options
        if ( isset( $this->options ) ) {
            return $this->options;
        }
        // check if options are in the db and store them in $this->options
        $tmp_options = get_option( $this->plugin_slug );
        if ( isset( $tmp_options ) ) {
            return $tmp_options;
        } else {
            // otherwise initialize to an empty array
            $this->options = array();
            add_option( $this->plugin_slug, $this->options );
            return $this->options;
        }

    }

    private function send_authorization_request() {
        $this->get_config();
        $this->api = new \fkooman\OAuth\Client\Api('mendeley', $this->client_config, new \fkooman\OAuth\Client\SessionStorage(), new \Guzzle\Http\Client());
        $context = new \fkooman\OAuth\Client\Context( (String) get_current_user_id(), array( 'all' ) );
        $access_token = $this->api->getAccessToken( $context );
        if ( false === $access_token ) {
            wp_redirect( $this->api->getAuthorizeUri( $context ) );
            exit();
        }
        $this->options['access-token'] = $access_token;
        $this->update_options( $this->options );
        $api_url = 'https://mix.mendeley.com/documents?type=own';
        try {
            $client = new \Guzzle\Http\Client();
            $bearer_auth = new \fkooman\Guzzle\Plugin\BearerAuth\BearerAuth($access_token->getAccessToken());
            $client->addSubscriber($bearer_auth);
            $response = $client->get($api_url)->send();

            header("Content-Type: application/json");
            echo $response->getBody();
        } catch (\fkooman\Guzzle\Plugin\BearerAuth\Exception\BearerErrorResponseException $e) {
            if ("invalid_token" === $e->getBearerReason()) {
                // the token we used was invalid, possibly revoked, we throw it away
                $this->api->deleteAccessToken($context);
                $this->api->deleteRefreshToken($context);

                /* no valid access token available, go to authorization server */
                header("HTTP/1.1 302 Found");
                header("Location: " . $this->api->getAuthorizeUri($context));
                exit;
            }
            throw $e;
        }
    }

    private function get_access_token() {
        $this->get_config();
        $cb = new \fkooman\OAuth\Client\Callback('mendeley', $this->client_config, new \fkooman\OAuth\Client\SessionStorage(), new \Guzzle\Http\Client());
        $cb->handleCallback($_GET);
        // wp_redirect($this->client_config->getRedirectUri() );
        // exit();
    }

    private function get_config() {
        $this->client_config = new \fkooman\OAuth\Client\ClientConfig(
            array(
                'authorize_endpoint'    => AUTHORIZE_ENDPOINT,
                'token_endpoint'        => TOKEN_ENDPOINT,
                'client_id'             => $this->options['client-id'],
                'client_secret'         => $this->options['client-secret'],
                'redirect_uri'          => admin_url('options-general.php?page=' . $this->plugin_slug)
            )
        );
    }

    private function update_options( $options ) {
        update_option( $this->plugin_slug, $options );
    }

}
