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
					<td>New Comment</td>
					<td>
						<!-- This should pull from swu to list the available templates -->
                        <?php

                            echo  generateTemplateSelection("new_comment", $GLOBALS['templates']);

                        ?>
					</td>
				</tr>
				<tr>
					<td>Event Example #2</td>
					<td>
						<!-- This should pull from swu to list the available templates -->
                        <?php
                            echo  generateTemplateSelection("event1", $GLOBALS['templates']);
                        ?>
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
    // Override of WordPress' wp_mail function.
    // Sendwithus' capabilities will be provided within here.
    if ( !function_exists('wp_mail') ) {
        function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
            // Compact the input, apply the filters, and extract them back out
            extract( apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) ) );

            if ( !is_array($attachments) )
                $attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );

            global $phpmailer;

            // (Re)create it, if it's gone missing
            if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {
                require_once ABSPATH . WPINC . '/class-phpmailer.php';
                require_once ABSPATH . WPINC . '/class-smtp.php';
                $phpmailer = new PHPMailer( true );
            }

            // Headers
            if ( empty( $headers ) ) {
                $headers = array();
            } else {
                if ( !is_array( $headers ) ) {
                    // Explode the headers out, so this function can take both
                    // string headers and an array of headers.
                    $tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
                } else {
                    $tempheaders = $headers;
                }
                $headers = array();
                $cc = array();
                $bcc = array();

                // If it's actually got contents
                if ( !empty( $tempheaders ) ) {
                    // Iterate through the raw headers
                    foreach ( (array) $tempheaders as $header ) {
                        if ( strpos($header, ':') === false ) {
                            if ( false !== stripos( $header, 'boundary=' ) ) {
                                $parts = preg_split('/boundary=/i', trim( $header ) );
                                $boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
                            }
                            continue;
                        }
                        // Explode them out
                        list( $name, $content ) = explode( ':', trim( $header ), 2 );

                        // Cleanup crew
                        $name = trim( $name );
                        $content = trim( $content );

                        switch ( strtolower( $name ) ) {
                            // Mainly for legacy -- process a From: header if it's there
                            case 'from':
                                if ( strpos($content, '<' ) !== false ) {
                                    // So... making my life hard again?
                                    $from_name = substr( $content, 0, strpos( $content, '<' ) - 1 );
                                    $from_name = str_replace( '"', '', $from_name );
                                    $from_name = trim( $from_name );

                                    $from_email = substr( $content, strpos( $content, '<' ) + 1 );
                                    $from_email = str_replace( '>', '', $from_email );
                                    $from_email = trim( $from_email );
                                } else {
                                    $from_email = trim( $content );
                                }
                                break;
                            case 'content-type':
                                if ( strpos( $content, ';' ) !== false ) {
                                    list( $type, $charset ) = explode( ';', $content );
                                    $content_type = trim( $type );
                                    if ( false !== stripos( $charset, 'charset=' ) ) {
                                        $charset = trim( str_replace( array( 'charset=', '"' ), '', $charset ) );
                                    } elseif ( false !== stripos( $charset, 'boundary=' ) ) {
                                        $boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset ) );
                                        $charset = '';
                                    }
                                } else {
                                    $content_type = trim( $content );
                                }
                                break;
                            case 'cc':
                                $cc = array_merge( (array) $cc, explode( ',', $content ) );
                                break;
                            case 'bcc':
                                $bcc = array_merge( (array) $bcc, explode( ',', $content ) );
                                break;
                            default:
                                // Add it to our grand headers array
                                $headers[trim( $name )] = trim( $content );
                                break;
                        }
                    }
                }
            }

            // Empty out the values that may be set
            $phpmailer->ClearAllRecipients();
            $phpmailer->ClearAttachments();
            $phpmailer->ClearCustomHeaders();
            $phpmailer->ClearReplyTos();

            // From email and name
            // If we don't have a name from the input headers
            if ( !isset( $from_name ) )
                $from_name = 'WordPress';

            /* If we don't have an email from the input headers default to wordpress@$sitename
            * Some hosts will block outgoing mail from this address if it doesn't exist but
            * there's no easy alternative. Defaulting to admin_email might appear to be another
            * option but some hosts may refuse to relay mail from an unknown domain. See
            * http://trac.wordpress.org/ticket/5007.
            */

            if ( !isset( $from_email ) ) {
                // Get the site domain and get rid of www.
                $sitename = strtolower( $_SERVER['SERVER_NAME'] );
                if ( substr( $sitename, 0, 4 ) == 'www.' ) {
                    $sitename = substr( $sitename, 4 );
                }

                $from_email = 'wordpress@' . $sitename;
            }

            // Plugin authors can override the potentially troublesome default
            $phpmailer->From = apply_filters( 'wp_mail_from' , $from_email );
            $phpmailer->FromName = apply_filters( 'wp_mail_from_name', $from_name );

            // Set destination addresses
            if ( !is_array( $to ) )
                $to = explode( ',', $to );

            foreach ( (array) $to as $recipient ) {
                try {
                    // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                    $recipient_name = '';
                    if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
                        if ( count( $matches ) == 3 ) {
                            $recipient_name = $matches[1];
                            $recipient = $matches[2];
                        }
                    }
                    $phpmailer->AddAddress( $recipient, $recipient_name);
                } catch ( phpmailerException $e ) {
                    continue;
                }
            }

            // Set mail's subject and body
            $phpmailer->Subject = $subject;
            $phpmailer->Body = $message;

            // Add any CC and BCC recipients
            if ( !empty( $cc ) ) {
                foreach ( (array) $cc as $recipient ) {
                    try {
                        // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                        $recipient_name = '';
                        if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
                            if ( count( $matches ) == 3 ) {
                                $recipient_name = $matches[1];
                                $recipient = $matches[2];
                            }
                        }
                        $phpmailer->AddCc( $recipient, $recipient_name );
                    } catch ( phpmailerException $e ) {
                        continue;
                    }
                }
            }

            if ( !empty( $bcc ) ) {
                foreach ( (array) $bcc as $recipient) {
                    try {
                        // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                        $recipient_name = '';
                        if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
                            if ( count( $matches ) == 3 ) {
                                $recipient_name = $matches[1];
                                $recipient = $matches[2];
                            }
                        }
                        $phpmailer->AddBcc( $recipient, $recipient_name );
                    } catch ( phpmailerException $e ) {
                        continue;
                    }
                }
            }

