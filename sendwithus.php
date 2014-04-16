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
}

function sendwithus_validate_settings($args) {
	// Used to validate settings passed to the plugin.
	echo("Sanitized!<br/>");
	return $args;
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
				<!-- For now this is static, but we should find a way to poll wordpress and gather all the emails  -->
				<tr>
					<td>Event Example #1</td>
					<td>
						<!-- This should pull from swu to list the available templates -->
						<select style="width: 100%">
							<option>Template Example 1</option>
							<option>Template Example 2</option>
							<option>Template Example 3</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Event Example #2</td>
					<td>
						<!-- This should pull from swu to list the available templates -->
						<select style="width: 100%">
							<option>Template Example 1</option>
							<option>Template Example 2</option>
							<option>Template Example 3</option>
						</select>
					</td>
				</tr>
				<tfoot>
					<tr>
						<td>sendwithus API Key</td>
						<td>
							<input type="text" name="api_key" placeholder="Your sendwithus API key." style="width: 100%" 
								value="<?php echo get_option('api_key'); ?>"/>
						</td>
					</tr>
				</tfoot>
			</table>
			<div style="width: 100%; margin-left: auto; margin-right: auto; display: block; padding: 0px 0px 10px;">
				<!--
				<input type="submit" name="key" id="api_key_settings" class="button button-primary" value="Save Changes" style="margin: 10px 0px; width: 100%"/>
				-->
				<?php submit_button() ?>
			</div>
		</form>
	</div>
	<?
}

?>
