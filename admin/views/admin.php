<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   CollabMendeleyPlugin
 * @author    Davide Parisi <davideparisi@gmail.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 --
 */

date_default_timezone_set( get_option( 'timezone_string' ) != '' ? get_option( 'timezone_string' ) : 'Europe/Rome' );

?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!--<p>
		All calls to the Mendeley API require authentication using the <a href="http://oauth.net/2/">OAuth 2.0
			protocol</a>.
	</p>
	<p>
		In order to begin using the API a client must first register their application with the <a href="http://dev.mendeley.com">authorization server</a>.
	</p>
	<p>
		You have to:
	<ol>
		<li>Copy the redirect url below</li>
		<li>Register this plugin on <a href="http://dev.mendeley.com">Mendeley</a></li>
		<li>Get the generated secret code and the client id</li>
		<li>Insert client id and secret in the form below and store them to db</li>
		<li>Click on request token</li>
	</ol>
	</p>-->

	<label for="callback-url"><b>Redirect url</b> (<em>insert this when asked for a redirect URL</em>)</label><br/>
	<input type="text" value="<?php echo $this->callback_url; ?>" readonly size="85"/>

	<form action="options.php" method="post">
		<?php settings_fields( $this->plugin_slug ); ?>
		<?php do_settings_sections( $this->plugin_slug ); ?>
		<?php submit_button('Save keys to DB'); ?>
	</form>

	<h2>Access Token</h2>
	<?php
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		$options = get_site_option( $this->plugin_slug );
	} else {
		$options = get_option( $this->plugin_slug );
	}
	if ( isset( $options['access_token'] ) ) {
		$access_token_data = $options['access_token']['result'];
		$expires_at = $options['expire_time'];

		$html = '<label for="access_token">Access Token:</label>';
		$html .= '<br/>';
		$html .= '<input id="access_token" type="text" readonly value="' . $access_token_data['access_token'] . '" size="85" />';
		$html .= '<br/>';
		$html .= '<p class="' . ( ( time() < $expires_at ) ? "token-updated" : "token-expired" ) . '"><b>Expire time: </b>' . date( 'd-n-Y H:i:s', $expires_at ) . '</p>';

	} else {
		$html = '<p><em>No access token requested for this account</em></p>';
		$html .= '<p><em>With API key saved to the db you can now request your <b>access token</b> to mendeley</em></p>';
		$html .= '<form action="' . admin_url( "admin.php" ) . '" method="post">';
		$html .= '<input type="hidden" name="action" value="request_token"/>';
		$html .= '<input type="submit" value="Request Token" class="button-primary"/>';
		$html .= '</form>';
	}
	echo $html;
	?>

	<br/>
	<br/>
	<div class="debug">
		<input type="text" value="592" readonly/> <br/>
		<input type="text" value="naSqP2QvBSUJNx1J" readonly/>
	</div>
</div>
