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

    register_setting('sendwithus_settings', 'new_comment');
    register_setting('sendwithus_settings', 'new_user');
    register_setting('sendwithus_settings', 'password_change');
    register_setting('sendwithus_settings', 'awaiting_approval');
    
    // Whether user is using multisite functionality or not.
    register_setting('sendwithus_settings', 'multisite_enabled');

    // Multisite specific settings.
    register_setting('sendwithus_settings', 'ms_new_user');
    register_setting('sendwithus_settings', 'ms_new_blog');
    register_setting('sendwithus_settings', 'ms_new_user_network_admin');
    register_setting('sendwithus_settings', 'ms_new_blog_network_admin');
    register_setting('sendwithus_settings', 'ms_new_user_success');
    register_setting('sendwithus_settings', 'ms_new_blog_success');
    register_setting('sendwithus_settings', 'ms_welcome_user_notification');
    register_setting('sendwithus_settings', 'ms_welcome_notification');
}

function sendwithus_validate_settings($args) {
	// Used to validate settings passed to the plugin.
	echo("Sanitized!<br/>");
	return $args;
}

// Wrapper for the emails() function in the API
function getTemplates(){
    $api_key = get_option('api_key');
    $api = new \sendwithus\API($api_key);
    $response = $api->emails();

    return $response;
}

// Get the API key for use as a global variable.
function getAPIKey() {
	return get_option('api_key');
}

// Generate a template selection drop down list;
// value = template id
// text = template name
function generateTemplateSelection($name, $array)
{
    if (get_option('api_key')) {
        $input_code = '<select name="' . $name . '">';
        $current_template = get_option($name);

        foreach ($array as $template) {
            if($template->id == $current_template){
                $input_code .= '<option value=' . $template->id . ' selected>' . $template->name . '</option>';
            }
            else {
                $input_code .= '<option value=' . $template->id . '>' . $template->name . '</option>';
            }
        }

        $input_code .= '</select>';
        return $input_code;

    } else {
        echo "<p>Please set your API Key</p>";
    }
}

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
				<!-- For now this is static, but we should find a way to poll wordpress and gather all the emails  -->
                <tr>
                    <td>New User Created</td>
                    <td>
                        <!-- Pull from swu to list the available templates -->
                        <?php
                            echo  generateTemplateSelection('new_user', $GLOBALS['templates']);
                        ?>
                    </td>
                </tr>
                <tr>
					<td>New Comment Posted by User</td>
					<td>
						<!-- Pull from swu to list the available templates -->
                        <?php
                            echo  generateTemplateSelection('new_comment', $GLOBALS['templates']);
                        ?>
					</td>
				</tr>
                <tr>
                    <td>User Comment Awaiting Approval</td>
                    <td>
                        <!-- Pull from swu to list the available templates -->
                        <?php
                            echo  generateTemplateSelection('awaiting_approval', $GLOBALS['templates']);
                        ?>
                    </td>
                </tr>          
                <tr>
                    <td>Password Change Requested</td>
                    <td>
                        <!-- Pull from swu to list the available templates -->
                        <?php
                            echo  generateTemplateSelection('password_change', $GLOBALS['templates']);
                        ?>
                    </td>
                </tr>
                <tr class="multiside_option">
                    <td>Enable Multisite Events</td>
                    <td> 
                        <input type="checkbox" name="multisite_enabled" value="multisite_enabled" 
                            <?php
                                checked('multisite_enabled', get_option('multisite_enabled'))
                            ?>
                        />
                    </td>
                </tr>
                <!-- Events that are displayed when multisite events are enabled -->
                <tr>
                <td colspan="2">
                <table class="multisite wp-list-table widefat">
                    <thead>
                        <th colspan="2" style="text-align: center;">Multisite Events</th>
                    </thead>
                    <tr>
                        <td>New User Notification</td>
                        <td> 
                            <?php
                                echo generateTemplateSelection('ms_new_user', $GLOBALS['templates']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>New Blog Notification</td>
                        <td> 
                            <?php
                                echo generateTemplateSelection('ms_new_blog', $GLOBALS['templates']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>New User Notification - Notify Network Admin</td>
                        <td> 
                            <?php
                                echo generateTemplateSelection('ms_new_user_network_admin', $GLOBALS['templates']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>New Blog Notification - Notify Network Admin</td>
                        <td> 
                            <?php
                                echo generateTemplateSelection('ms_new_blog_network_admin', $GLOBALS['templates']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>New User Success - Notify User</td>
                        <td> 
                            <?php
                                echo generateTemplateSelection('ms_new_user_success', $GLOBALS['templates']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>New Blog Success - Notify User</td>
                        <td> 
                            <?php
                                echo generateTemplateSelection('ms_new_blog_success', $GLOBALS['templates']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>New User Welcome - Notify User</td>
                        <td> 
                            <?php
                                echo generateTemplateSelection('ms_welcome_user_notification', $GLOBALS['templates']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>New Blog Welcome - Notify User</td>
                        <td> 
                            <?php
                                echo generateTemplateSelection('ms_welcome_notification', $GLOBALS['templates']);
                            ?>
                        </td>
                    </tr>
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
				<input type="submit" name="key" id="api_key_settings" class="button button-primary" value="Save Changes" style="margin: 10px 0px; width: 100%"/>
				-->
				<?php submit_button() ?>
			</div>
		</form>
	</div>
    <pre>
	<?
}

// Replace new comment alert with sendwithus
if( ! function_exists('wp_notify_postauthor') ) {
    function wp_notify_postauthor( $comment_id ){
        $api_key = get_option('api_key');
        $api = new \sendwithus\API($api_key);

        $comment = get_comment($comment_id);

        $response = $api->send(
            get_option('new_comment'),
            array('address' => get_option('admin_email')),
            array(
                'email_data' => array(
                    'comment_ID' => $comment->comment_ID,
                    'comment_post_ID' => $comment->comment_post_ID,
                    'comment_author' => $comment->comment_author,
                    'comment_author_email' => $comment->comment_author_,
                    'comment_author_url' => $comment->comment_author_url,
                    'comment_author_IP' => $comment->comment_author_IP,
                    'comment_date' => $comment->comment_date,
                    'comment_date_gmt' => $comment->comment_date_gmt,
                    'comment_content' => $comment->comment_content,
                    'comment_karma' => $comment->comment_karma,
                    'comment_approved' => $comment->comment_approved,
                    'comment_agent' => $comment->comment_agent,
                    'comment_type' => $comment->comment_type,
                    'comment_parent' => $comment->comment_parent,
                    'user_id' => $comment->user_id,
                )
            )
        );
    }
}


    if (!function_exists('wp_new_user_notification')) {
        function wp_new_user_notification()
        {

        }
    }

    if (!function_exists('wp_notify_moderator')) {
        function wp_notify_moderator()
        {

        }
    }

    if (!function_exists('wp_password_change_notification')) {
        function wp_password_change_notification()
        {

        }
    }



    ?>
