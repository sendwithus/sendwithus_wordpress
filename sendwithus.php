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

$GLOBALS['wp_notifications'] = array(
    'new_user'                       => 'New User Created',
    'new_comment'                    => 'New Comment Posted',
    'awaiting_approval'              => 'User Comment Awaiting Approval',
    'password_change_notification'   => 'Password Change Requested (Notify Admin)',
    'password_reset'                 => 'Password Reset Requested (Notify User)'
);

$GLOBALS['wp_ms_notifications'] = array(
    'ms_new_user_network_admin'    => 'New User Notification - Notify Network Admin',
    'ms_new_blog_network_admin'    => 'New Blog Notification - Notify Network Admin',
    'ms_new_user_success'          => 'New User Success - Notify User',
    'ms_new_blog_success'          => 'New Blog Success - Notify User',
    'ms_welcome_user_notification' => 'New User Welcome - Notify User',
    'ms_welcome_notification'      => 'New Blog Welcome - Notify User',
);

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

// Generate table body from the wp_notification arrays
function generateTemplateTable($notification_array)
{
    foreach ($notification_array as $name => $text) {
        echo '<tr><td>' . $text;
        echo '</td><td>';
        echo generateTemplateSelection($name,$GLOBALS['templates']);
        echo '</td></tr>';
    }
}

