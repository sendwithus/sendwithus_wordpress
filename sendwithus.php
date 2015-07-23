<?php
/**
 * @package sendwithus
 * @version 1.1.0
 */
/*
Plugin Name: sendwithus
Plugin URI: http://www.sendwithus.com
Description: Easily integrate transactional email into WordPress' default emails.
Author: Dylan Moore, Kyle Poole, and Cory Purnell
Version: 1.1.0
Author URI: http://www.sendwithus.com
*/

require( 'sendwithus_php/lib/API.php' );
require( 'inc/globals.php' );
require( 'inc/helper_functions.php' );
require( 'inc/single_site_overrides.php' );
require( 'inc/multisite_overrides.php' );

// API key is needed throughout. 
$GLOBALS['api_key'] = get_api_key();

// Add stylesheet
add_action( 'admin_enqueue_scripts', 'register_style_sheet' );
function register_style_sheet() {
    wp_register_style( 'sendwithus_style', plugins_url( '/css/sendwithus_style.css', __FILE__ ) );
    wp_enqueue_style( 'sendwithus_style' );
}

// Used for displaying the main menu page.
// Activated when user clicks on link in sidebar.
function sendwithus_conf_main() {
    set_globals();

    if(is_network_admin()){
        $GLOBALS['api_key'] = get_site_option('api_key');
    }

    if ( $GLOBALS['api_key'] == '' || $GLOBALS['templates']->status == 'error' ) {
        $GLOBALS['valid_key'] = false;
    } else {
        // Establish whether an API key has been entered and that it is valid.
        $GLOBALS['valid_key'] = true;
        if(is_network_admin()){
            add_action( 'init', 'ms_create_default_template');
            // Some sites don't work with muplugins_loaded for some reason.
            // This will make default be created.
            if ( did_action('create_default_template') == 0 ) {
               add_action( 'plugins_loaded', 'ms_create_default_template');
            } 
        }
        else{
            add_action( 'init', 'create_default_template');      
            // Some sites don't work with muplugins_loaded for some reason.
            // This will make default be created.
            if ( did_action('create_default_template') == 0 ) {
               add_action( 'plugins_loaded', 'create_default_template');
            }
        }
        add_action( 'plugins_loaded', 'set_globals');
    }

    // Make sure that the user has appropriate permissions to be here.
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    ?>
    <!-- Font for sendwithus' logo -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

    <div id="header">
        <h1 style="float: left; margin-top: 10px; margin-bottom: 0;">
            <a href="http://www.sendwithus.com" target="_blank">
                <span style="color: #777">send<span style="color: #f7931d">with</span>us</span>
            </a>
        </h1>
        <p style="float: right; margin-right: 20px; margin-bottom: 0;">Enable transactional emails within WordPress with ease.</p>
    </div>
    <?php
    display_getting_started_message();
    ?>
    <div style="margin-top: 0; text-align: center;">
        <form action="http://www.sendwithus.com/login" target="_blank" class="site_button">
            <button id="dashboard_button" class="button">Dashboard</button>
        </form>
        <form action="http://www.sendwithus.com/docs" target="_blank" class="site_button">
            <button id="help_button" class="button">Docs</button>
        </form>
        <form action="https://www.sendwithus.com/#/emails" target="_blank" class="site_button">
            <button id="help_button" class="dashboard_button button">Templates</button>
        </form>
        <form action="https://www.sendwithus.com/?#/analytics" target="_blank" class="site_button">
            <button id="help_button" class="dashboard_button button">Analytics</button>
        </form>
        <form action="https://www.sendwithus.com/?#/logs" target="_blank" class="site_button">
            <button id="help_button" class="dashboard_button button">Email Logs</button>
        </form>
        <form action="https://www.sendwithus.com/?#/api_settings" target="_blank" class="site_button">
            <button id="help_button" class="dashboard_button button">API Settings</button>
        </form>
    </div>

    <div class="welcome-panel">
        <!-- A check should be performed before loading the table to ensure that the user
             has entered an API key - otherwise only an entry for API key should be displayed. -->
	    <?php if ( is_network_admin() ) : ?>
	    <form action="edit.php?action=reg_settings" method="post">

	    <?php else : ?>
        <form action="options.php" method="post">
	    <?php endif ?>
            <?php
            // Load up the previously saved settings.
            settings_fields( 'sendwithus_settings' );
            do_settings_sections( 'sendwithus_settings' );
            ?>

            <!-- Hidden input containing default template ID -->
			<?php if ( is_network_admin() ) : ?><!-- Just for the network admin-->
				<input id="default_wordpress_email_id" name="default_wordpress_email_id"
				       style="display: none;" value="<?php echo get_site_option('default_wordpress_email_id'); ?>" />

			<?php else : ?>
            <input id="default_wordpress_email_id" name="default_wordpress_email_id"
                style="display: none;" value="<?php echo get_option('default_wordpress_email_id'); ?>" />
			<?php endif ?>

            <!-- Only display if API key is populated -->
            <?php if ( $GLOBALS['valid_key'] ) : ?>
                <h3>Events</h3>
                <table style="width: 100%">
                    <tr>
                        <td style="vertical-align: top;">
                            <p class="description">Events that trigger the sending of transactional emails are listed below.</p>
                        </td>
                        <td>
                            <div id="api_entry" style="display: none;">
                                <input id="api_box" type="text" name="api_key"
                                       placeholder="Your sendwithus API key."
                                       value="<?php echo get_api_key(); ?>"/>
                                <?php submit_button(); ?>
                            </div>

                            <div id="api_button" class="button">Show API Key</div>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>

            <!-- Only display if API key is populated -->
            <?php if ( $GLOBALS['valid_key']) : ?>
	            <?php if(!is_network_admin()) : ?>
                <table class="wp-list-table widefat sendwithus_table">
                    <?php generate_template_table( $GLOBALS['wp_notifications'] ); ?>
                </table>
	            <?php endif; ?>
                <!-- Events that are displayed when multisite events are enabled -->
                <?php if ( is_network_admin() ) : ?>
                    <table class="multisite wp-list-table widefat" id="multisite_table">
                        <thead>
                        <th colspan="2">
                            <p class="table_description">Multi-site Events</p>

                            <p class="description"
                               style="text-align: center;">Multi-site events are specific to WordPress instances that host multiple WordPress sites. As such, they feature several events specific to administering multiple sites.</p>
                        </th>
                        </thead>
                        <?php generate_template_table( $GLOBALS['wp_ms_notifications'] ); ?>
                    </table>
                <?php endif; ?>
                <!-- Display a notice telling the user to enter their API key & save -->
            <?php else : ?>
                <table class="wp-list-table widefat">
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
                                   value="<?php echo get_api_key(); ?>"/>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>
            <div class="display_button_area">
                <?php submit_button(); ?>
            </div>
        </form>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript">
            $('.loading').hide();
            // Triggered when user chooses to see parameters passed to sendwithus.
            $('.display_info').click(function(event) {
                $(this).text(function(i, text) {
                    return text === 'Show parameters sent to sendwithus' ? 'Hide parameters' : 'Show parameters sent to sendwithus';
                });

                // Get name of class from button.
                // Kinda sloppy in how it relies on the position.
                var className = event.target.classList[3];
                $('.parameters.' + className).slideToggle(150);
                $('.test_email_button.' + className).slideToggle(150);
            });
            $('.test_email_button').click(function(){
                $(this).parent().find('.loading').show();
                var className = this.classList[2];
                var data = { action : 'test_email',
                          email : className};
                $.post(ajaxurl, data, function(response){
                    $('.loading').hide();
                });
            });

            // Used to hide/display API entry/viewing area in main screen.
            $('#api_button').click(function() {
                $(this).hide();
                $('#api_entry').show();
            });


            // Used to get rid of the 'welcome' message that pops up.
            $('#dismiss_message').click(function() {
                $('.getting_started').css('display', 'none');
                $.post(ajaxurl, { action: 'turn_off_help' });
            });
        </script>
    </div>
<?php
}

?>
