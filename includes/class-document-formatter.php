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

	const CSL_STYLE = 'ieee';

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
			if ( ! false == $mode ) {
				$render .= $cp->render( $document, $mode );
			} else {
				$render .= $cp->render( $document, 'bibliography' );
			}
		}

		return $render;
	}

	public static function custom_format( $documents, $main_author, $access_token ) {
		$formatted = '<ol class="collab-mendeley-authored-list">';

		foreach ( $documents as $doc ) {
			$html = '<li>(<span class="collab-mendeley-year">' . $doc['year'] . '</span>) ';
			foreach ( $doc['authors'] as $author ) {
				$acronym = self::make_acronym( $author['forename'] );
				if ( ( $author['surname'] == $main_author[1] ) && ( $acronym == self::make_acronym( $main_author[0] ) ) ) {
					$html .= '<span class="collab-mendeley-author">' . $acronym . ' ' . $author['surname'] . '</span>, ';
				} else {
					$html .= $acronym . ' ' . $author['surname'] . ', ';
				}
			}

			$html .= '<a class="collab-mendeley-title" href="https://api.mendeley.com/files/' . $doc['canonical_id'] . '" target="_blank" rel="nofollow">' . $doc['title'] . '</a>, ';

			if ( isset($doc['publisher']) ) {
				$html .= '<span class="collab-mendeley-publisher">(' . $doc['publisher'] .'</span>),';
			}

			if ( isset( $doc['published_in'] ) ) {
				$html .= ' <span class="collab-mendeley-published-in">' . $doc['published_in'] .'</span>,';
			}


			if ( isset( $doc['identifiers'] ) ) {
				foreach ($doc['identifiers'] as $identifier => $value){
					if ($identifier == 'doi'){
						$html .= ' <span class="collab-mendeley-identifiers"> DOI:</span> <a href="http://dx.doi.org/' . $value . '" target="_blank" rel="nofollow">' . $value . '</a>';
					}else{
						$html .= "<span class='collab-mendeley-identifiers'> {$identifier}:</span> {$value}";
					}

				}
			}

			$html . '<li/>';
			$formatted .= $html;

		}
		$formatted .= '</ol>';


		return $formatted;
	}

	private static function make_acronym( $text ) {
		$words   = explode( " ", $text );
		$acronym = "";

		foreach ( $words as $w ) {
			$acronym .= $w[0];
		}

		return $acronym;
	}


} 