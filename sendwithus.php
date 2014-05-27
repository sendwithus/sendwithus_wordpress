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

require( 'sendwithus_php/lib/API.php' );
require( 'inc/globals.php' );
require( 'inc/helper_functions.php' );
require( 'inc/single_site_overrides.php' );
require( 'inc/multisite_overrides.php' );

// Add stylesheet
add_action( 'admin_enqueue_scripts', 'register_style_sheet' );
function register_style_sheet() {
    wp_register_style( 'sendwithus_style', plugins_url( '/css/sendwithus_style.css', __FILE__ ) );
    wp_enqueue_style( 'sendwithus_style' );
}

add_action( 'admin_menu', 'activate_sidebar_shortcut' );
// Creates link to plugin settings in WordPress control panel.
function activate_sidebar_shortcut() {
    // Add the shortcut for the plugin settings underneath the 'plugins' sidebar menu.
    add_menu_page( 'sendwithus', 'sendwithus', 'manage_options', 'sendwithus.php', 'sendwithus_conf_main', 'dashicons-email-alt' );

    // Create an area in WordPress to store the settings saved by the user.
    add_action( 'admin_init', 'sendwithus_register_settings' );
}

// Warn the use if their api key is invalid
function sendwithus_no_api_key_warning() {
    $site_url = get_site_url();
    if ( $GLOBALS['api_key'] == false ) {
        echo "<br />
              <div id='invalid_key_warning'>
                You are using an invalid sendwithus API key. Please <a href='" . $site_url . "/wordpress/wp-admin/admin.php?page=sendwithus.php'> update it </a> now!
              </div>";
    }
}

add_action( 'admin_notices', 'sendwithus_no_api_key_warning' );

// Used to create an area to save plugin settings.
function sendwithus_register_settings() {
    // Save settings within wp_options table as 'sendwithus_settings'
    register_setting( 'sendwithus_settings', 'api_key' );
    register_setting( 'sendwithus_settings', 'display_parameters' );

    // Whether user is using multisite functionality or not.
    register_setting( 'sendwithus_settings', 'multisite_enabled' );

    foreach ( $GLOBALS['wp_notifications'] as $key => $value ) {
        register_setting( 'sendwithus_settings', $key );
    }

    foreach ( $GLOBALS['wp_ms_notifications'] as $key => $value ) {
        register_setting( 'sendwithus_settings', $key );
    }
}

// Add a simple WordPress pointer to Settings menu - shows new user where to find swu.

function display_pointer( $hook_suffix ) {
    // Assume pointer shouldn't be shown
    $enqueue_pointer_script_style = false;

    // Get array list of dismissed pointers for current user and convert it to array
    $dismissed_pointers = explode( ',', get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

    // Check if our pointer is not among dismissed ones
    if( !in_array( 'thsp_sendwithus_pointer', $dismissed_pointers ) ) {
        $enqueue_pointer_script_style = true;
        
        // Add footer scripts using callback function
        add_action( 'admin_print_footer_scripts', 'populate_pointer' );
    }

    // Enqueue pointer CSS and JS files, if needed
    if( $enqueue_pointer_script_style ) {
        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'wp-pointer' );
    }
    
}

// Used to display the pointer and control what happens when the user closes it.
add_action( 'admin_enqueue_scripts', 'display_pointer' );
function populate_pointer() {
    $pointer_content  = "<h3>sendwithus activated!</h3>";
    $pointer_content .= "<p>The sendwithus WordPress plugin can be accessed here!</p><p>Continue your installation by clicking on the menu.</p>";
    ?>
    
    <script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function($) {
        $('#toplevel_page_sendwithus').pointer({
            content: '<?php echo $pointer_content; ?>',
            position: {
                edge: 'left', // arrow direction
                align: 'center' // vertical alignment
            },
            pointerWidth: 350,
            close: function() {
                $.post(ajaxurl, {
                    pointer: 'thsp_sendwithus_pointer', // pointer ID
                    action: 'dismiss-wp-pointer'
                });
            }
        }).pointer('open');
    });
    //]]>
    </script>
<?php
}