// Set to use PHP's mail()
            $phpmailer->IsMail();

// Set Content-Type and charset
// If we don't have a content-type from the input headers
            if ( !isset( $content_type ) )
                $content_type = 'text/plain';

            $content_type = apply_filters( 'wp_mail_content_type', $content_type );

            $phpmailer->ContentType = $content_type;

// Set whether it's plaintext, depending on $content_type
            if ( 'text/html' == $content_type )
                $phpmailer->IsHTML( true );

// If we don't have a charset from the input headers
            if ( !isset( $charset ) )
                $charset = get_bloginfo( 'charset' );

// Set the content-type and charset
            $phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );

// Set custom headers
            if ( !empty( $headers ) ) {
                foreach( (array) $headers as $name => $content ) {
                    $phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
                }

                if ( false !== stripos( $content_type, 'multipart' ) && ! empty($boundary) )
                    $phpmailer->AddCustomHeader( sprintf( "Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary ) );
            }

            if ( !empty( $attachments ) ) {
                foreach ( $attachments as $attachment ) {
                    try {
                        $phpmailer->AddAttachment($attachment);
                    } catch ( phpmailerException $e ) {
                        continue;
                    }
                }
            }

            do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );

// Send!
            try {
                return $phpmailer->Send();
            } catch ( phpmailerException $e ) {
                return false;
            }
        }
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

?>
