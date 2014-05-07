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

// Add stylesheet
add_action('admin_enqueue_scripts','register_style_sheet');
function register_style_sheet(){
    wp_register_style( 'sendwithus_style', plugins_url('./css/sendwithus_style.css', __FILE__));
    wp_enqueue_style('sendwithus_style');
}

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
    register_setting('sendwithus_settings', 'display_parameters');

    // Whether user is using multisite functionality or not.
    register_setting('sendwithus_settings', 'multisite_enabled');


    foreach($GLOBALS['wp_notifications'] as $key => $value) {
        register_setting('sendwithus_settings', $key);
    }

    foreach($GLOBALS['wp_ms_notifications'] as $key => $value) {
        register_setting('sendwithus_settings', $key);
    }
}

$GLOBALS['templates'] = getTemplates();
$GLOBALS['api_key'] = getAPIKey();

// Establish whether an API key has been entered and that it is valid.
$GLOBALS['valid_key'] = true;
if($GLOBALS['api_key'] == '' || $GLOBALS['templates']->status == 'error') {
    $GLOBALS['valid_key'] = false;
}

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
        <!-- Only display if API key is populated -->
        <?php if($GLOBALS['valid_key']) : ?>
			<h3>Events</h3>
			<p>Events that trigger the sending of transactional emails are listed below.</p>
		<?php endif; ?>
		<!-- A check should be performed before loading the table to ensure that the user
			 has entered an API key - otherwise only an entry for API key should be displayed. -->
		<form action="options.php" method="post">
			<?php
				// Load up the previously saved settings.
				settings_fields('sendwithus_settings');
				do_settings_sections('sendwithus_settings');
			?>
            <!-- Only display if API key is populated -->
	        <?php if($GLOBALS['valid_key']) : ?>
				<table class="wp-list-table widefat sendwithus_table">
					<thead>
						<th style="width: 49%">WordPress Event</th>
						<th style="width: 49%">sendwithus Template</th>
					</thead>
	                    <?php generateTemplateTable($GLOBALS['wp_notifications']); ?>
	                <!-- Events that are displayed when multisite events are enabled -->
	                <tr>
	                <td colspan="2">
	                <table class="multisite wp-list-table widefat" id="multisite_table">
	                    <thead>
	                        <th colspan="2" style="text-align: center;"><b>Multisite Events</b></th>
	                    </thead>
	                    <?php
	                        // Check that an API Key has been etered before displaying these.
	                        if($GLOBALS['valid_key']) {                
	                            generateTemplateTable($GLOBALS['wp_ms_notifications']);
	                        }
	                    ?>
	                </table>
	                </td>
	                </tr>
					<tfoot>
						<tr>
							<td><strong>sendwithus API Key</strong></td>
							<td>
								<input type="text" name="api_key" placeholder="Your sendwithus API key." style="width: 100%"
									value="<?php echo getAPIKey(); ?>"/>
							</td>
						</tr>
					</tfoot>
				</table>
            <!-- Display a notice telling the user to enter their API key & save -->
            <?php else : ?>
            	<table>
	                <tr>
	                    <td colspan="2" style="text-align: center;"><h2>In order to proceed, please enter a valid API key and save your changes.</h2></td>
	                </tr>
					<tr>
						<td><strong>sendwithus API Key</strong></td>
						<td>
							<input type="text" name="api_key" placeholder="Your sendwithus API key." style="width: 100%"
								value="<?php echo getAPIKey(); ?>"/>
						</td>
					</tr>
				</table>
            <?php endif; ?>
			<div style="width: 100%; margin-left: auto; margin-right: auto; display: block; padding: 0px 0px 10px;">
				<?php submit_button() ?>
			</div>
            <div>
                <input type="checkbox" id="multisite_enabled" name="multisite_enabled" value="multisite_enabled"
                    <?php checked('multisite_enabled', get_option('multisite_enabled')) ?>
                    />
                <strong>Enable multisite events.</strong>
            </div>
            <div>
                <input style="visibility: hidden;" type="checkbox" id="display_parameters" name="display_parameters" value="display_parameters"
                    <?php checked('display_parameters', get_option('display_parameters')) ?>
                    />
                <strong style="visibility: hidden;">Display descriptions of parameters sent to sendwithus</strong>

            </div>
		</form>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript">
            // Check to see if the multisite options should be listed or not.
            var is_multisite_enabled = '<?php echo get_option("multisite_enabled") ?>';
            var are_parameters_displayed = '<?php echo get_option("display_parameters") ?>'

            if (is_multisite_enabled === 'multisite_enabled') {
                is_multisite_enabled = true;
            } else {
                is_multisite_enabled = false;
            }

            if (are_parameters_displayed === 'display_parameters') {
                are_parameters_displayed = true;
            } else {
                are_parameters_displayed = false;
            }

            function toggle_multisite() {
                if (is_multisite_enabled === true) {
                    $('#multisite_table').css('display', 'table');
                } else {
                    $('#multisite_table').css('display', 'none');
                }

                is_multisite_enabled = !is_multisite_enabled;
            }

            function toggle_parameters() {
                if (are_parameters_displayed === true) {
                    $('.parameters').css('display', 'initial');
                } else {
                    $('.parameters').css('display', 'none');
                }         

                are_parameters_displayed = !are_parameters_displayed;
            }

            toggle_multisite();
            toggle_parameters();

            $('#multisite_enabled').change(function() {
                toggle_multisite();
            });

            $('#display_parameters').change(function() { 
                toggle_parameters();
            });

            $('.display_info').click(function () {
                $(this).text(function(i, text){
                    return text === "Display Description" ? "Hide Description" : "Display Description"
                });

                $(this).parent().siblings().find('.parameters').slideToggle(150);
            });
        </script>
	</div>
	<?
}

?>