// Display a help message to new users of the plugin.
function display_getting_started_message() {
    global $current_user;
    $userid = $current_user->ID;

    //delete_user_meta($userid, 'hide_getting_started_message');

    // Only show message if user hasn't dismissed it.
    if ( ! get_user_meta( $userid, 'hide_getting_started_message' ) ) {
        echo '<div class="getting_started updated"><h2 style="margin-bottom: 5px;">New to sendwithus? <a href="http://www.sendwithus.com/docs" target="_blank">Read the docs!</a></h2><a id="dismiss_message">Dismiss this message.</a></div>';
    }
}

// AJAX function to disable the getting started message.
add_action( 'wp_ajax_turn_off_help', 'turn_off_help_callback' );
function turn_off_help_callback() {
    global $current_user;
    $userid = $current_user->ID;
    add_user_meta( $userid, 'hide_getting_started_message', 'yes', true );
    die();
}


$GLOBALS['templates'] = getTemplates();
$GLOBALS['api_key'] = getAPIKey();

// Establish whether an API key has been entered and that it is valid.
$GLOBALS['valid_key'] = true;
if ( $GLOBALS['api_key'] == '' || $GLOBALS['templates']->status == 'error' ) {
    $GLOBALS['valid_key'] = false;
}

// Used for displaying the main menu page.
// Activated when user clicks on link in sidebar.
function sendwithus_conf_main() {
    // Make sure that the user has appropriate permissions to be here.
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    ?>
    <!-- Font for sendwithus' logo -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

    <div id="header">
        <h1>
            <a href="http://www.sendwithus.com" target="_blank">
                <span style="color: #777">send<span style="color: #f7931d">with</span>us</span>
            </a>
        </h1>

        <form action="http://www.sendwithus.com/docs" target="_blank">
            <button id="help_button" class="button" style="float: right">Docs</button>
        </form>
        <p>Enable transactional emails within WordPress with ease.</p>
    </div>
    <?php
    display_getting_started_message();
    ?>
    <div class="welcome-panel">
        <!-- A check should be performed before loading the table to ensure that the user
             has entered an API key - otherwise only an entry for API key should be displayed. -->
        <form action="options.php" method="post">
            <?php
            // Load up the previously saved settings.
            settings_fields( 'sendwithus_settings' );
            do_settings_sections( 'sendwithus_settings' );
            ?>

            <!-- Only display if API key is populated -->
            <?php if ( $GLOBALS['valid_key'] ) : ?>
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
            <?php if ( $GLOBALS['valid_key'] ) : ?>
                <table class="wp-list-table widefat sendwithus_table">
                    <?php if ( is_multisite() ) : ?>
                        <thead>
                        <th colspan="2">
                            <p class="table_description">Single-site Events</p>
                            <p class="description" style="text-align: center;">
                                Single-site events occur on all WordPress installations. They are primarly concerned with user and comment moderation.
                            </p>
                        </th>
                        </thead>
                    <?php endif; ?>
                    <?php generateTemplateTable( $GLOBALS['wp_notifications'] ); ?>
                </table>
                <!-- Events that are displayed when multisite events are enabled -->
                <?php if ( is_multisite() ) : ?>
                    <table class="multisite wp-list-table widefat" id="multisite_table">
                        <thead>
                        <th colspan="2">
                            <p class="table_description">Multi-site Events</p>

                            <p class="description"
                               style="text-align: center;">Multi-site events are specific to WordPress instances that host multiple WordPress sites. As such, they feature several events specific to administering multiple sites.</p>
                        </th>
                        </thead>
                        <?php generateTemplateTable( $GLOBALS['wp_ms_notifications'] ); ?>
                    </table>
                <?php endif; ?>
                <!-- Display a notice telling the user to enter their API key & save -->
            <?php else : ?>
                <table>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <h2>In order to proceed please enter a valid API key and save your changes.</h2>

                            <p>Don't know what that is? Log in to your <a
                                    href="http://www.sendwithus.com">sendwithus control panel</a> and check under 'API Settings.'</p>
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

            $('#dismiss_message').click(function() {
                $('.getting_started').css('display', 'none');

                $.post(ajaxurl, { action: 'turn_off_help' });
            });
        </script>
    </div>
<?
}

?>
