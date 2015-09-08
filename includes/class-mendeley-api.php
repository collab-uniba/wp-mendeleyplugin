<?php
/**
 * Created by PhpStorm.
 * User: Gabriele Cianciaruso <infogabry@gmail.com>
 * Date: 08/07/14
 * Time: 14:20
 */

if ( ! class_exists( "Client" ) ) {
	include_once 'vendor/autoload.php';
}
//define( 'AUTHORIZE_ENDPOINT', 'https://api.mendeley.com/oauth/authorize' );
//define( 'TOKEN_ENDPOINT', 'https://api.mendeley.com/oauth/token' );
//define( 'API_ENDPOINT', 'https://api.mendeley.com/' );

class MendeleyApi {

	const AUTHORIZE_ENDPOINT = 'https://api.mendeley.com/oauth/authorize';
	const TOKEN_ENDPOINT = 'https://api.mendeley.com/oauth/token';
	const API_ENDPOINT = 'https://api.mendeley.com/';


	protected $client = null;

	protected $client_id;

	protected $client_secret;

	protected $callback_url;

	protected static $instance = null;


	function __construct() {
		$this->client_id     = '';
		$this->client_secret = '';
		$this->callback_url  = '';
	}


	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function init() {
		$this->client = new \OAuth2\Client( $this->client_id, $this->client_secret );
	}

	/**
	 * @return string
	 */
	public function get_callback_url() {
		return $this->callback_url;
	}

	/**
	 * @param string $callback_url
	 */
	public function set_callback_url( $callback_url ) {
		$this->callback_url = $callback_url;
	}

	/**
	 * @return string
	 */
	public function get_client_id() {
		return $this->client_id;
	}

	/**
	 * @param string $client_id
	 */
	public function set_client_id( $client_id ) {
		$this->client_id = $client_id;
	}

	/**
	 * @return string
	 */
	public function get_client_secret() {
		return $this->client_secret;
	}

	/**
	 * @param string $client_secret
	 */
	public function set_client_secret( $client_secret ) {
		$this->client_secret = $client_secret;
	}


	public function start_authorization_flow() {
		$url = $this->client->getAuthenticationUrl( self::AUTHORIZE_ENDPOINT, $this->callback_url, array( 'scope' => 'all' ) );
		// redirige sul sito mix.mendeley.com/oauth
		wp_redirect( $url );
		exit();
	}

	public function get_access_token( $auth_code ) {
		// set request parameters
		
		$params   = array( 'code' => $auth_code, 'redirect_uri' => $this->callback_url );
		$response = $this->client->getAccessToken( self::TOKEN_ENDPOINT, 'authorization_code', $params );
		
		return $response;
	}

	public function  set_client_access_token( $access_token ) {
		$this->client->setAccessToken( $access_token );
	}

	public function refresh_access_token( $refresh_token ) {
		$params   = array( 'refresh_token' => $refresh_token );
		$response = $this->client->getAccessToken( self::TOKEN_ENDPOINT, 'refresh_token', $params );

		return $response;
	}

	public function get_authored_publications($id_profile) {

		$url = self::API_ENDPOINT . 'documents';

		// $response = $this->client->fetch( $url );
		$response = $this->client->fetch( $url, array('id_profile'=> $id_profile), 'GET' );
		if ( $response['code'] != 200 ) {
			return null;
		}

		$documents = $response['result'];
		$data      = $this->process_authored_publications( $documents );

		return $data;
	}

	public function get_document( $id ) {
		$url      = self::API_ENDPOINT . 'documents/' . $id;
		$document = $this->client->fetch( $url );

		return $document;
	}

	public function get_account_info() {
		$url  = self::API_ENDPOINT . 'profiles/me';
		$info = $this->client->fetch( $url );
		
		if ( $info['code'] == 200 ) {
			return $info['result'];
		}

		return null;
	}

	/**
	 * Set up needed components and startup the object
	 *
	 * @param $client_id
	 * @param $client_secret
	 * @param $callback_url
	 */
	public function set_up( $client_id, $client_secret, $callback_url ) {
		$this->client_id     = $client_id;
		$this->client_secret = $client_secret;
		$this->callback_url  = $callback_url;
		$this->init();
	}

	public function fetch( $url, $parameters = array() ) {
		$this->client->fetch( $url, $parameters );
	}


	public function get_file_info( $doc_id ) {

		$url      = self::API_ENDPOINT . 'files';
		$file = $this->client->fetch( $url, array('document_id'=> $doc_id), 'GET');
		

		if ( $file['code'] = 200 ) {
			return $file['result'];
		}
		


		return null;
	}

