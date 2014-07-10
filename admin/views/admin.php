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


?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form action="options.php" method="post">
		<?php settings_fields( $this->plugin_slug ); ?>
		<?php do_settings_sections( $this->plugin_slug ); ?>
		<?php submit_button(); ?>
	</form>

<div id="debug">
	<input type="text" value="592" readonly/> <br/>
	<input type="text" value="naSqP2QvBSUJNx1J" readonly/>
</div>

