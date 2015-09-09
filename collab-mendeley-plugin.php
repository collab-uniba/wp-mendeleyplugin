<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   CollabMendeleyPlugin
 * @author    Davide Parisi <davideparisi@gmail.com>, Nicola Musicco <nicolamc@hotmail.it>, Gabriele Cianciaruso <infogabry@gmail.com>
 * @license   MIT
 *
 * @wordpress-plugin
 * Plugin Name:       Collab Mendeley Plugin
 * Plugin URI:        https://github.com/collab-uniba/wp-mendeleyplugin
 * Description:       Collab Mendeley Plugin
 * Version:           2.0.0
 * Author:            Davide Parisi, Nicola Musicco, Gabriele Cianciaruso
 * Author URI:        https://github.com/collab-uniba/wp-mendeleyplugin
 * Text Domain:       collab-mendeley-plugin-locale
 * License:           MIT
 * License URI:
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/collab-uniba/wp-mendeleyplugin
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-collab-mendeley-plugin.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'CollabMendeleyPlugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CollabMendeleyPlugin', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'CollabMendeleyPlugin', 'get_instance' ) );


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-collab-mendeley-plugin-admin.php` with the name of the plugin's admin file
 * - replace CollabMendeleyPluginAdmin with the name of the class defined in
 *   `class-collab-mendeley-plugin-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-collab-mendeley-plugin-admin.php' );
	add_action( 'plugins_loaded', array( 'CollabMendeleyPluginAdmin', 'get_instance' ) );

}

/*----------------------------------------------------------------------------*
 * Mendeley API Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-mendeley-api.php' );
add_action( 'plugins_loaded', array( 'MendeleyApi', 'get_instance' ) );


/*----------------------------------------------------------------------------*
 * Documents Formatting (using CiteProc)
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-document-formatter.php' );
add_action( 'plugins_loaded', array( 'DocumentFormatter', 'get_instance' ) );

if($_POST['action']=='mendeley_download'){
	$curl = curl_init("https://api.mendeley.com:443/files/".$_POST['idfile']);
  	$r = get_option( 'collab-mendeley-plugin' );

	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_VERBOSE, true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$r['access_token']['result']['access_token']));
	$auth = curl_exec($curl);
	$donwload = curl_getinfo($curl);
	
	header('Location: '.$donwload['redirect_url']);
	
    die;
	
}