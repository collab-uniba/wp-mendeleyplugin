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

	public static function format( $documents_array, $cls_path = false, $mode = false, $author = false ) {

		if (isset($cls_path) && ! false == $cls_path ) {
			$csl = file_get_contents( $cls_path );
		} else {
			$csl = file_get_contents( plugin_dir_path( __DIR__ ) . 'assets/csl/' . self::CSL_STYLE . '.csl' ); //association-for-computing-machinery.csl
		}

		$cp     = new citeproc( $csl, 'en' );
		$render = '<ol class="collab-mendeley-authored-list">';
		foreach ( $documents_array as $document ) {
			$render .= '<li>';
			foreach ( $document->author as $auth ) {
				/*if (self::same_author($auth, $author)){
					$render .= ' <strong>'. $auth['given'] . ' ' . $auth['family'] . '</strong>';
				} else {
					$render .= ' ' . $auth['given'] . ' ' . $auth['family'];
				}*/

				$render .= ' ' . $auth['given'] . ' ' . $auth['family'];
				$render .= ', ';
			}

			if ( ! false == $mode ) {
				$render .= $cp->render( $document, $mode );
			} else {
				$render .= $cp->render( $document, 'bibliography' );
			}
			if (isset($document->DOI)) {
				$render .= ' <span class="collab-mendeley-doi">DOI: <a href="http://dx.doi.org/' . $document->DOI . '" target="_blank" rel="nofollow">' . $document->DOI . '</a></span>';
			}
			$render .= '</li><br/>';


		}
		$render .= '</ol>';

		return $render;
	}

	public static function custom_format( $documents, $main_author ) {
		$formatted              = '<ol class="collab-mendeley-authored-list">';
		$main_author_name_array = explode( " ", ucwords( $main_author['name'] ) );
		$main_author_forename   = $main_author_name_array[0];
		$main_author_surname    = $main_author_name_array[1];
		$profile_id             = $main_author['profile_id'];
		foreach ( $documents as $doc ) {
			$title = $doc['title'];
			$html  = '<li>(<span class="collab-mendeley-year">' . $doc['year'] . '</span>) ';
			foreach ( $doc['authors'] as $author ) {
				$acronym = self::make_acronym( $author['forename'] );
				if ( ( $author['surname'] == $main_author_surname ) && ( $acronym == self::make_acronym( $main_author_forename ) ) ) {
					$html .= '<span class="collab-mendeley-author">' . $acronym . ' ' . $author['surname'] . '</span>, ';
				} else {
					$html .= $acronym . ' ' . $author['surname'] . ', ';
				}
			}

			// A Planning Poker Tool for Supporting Collaborative Estimation in Distributed Agile Development
			// http://www.mendeley.com/download/personal/12946271/5511616284/94a178314e0e005156697b065f85c8f8f6b6c384/dl.pdf

			/*if (isset($doc['files']) && !empty($doc['files'])){
				$file_hash = $doc['files'];
				$url = self::get_file_url($profile_id, $doc['id'], $file_hash[0]['file_hash']);
				$html .= '<a class="collab-mendeley-title" href="' . $url . '" target="_blank" rel="nofollow">' . $doc['title'] . '</a>, ';
			} else {*/
			$html .= '<a class="collab-mendeley-title" href="' . $doc['mendeley_url'] . '" target="_blank" rel="nofollow">' . $doc['title'] . '</a>, ';
			//}

			if ( isset( $doc['publisher'] ) ) {
				$html .= '<span class="collab-mendeley-publisher">(' . $doc['publisher'] . '</span>),';
			}

			if ( isset( $doc['published_in'] ) ) {
				$html .= ' <span class="collab-mendeley-published-in">' . $doc['published_in'] . '</span>,';
			}


			if ( isset( $doc['identifiers'] ) ) {
				foreach ( $doc['identifiers'] as $identifier => $value ) {
					if ( $identifier == 'doi' ) {
						$html .= ' <span class="collab-mendeley-identifiers"> DOI:</span> <a href="http://dx.doi.org/' . $value . '" target="_blank" rel="nofollow">' . $value . '</a>';
					} else {
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

	/*private static function same_author($first, $second){
		if (($first['given'] == $second[0]) && ($first['family'] == $second[1])) {
			return true;
		}else{
			return false;
		}
	}*/

	private static function make_acronym( $text ) {
		$words   = explode( " ", $text );
		$acronym = "";

		foreach ( $words as $w ) {
			$acronym .= $w[0];
		}

		return $acronym;
	}

	/*private function get_file_url( $profile_id, $document_id, $file_hash ) {
		$url = 'http://www.mendeley.com/download/personal/';
		$url .= $profile_id . '/';
		$url .= $document_id . '/';
		$url .= $file_hash . '/dl.pdf';

		return $url;
	}*/


} 