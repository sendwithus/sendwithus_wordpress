<?php
/**
 * @package sendwithus
 * @version 0.1
 */
/*
Plugin Name: sendwithus Transactional Email
Plugin URI: http://www.sendwithus.com
Description: Easily integrate transactional email into WordPress' default emails.
Author: Dylan Moore, Kyle Poole, and Cory Purnell
Version: 0.1
Author URI: http://www.sendwithus.com
*/

require('sendwithus_php/lib/API.php');
require('inc/globals.php');
require('inc/helper_functions.php');
require('inc/single_site_overrides.php');
require('inc/multisite_overrides.php');

add_action('admin_menu', 'activate_sidebar_shortcut');
// Creates link to plugin settings in WordPress control panel.
function activate_sidebar_shortcut() {
    // Add the shortcut for the plugin settings underneath the 'plugins' sidebar menu.
    add_submenu_page('plugins.php', 'sendwithus', 'sendwithus', 'manage_options', 'sendwithus_admin_menu', 'sendwithus_conf_main');

    // Create an area in WordPress to store the settings saved by the user.
    add_action('admin_init', 'sendwithus_register_settings');
}

// Used to create an area to save plugin settings.
function sendwithus_register_settings() {
	// Save settings within wp_options table as 'sendwithus_settings'
	register_setting('sendwithus_settings', 'api_key');

    // Whether user is using multisite functionality or not.
    register_setting('sendwithus_settings', 'multisite_enabled');


    foreach($GLOBALS['wp_notifications'] as $key => $value) {
        register_setting('sendwithus_settings', $key);
    }

    foreach($GLOBALS['wp_ms_notifications'] as $key => $value) {
        register_setting('sendwithus_settings', $key);
    }
}

// Add stylesheet\
function add_style_sheet(){
    wp_register_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}
add_action('wp_enqueue_scripts','add_style_sheet');

$GLOBALS['templates'] = getTemplates();
$GLOBALS['api_key'] = getAPIKey();

// Used for displaying the main menu page.
// Activated when user clicks on link in sidebar.
function sendwithus_conf_main() {
	// Make sure that the user has appropriate permissions to be here.
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	?>
	<!-- Font for sendwithus' logo -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

	<h1 style="font-family: 'Open Sans', sans-serif;">
		<span style="color: #777">send<span style="color: #f7931d">with</span>us</span>
	</h1>
	<p>Send transactional emails with ease.</p>
	<div class="welcome-panel">
		<h3>Events</h3>
		<p>Events that trigger the sending of transactional emails are listed below.</p>

		<!-- A check should be performed before loading the table to ensure that the user
			 has entered an API key - otherwise only an entry for API key should be displayed. -->
		<form action="options.php" method="post">
			<?php
				// Load up the previously saved settings.
				settings_fields('sendwithus_settings');
				do_settings_sections('sendwithus_settings');
			?>
			<table class="wp-list-table widefat sendwithus_table">
				<thead>
					<th>WordPress Event</th>
					<th>sendwithus Template</th>
				</thead>
                <?php
                    generateTemplateTable($GLOBALS['wp_notifications']);
                ?>
                <tr class="multiside_option">
                    <td>Enable Multisite Events</td>
                    <td> 
                        <input type="checkbox" id="multisite_enabled" name="multisite_enabled" value="multisite_enabled" 
                            <?php
                                checked('multisite_enabled', get_option('multisite_enabled'))
                            ?>
                        />

                    </td>
                </tr>
                <!-- Events that are displayed when multisite events are enabled -->
                <tr>
                <td colspan="2">
                <table class="multisite wp-list-table widefat" id="multisite_table">
                    <thead>
                        <th colspan="2" style="text-align: center;"><b>Multisite Events</b></th>
                    </thead>
                    <?php
                    generateTemplateTable($GLOBALS['wp_ms_notifications']);
                    ?>
                </table>
                </td>
                </tr>
				<tfoot>
					<tr>
						<td>sendwithus API Key</td>
						<td>
							<input type="text" name="api_key" placeholder="Your sendwithus API key." style="width: 100%"
								value="<?php echo getAPIKey(); ?>"/>
						</td>
					</tr>
				</tfoot>
			</table>
			<div style="width: 100%; margin-left: auto; margin-right: auto; display: block; padding: 0px 0px 10px;">
				<!--
				<input type="submit" name="key" id="api_key_settings" class="button button-primary save-button" value="Save Changes" style="margin: 10px 0px; width: 100%"/>
				-->
				<?php submit_button() ?>
			</div>
		</form>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript">
            // Check to see if the multisite options should be listed or not.
            var is_multisite_enabled = '<?php echo get_option("multisite_enabled") ?>';

            if (is_multisite_enabled === 'multisite_enabled') {
                is_multisite_enabled = true;
            } else {
                is_multisite_enabled = false;
            }

            function toggle_multisite() {
                if(is_multisite_enabled === true) {
                    is_multisite_enabled = !is_multisite_enabled;
                    $('#multisite_table').css('display', 'table');
                } else {
                    is_multisite_enabled = !is_multisite_enabled;
                    $('#multisite_table').css('display', 'none');

                }            
            }

            toggle_multisite();

            $("#multisite_enabled").change(function() {
                toggle_multisite();
            })
        </script>
	</div>
	<?
}

?>
