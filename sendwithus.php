<?php
/**
 * @package sendwithus
 * @version 0.8
 */
/*
Plugin Name: sendwithus Transactional Email
Plugin URI: http://www.sendwithus.com
Description: Easily integrate transactional email into WordPress' default emails.
Author: Dylan Moore, Kyle Poole, and Cory Purnell
Version: 0.8
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
    wp_register_style('sendwithus_style', plugins_url('/css/sendwithus_style.css', __FILE__));
    wp_enqueue_style('sendwithus_style');
}

add_action('admin_menu', 'activate_sidebar_shortcut');
// Creates link to plugin settings in WordPress control panel.
function activate_sidebar_shortcut() {
    // Add the shortcut for the plugin settings underneath the 'plugins' sidebar menu.
    add_menu_page( 'sendwithus', 'sendwithus', 'manage_options', 'sendwithus.php', 'sendwithus_conf_main', 'dashicons-email-alt');

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

	<h1>
        <a href="http://www.sendwithus.com" target="_">    
		  <span style="color: #777">send<span style="color: #f7931d">with</span>us</span>
        </a>
	</h1>
	<p>Enable transactional emails within WordPress with ease.</p>
	<div class="welcome-panel">
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
                <h3>Events</h3>
                <table style="width: 100%">
                    <tr>
                        <td>
                            <p class="description">Events that trigger the sending of transactional emails are listed below.</p>
                        </td>
                        <td>
                            <input id="api_box" type="text" name="api_key"
                                placeholder="Your sendwithus API key." 
                                value="<?php echo getAPIKey(); ?>"/>

                            <div id="api_button" class="button">Show API Key</div>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>

            <!-- Only display if API key is populated -->
	        <?php if($GLOBALS['valid_key']) : ?>
				<table class="wp-list-table widefat sendwithus_table">
					<thead>
						<th colspan="2">
                            <p class="table_description">Single-site Events</p>
                            <p class="description" style="text-align: center;">
                                Single-site events occur on all WordPress installations. They are primarly concerned with user and comment moderation.
                            </p>
                        </th>
					</thead>
	                    <?php generateTemplateTable($GLOBALS['wp_notifications']); ?>
                </table>    
                <!-- Events that are displayed when multisite events are enabled -->
                <?php if (is_multisite()) : ?>
                    <table class="multisite wp-list-table widefat" id="multisite_table">
                        <thead>
                            <th colspan="2">
                                <p class="table_description">Multi-site Events</p>
                                <p class="description" style="text-align: center;">Multi-site events are specific to WordPress instances that host multiple WordPress sites. As such, they feature several events specific to administering multiple sites.</p>
                            </th>
                        </thead>
                        <?php generateTemplateTable($GLOBALS['wp_ms_notifications']); ?>
                    </table>
                <?php endif; ?>
            <!-- Display a notice telling the user to enter their API key & save -->
            <?php else : ?>
            	<table>
	                <tr>
	                    <td colspan="2" style="text-align: center;">
                            <h2>In order to proceed please enter a valid API key and save your changes.</h2>
                            <p>Don't know what that is? Log in to your <a href="http://www.sendwithus.com">sendwithus control panel</a> and check under 'API Settings.'</p>
                        </td>
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
			<div class="display_button_area">
				<?php submit_button() ?>
			</div>
		</form>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript">
            $('.display_info').click(function(event) {
                $(this).text(function(i, text) {
                    return text === 'Show parameters sent to sendwithus' ? 'Hide parameters' : 'Show parameters sent to sendwithus';
                });

                // Get name of class from button.
                // Kinda sloppy in how it relies on the position.
                var className = event.target.classList[3];
                $('.parameters.' + className).slideToggle(150);
            });

            $('#api_button').click(function() {
                $(this).hide();
                $('#api_box').show(300, 'linear', { direction: 'left' });
            });
        </script>
	</div>
	<?
}

?>
