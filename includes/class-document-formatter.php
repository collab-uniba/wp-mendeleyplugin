<?php
/**
 * Created by PhpStorm.
 * User: davide
 * Date: 10/07/14
 * Time: 17:23
 */

if ( ! class_exists( "citeproc" ) ) {
	include_once( 'CiteProc.php' );
}

class DocumentFormatter {

	protected static $instance = null;

	const CSL_STYLE = 'apa';

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function format( $documents_array, $cls_path = false, $mode = false ) {

		if ( ! false == $cls_path ) {
			$csl = file_get_contents( $cls_path );
		} else {
			$csl = file_get_contents( plugin_dir_path( __DIR__ ) . 'assets/csl/' . self::CSL_STYLE . '.csl' ); //association-for-computing-machinery.csl
		}

		$cp     = new citeproc( $csl, 'it' );
		$render = '';
		foreach ( $documents_array as $document ) {
			if (!false == $mode ){
				$render .= $cp->render( $document, $mode );
			}else{
				$render .= $cp->render( $document, 'bibliography' );
			}
		}

		return $render;
	}


} 