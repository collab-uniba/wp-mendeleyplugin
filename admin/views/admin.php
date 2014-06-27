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

	<section id="api-keys-manager">
      <div class="section-text">
          <h3>Mendeley API Keys Management</h3>
      </div>
      <form class="section-form" action="">
          <label for="client-id">Client ID:</label><br/>
          <input type="text" id="client-id" name="client-id" size="60"/><br/>
          <label for="client-secret">Client Secret:</label><br/>
          <input type="text" id="client-secret" name="client-secret" size="60"/><br/>
          <label for="access-token">Access Token:</label><br/>
          <input type="text" size="60" readonly/><br/>
          <p id="expire-at"></p><br/>
          <input type="submit" value="Request/Authorize Access Token" name="oauth2-request-access-token"/>
      </form>
  </section>

</div>