	/*-------------------------------------------------------------------------------
	 *
	 * Private Functions/Utilities
	 *
	 *------------------------------------------------------------------------------*/

	private function process_authored_publications( $documents ) {
		//$documents_id_array = $data['document_ids'];
		$pubblications      = array();
		$count = 0;
		foreach ( $documents as $doc ) {
			
			$tmp_doc                                   		 = $this->pre_process( $doc );
			$pubblications['data'][ $doc['type'] ][ $count ] = $tmp_doc;
			$count++;
		}

		return $pubblications;
	}

	

	/*
	 * Preprocessing
	 */
	private function mendeleyNames2CiteProcNames( $names ) {
		if ( ! $names ) {
			return $names;
		}
		$tmp_names = array();
		foreach ( $names as $rank => $name ) {
			$tmp_names[ $rank ]['given']  = $name['first_name'];  	// forename
			$tmp_names[ $rank ]['family'] = $name['last_name'];		// surname
		}

		return $tmp_names;
	}

	private function mendeleyType2CiteProcType( $type ) {
		if ( ! isset( $this->type_map ) ) {
			/*
			['journal' or 'book' or 'generic' or 'book_section' or 'conference_proceedings' or 'working_paper' or 'report' or 'web_page' or 
			 'thesis' or 'magazine_article' or 'statute' or 'patent' or 'newspaper_article' or 'computer_program' or 'hearing' or 
			 'television_broadcast' or 'encyclopedia_article' or 'case' or 'film' or 'bill']
			*/
			$this->type_map = array(
				'book'                   => 'book',
				'film'                   => 'book',
				'bill'                   => 'bill',
				'book_section'           => 'chapter',
				'magazine_article'       => 'article-magazine',
				'newspaper_article'      => 'article-newspaper',
				'conference_proceedings' => 'paper-conference',
				'report'                 => 'report',
				'thesis'                 => 'thesis',
				'case'                   => 'legal_case',
				'encyclopedia_article'   => 'entry-encyclopedia',
				'web_page'               => 'webpage',
				'working_paper'          => 'report',
				'generic'                => 'chapter',
				'journal'                => 'Giornali'
			);
		}

		return $this->type_map[ $type ];
	}

	private function pre_process( $doc ) {
		// stdClass for showing document
		$docdata         = new stdClass;
		
		$docdata->document_id  = $doc['id'];


		$docdata->file_info	   = $this->get_file_info($doc['id']);  // necessario per visualizzare\download ecc ecc
		

		$docdata->profile_id   = $doc['profile_id'];
		$docdata->group_id     = $doc['group_id'];
		
		$docdata->type   = $this->mendeleyType2CiteProcType( $doc['type'] );
		$docdata->author = $this->mendeleyNames2CiteProcNames( $doc['authors'] );
		$docdata->editor = $this->mendeleyNames2CiteProcNames( $doc['editors'] );
		// DEPRECATED $docdata->issued = (object) array( 'date-parts' => array( array( $doc['year'] ) ) );
		$docdata->title  = $doc['title'];
		$docdata->year   = $doc['year'];
		if ( isset( $doc['published_in'] ) ) {
			$docdata->container_title = $doc['published_in'];
		}
		if ( isset( $doc['publication_outlet'] ) ) {
			$docdata->container_title = $doc['publication_outlet'];
		}
		if ( isset( $doc['journal'] ) ) {
			$docdata->container_title = $doc['journal'];
		}
		if ( isset( $doc['volume'] ) ) {
			$docdata->volume = $doc['volume'];
		}
		if ( isset( $doc['issue'] ) ) {
			$docdata->issue = $doc['issue'];
		}
		if ( isset( $doc['pages'] ) ) {
			$docdata->page = $doc['pages'];
		}
		if ( isset( $doc['publisher'] ) ) {
			$docdata->publisher = $doc['publisher'];
		}
		if ( isset( $doc['city'] ) ) {
			$docdata->publisher_place = $doc['city'];
		}
		if ( isset( $doc['url'] ) ) {
			$docdata->URL = $doc['url'];
		}
		if ( isset( $doc['doi'] ) ) {
			$docdata->DOI = $doc['doi'];
		}
		if ( isset( $doc['isbn'] ) ) {
			$docdata->ISBN = $doc['isbn'];
		}

		return $docdata;
	}

}