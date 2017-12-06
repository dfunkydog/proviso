<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<form action='options.php' method='post'>

    <h2>Provisioning Options</h2>

    <?php
    settings_fields( 'provisioning' );
    do_settings_sections( 'provisioning' );
    submit_button();
    ?>

</form>