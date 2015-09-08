<?php
/**
 * Created by PhpStorm.
 * User: Gabriele Cianciaruso <infogabry@gmail.com>
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



/*  NOT USED

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
*/

	public static function custom_format( $documents, $main_author ) {
		$formatted                = '<ol class="collab-mendeley-authored-list">';
		$main_author_first_name   = $main_author[0];
		$main_author_last_name    = $main_author[1];
		
		$count = 0;
		$trovato = false;
		foreach ( $documents as $doc ) {
			//print_r($doc->author);
			//$profile_id             = $doc->profile_id;
			$trovato = false;
			$title = $doc->title;
			$html  = '<li>';
			
			if(isset($doc->year)) $html.='(<span class="collab-mendeley-year">' . $doc->year . '</span>) ';

			
			foreach ( $doc->author as $author ) {
				$acronym = self::make_acronym( $author );

				echo $acronym;

				if ( $author['given'] == $main_author_first_name && $author['family'] == $main_author_last_name ) {
					$html .= '<span class="collab-mendeley-author">' . $author['given'] . ' ' . $author['family'] . '</span>, ';
					$trovato = true;
				} else {
					$html .= $author['given'] . ' ' . $author['family'] . ', ';
				}
			}
			if(!$trovato) continue;
			
			$html .= '<form method="post" name="down'.$count.'" target="_blank">';
			$html .= '<input type="hidden" name="action" value="mendeley_download"/>';
			$html .= '<input type="hidden" name="idfile" value="'.$doc->file_info[0]['id'].'"/>';
			$html .= '<a class="collab-mendeley-title" href="javascript:document.down'.$count.'.submit();" rel="nofollow">' . $doc->title . '</a>, ';
			$html .= '</form>';
			$count++;

			if ( isset( $doc->publisher ) ) {
				$html .= '<span class="collab-mendeley-publisher">(' . $doc->publisher . ')</span>,';
			}

			if ( isset( $doc->published_in ) ) {
				$html .= ' <span class="collab-mendeley-published-in">' . $doc->published_in . '</span>,';
			}


			if ( isset( $doc->identifiers ) ) {
				foreach ( $doc->identifiers as $identifier => $value ) {
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

	private static function make_acronym( $text ) {
		$words   = explode( " ", $text );
		$acronym = "";

		foreach ( $words as $w ) {
			$acronym .= $w[0];
		}

		return $acronym;
	}

	
} 