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

    <?php if ( ! ( isset( $this->options['client_id'] ) && isset( $this->options['client_secret'] ) )  ) { ?>
	<section id="api-keys-manager">
      <div class="section-text">
          <h3>Mendeley API Keys Management</h3>
      </div>
      <form class="section-form form" action="<?php echo admin_url( 'admin.php' ); ?>" method="post">
          <label for="client-id">Client ID:</label><br/>
          <input type="text" id="client-id" name="client-id" size="60"/><br/>
          <label for="client-secret">Client Secret:</label><br/>
          <input type="text" id="client-secret" name="client-secret" size="60"/><br/>
          <label for="access-token">Access Token:</label><br/>
          <input type="text" size="60" readonly/><br/>
          <p id="expire-at"></p><br/>
          <input type="hidden" name="action" value="set_keys"/>
          <input type="submit" value="Request/Refresh Access Token" name="keys_settings" class="button-primary"/>
      </form>
    </section>
    <?php } else { ?>
    <section>
        <div class="section-text">
            <h3>Mendeley API Keys Management</h3>
        </div>
        <form class="section-form form" action="<?php echo admin_url( 'admin.php' ); ?>" method="post">
            <label for="client-id">Client ID:</label><br/>
            <input type="text" id="client-id" name="client-id" size="60" value="<?php echo $this->options['client_id']; ?>" readonly/><br/>
            <label for="client-secret">Client Secret:</label><br/>
            <input type="text" id="client-secret" name="client-secret" size="60" value="<?php echo $this->options['client_secret']; ?>" readonly/><br/>
            <label for="access-token">Access Token:</label><br/>
	        <?php if ( isset( $this->options['access_token'] ) ) { ?>
                <input type="text" size="60" value="<?php echo $this->options['access_token']; ?>" readonly/><br/>
	        <?php } ?>
            <p id="expire-at" class="<?php  echo ( time() < $this->options['expire_time'] ) ? 'updated' : 'expired'; ?>"><b>Access Token expire time: </b><?php echo date( 'd-n-Y H:i:s', $this->options['expire_time'] ); ?></p><br/>
            <input type="hidden" name="action" value="set_keys"/>
            <input type="submit" value="Request/Refresh Access Token" name="keys_settings" class="button-primary"/>
        </form>
    </section>
    <?php } ?>
</div>
<br/><br/>
<div id="debug">
	<input type="text" value="592" readonly/> <br/>
	<input type="text" value="naSqP2QvBSUJNx1J" readonly/>
</div>