// Make 'default_message' HTML friendly.
function htmlDefaultMessage($default_message) {
    // Convert newline into line breaks.
    return preg_replace('/\\n/', '<br>', $default_message);
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
				<input type="submit" name="key" id="api_key_settings" class="button button-primary" value="Save Changes" style="margin: 10px 0px; width: 100%"/>
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

/* 
 * FUNCTION OVERRIDE BASED OVERRIDES	
 */

// Replace new comment alert with sendwithus
if (!function_exists('wp_notify_postauthor')) {
    function wp_notify_postauthor($comment_id)
    {
        $api = new \sendwithus\API($GLOBALS['api_key']);

        $comment = get_comment($comment_id);
        $post    = get_post( $comment->comment_post_ID );
        $author  = get_userdata( $post->post_author );

        /* Begin code to generate 'default_message' */
        switch ( $comment->comment_type ) {
            case 'trackback':
                $notify_message  = sprintf( __( 'New trackback on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: website name, 2: author IP, 3: author domain */
                $notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= __('Excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                $notify_message .= __('You can see all trackbacks on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
                break;
            case 'pingback':
                $notify_message  = sprintf( __( 'New pingback on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: comment author, 2: author IP, 3: author domain */
                $notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= __('Excerpt: ') . "\r\n" . sprintf('[...] %s [...]', $comment->comment_content ) . "\r\n\r\n";
                $notify_message .= __('You can see all pingbacks on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
                break;
            default: // Comments
                $notify_message  = sprintf( __( 'New comment on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: comment author, 2: author IP, 3: author domain */
                $notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= sprintf( __('Whois  : http://whois.arin.net/rest/ip/%s'), $comment->comment_author_IP ) . "\r\n";
                $notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                $notify_message .= __('You can see all comments on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
                break;
        }
        $notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
        $notify_message .= sprintf( __('Permalink: %s'), get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment_id ) . "\r\n";

        if ( user_can( $post->post_author, 'edit_comment', $comment_id ) ) {
            if ( EMPTY_TRASH_DAYS )
                $notify_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
            else
                $notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
            $notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";
        }
        /* End code to generate 'default_message' */

        $response = $api->send(
            get_option('new_comment'),
            array('address' => $author->user_email),
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
                    'blogname' => get_option('blogname'),
                    'default_message'=> htmlDefaultMessage($notify_message)
                )
            )
        );
    }
}

// Replace new user email
if (!function_exists('wp_new_user_notification')) {
    function wp_new_user_notification($user_id, $plaintext_pass = "")
    {
        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        $api = new \sendwithus\API($GLOBALS['api_key']);

        $user = get_userdata( $user_id );

        // Below is used to create 'default_message'
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";
        $message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
        $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
        $message .= wp_login_url() . "\r\n";

        $response = $api->send(
            get_option('new_user'),
            array('address' => $user_email),
            array(
                'email_data' => array(
                    'user_login' => $user_login,
                    'password' => $plaintext_pass,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'caps' => $user->caps,
                    'blogname' => get_option('blogname'),
                    'default_message' => htmlDefaultMessage($message)
                )
            )
        );
    }
}

// Use swu to send comments awaiting moderation to all moderators
if (!function_exists('wp_notify_moderator')) {
    function wp_notify_moderator($comment_id)
    {
        if ( 0 == get_option( 'moderation_notify' ) )
            return true;

        $comment = get_comment($comment_id);
        $post = get_post($comment->comment_post_ID);
        $user = get_userdata( $post->post_author );

        // Send to the administration and to the post author if the author can modify the comment.
        $emails = array( get_option( 'admin_email' ) );
        if ( user_can( $user->ID, 'edit_comment', $comment_id ) && ! empty( $user->user_email ) ) {
            if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) )
                $emails[] = $user->user_email;
        }

        /**
         * Filter the list of recipients for comment moderation emails.
         *
         * @since 3.7.0
         *
         * @param array $emails     List of email addresses to notify for comment moderation.
         * @param int   $comment_id Comment ID.
         */
        $emails = apply_filters( 'comment_moderation_recipients', $emails, $comment_id );

        /* Begin code to generate 'default_message' */
        switch ( $comment->comment_type ) {
            case 'trackback':
                $notify_message  = sprintf( __('A new trackback on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $notify_message .= sprintf( __('Website : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= __('Trackback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
            case 'pingback':
                $notify_message  = sprintf( __('A new pingback on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $notify_message .= sprintf( __('Website : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= __('Pingback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
            default: // Comments
                $notify_message  = sprintf( __('A new comment on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= sprintf( __('Whois  : http://whois.arin.net/rest/ip/%s'), $comment->comment_author_IP ) . "\r\n";
                $notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
        }

        $notify_message .= sprintf( __('Approve it: %s'),  admin_url("comment.php?action=approve&c=$comment_id") ) . "\r\n";
        if ( EMPTY_TRASH_DAYS )
            $notify_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
        else
            $notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
        $notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";

        $notify_message .= sprintf( _n('Currently %s comment is waiting for approval. Please visit the moderation panel:',
            'Currently %s comments are waiting for approval. Please visit the moderation panel:', $comments_waiting), number_format_i18n($comments_waiting) ) . "\r\n";
        $notify_message .= admin_url("edit-comments.php?comment_status=moderated") . "\r\n";
        /* End code to generate 'default_message' */

        $api = new \sendwithus\API($GLOBALS['api_key']);

        foreach ( $emails as $email ) {
            $response = $api->send(
                get_option('awaiting_approval'),
                array('address' => $email),
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
                        'blogname' => get_option('blogname'),
                        'default_message' => htmlDefaultMessage($notify_message)
                    )
                )
            );
        }

        return true;
    }
}

if (!function_exists('wp_password_change_notification')) {
    function wp_password_change_notification( $user )
    {
        $message = sprintf(__('Password Lost and Changed for user: %s'), $user->user_login) . "\r\n";

        $api = new \sendwithus\API($GLOBALS['api_key']);

        $response = $api->send(
            get_option('password_change_notification'),
            array('address' => get_option('admin_email')),
            array(
                'email_data' => array(
                    'user_login' => $user->user_login,
                    'user_pass' => $user->user_pass,
                    'user_nicename' => $user->user_nicename,
                    'user_email' => $user->user_email,
                    'user_url' => $user->user_url,
                    'user_registered' => $user->user_registered,
                    'user_activation_key' => $user->user_activation_key,
                    'user_status' => $user->user_status,
                    'display_name' => $user->display_name,
                    'spam' => $user->spam,
                    'deleted' =>$user->deleted,
                    'default_message' => htmlDefaultMessage($message)
                )
            )
        );
    }
}

/* 
 * FILTER BASED OVERRIDES	
 */

// Adds a function to occur when the filter retrieve_password_message is called
add_filter ("retrieve_password_message", "reset_password_notification", 10, 2 );

function reset_password_notification($content, $key) {
   //Grabs the information about the user attempting to reset their password
    $input = filter_input( INPUT_POST, 'user_login', FILTER_SANITIZE_STRING );

    if( is_email( $input ) ) {
	    $user = get_user_by( 'email', $input );
    } else {
        $user = get_user_by( 'login', sanitize_user( $input ) );
    }

    $user_info = get_userdata($user->ID);

    //Creates a string to hold the end section of the password reset link
    $message = 'wp-login.php?action=rp&key='. $key. '&login='.$user->user_login;
    //Appends the password reset link to the url of the site we want the password to be reset on            
    $url = network_site_url($message);
    //Gets the blogname
    $blogname = get_bloginfo('name');

    // Modify the message content to include the URL
    $content .= $url;

    //Create a new SWU email with the password reset information
   	$api = new \sendwithus\API($GLOBALS['api_key']);
    $response = $api->send(
        get_option('password_reset'),
        array('address' => $user->user_email),
        array(
            'email_data' => array(
                'user_login' => $user->user_login,
                'reset_url' => $url,
                'user_nicename' => $user->user_nicename,
                'user_email' => $user->user_email,
                'blog_name' => $blogname,
                'default_message' => htmlDefaultMessage($content)
            )
        )
    ); 

    return false;
}


/* 
 * MULTISITE BASED OVERRIDES	
 */

// Problem: These functions aren't pluggable!
// Solution: Filters! 

// Filter for when a new blog is created on a multisite site.
add_filter ("newblog_notify_siteadmin", "swu_newblog_notify_siteadmin", 10, 1);

function swu_newblog_notify_siteadmin($msg) {
    $api = new \sendwithus\API($GLOBALS['api_key']);

    // Extract pertinent information from the message.
    // Maybe a better way to do this? Filter is called after message is assembled...
    preg_match("/New\sSite:\s([^\\n]*)/", $msg, $site_name);
    preg_match("/URL:\s([^\\n]*)/", $msg, $site_url);
    preg_match("/Remote\sIP:\s([^\\n]*)/", $msg, $remote_ip);
    preg_match("/Disable\sthese\snotifications:\s([^\\n]*)/", $msg, $disable_notifications);

    $email = get_site_option( 'admin_email' );

    $response = $api->send(
        get_option('ms_new_blog_network_admin'),
        array('address' => $email),
        array(
            'email_data' => array(
                    'site_name' => $site_name[1],
                    'site_url' => $site_url[1],
                    'remote_ip' => $remote_ip[1],
                    'disable_notifications' => $disable_notifications[1],
                    'default_message' => htmlDefaultMessage($msg)
            )
        )
    );

    return false;
}

?>
