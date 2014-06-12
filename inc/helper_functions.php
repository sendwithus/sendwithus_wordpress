<?php
/*
 *  MISCELLANEOUS FUNCTIONS
 */

// Wrapper for the emails() function in the API
function get_templates() {
	if(is_network_admin()) {
		$api_key = get_site_option( 'api_key' );
	} else {
		$api_key = get_option( 'api_key' );
	}
    $api = new \sendwithus\API($api_key);
    $response = $api->emails();

    return $response;
}

function set_globals(){
    $GLOBALS['templates'] = get_templates();
    $GLOBALS['api_key'] = get_api_key();
}

// Function for creating a default template if there isn't one already (Single Site Instance)
function create_default_template(){
    $active_templates = get_templates();

    $api_key = get_option('api_key');
    $api = new \sendwithus\API($api_key);
    $response = $api->emails();

    //Get the default wordpress email template ID
    $default_id = get_option('default_wordpress_email_id');
    $default_deleted = true;

    // Ensure that the default template hasn't been deleted.
    foreach ( $active_templates as $current ) {
        if ( $current->id == $default_id && $default_id != "" ) {
            $default_deleted = false;
        }
        if($current->name == 'Default Wordpress email'){
            update_option('default_wordpress_email_id', $current->id);
            return;
        }
    }

    //If the default wordpress template id isn't in the array
    if($default_id == "" || $default_deleted){
        //Create a new template for default wordpress emails
        $response = $api->create_email('Default Wordpress email',
                    '{{default_email_subject}} ',
                    '<html><head></head><body>{{default_message}}</body></html>');

        // Only save if the response is good.
        if ( is_object($response) ) {
            update_option('default_wordpress_email_id', $response->id);
        }
    }
}
// Function for creating a default template if there isn't one already (Multi Site Instance)
function ms_create_default_template(){
    $active_templates = get_templates();

    $api_key = get_option('api_key');
    $api = new \sendwithus\API($api_key);
    $response = $api->emails();

    //Get the default wordpress email template ID
    $default_id = get_site_option('ms_default_wordpress_email_id');
    $default_deleted = true;

    // Ensure that the default template hasn't been deleted.
    foreach ( $active_templates as $current ) {
        if ( $current->id == $default_id && $default_id != "" ) {
            $default_deleted = false;
        }
        if($current->name == 'Default Wordpress email'){
            update_site_option('ms_default_wordpress_email_id', $current->id);
            return;
        }
    }

    //If the default wordpress template id isn't in the array
    if($default_id == "" || $default_deleted){
        //Create a new template for default wordpress emails
        $response = $api->create_email('Default Wordpress email',
                    '{{default_email_subject}} ',
                    '<html><head></head><body>{{default_message}}</body></html>');

        // Only save if the response is good.
        if ( is_object($response) ) {
            update_site_option('ms_default_wordpress_email_id', $response->id);
        }
    }
}

// Get the API key for use as a global variable.
function get_api_key() {
	if(is_network_admin()){
		return get_site_option('api_key');
	}
    return get_option('api_key');
}

// Generate a template selection drop down list
function generate_template_selection($name, $array) {
    $input_code = '<select name="' . $name . '" style="width: 100%">';
	if(is_network_admin()) {
		$current_template = get_site_option( $name );
	} else {
		$current_template = get_option( $name );
	}

    // Assign it to the default if no template is returned.
    if($current_template == "") {
	    if(is_network_admin()){
		    $current_template = get_site_option('default_wordpress_email_id');
	    } else {
            $current_template = get_option('default_wordpress_email_id');
	    }
    }

    foreach ($array as $template) {
        if ($template->id == $current_template) {
            $input_code .= '<option value=' . $template->id . ' selected>' . $template->name . '</option>';
        } else {
            $input_code .= '<option value=' . $template->id . '>' . $template->name . '</option>';
        }
    }

    $input_code .= '</select>';
    return $input_code;
}

// Generate table body from the wp_notification arrays
function generate_template_table($notification_array) {
    foreach ($notification_array as $current => $text) {
        echo '<tr><td style="width: 49%; padding-bottom: 0px;"><strong>' . $text['event'] .'</strong>';
        echo '<div class="' . $current . '-description"><p class="description">' . $text['description'] . '</p></div>';
        echo '</td><td style="text-align: right; padding-bottom: 0px;">';
        echo generate_template_selection($current, $GLOBALS['templates']);
        echo '<div class="button display_info parameters_button ' . $current . '">Show parameters sent to sendwithus</div>';
        echo '</td></tr>';
        echo '<tr><td colspan="2" style="padding-top: 0px;">';
        echo generate_parameter_listing($current, $text);
        echo '</td></tr>';
    }
}

// Generate code to display/hide parameters sent with events.
function generate_parameter_listing($name, $parameterData) {
    $parameterListing = '
        <span class="parameters ' . $name . '">' . $parameterData['parameters'] . 
        '</span>';

    return $parameterListing;
}

// Make 'default_message' HTML friendly.
function html_default_message($default_message) {
    // Convert newline into line breaks.
    return preg_replace('/\\n/', '<br>', $default_message);
}

// Used to create an area to save plugin settings.
function sendwithus_register_settings() {
    //Use site_option if we are using a multisite instance
    if(is_multisite()) {
	    // Make sure the default template ID doesn't get overwritten!
	    $default_id = get_site_option( 'ms_default_wordpress_email_id' );
	    register_setting( 'sendwithus_settings', 'default_wordpress_email_id' );
	    update_option( 'ms_default_wordpress_email_id', $default_id );

	    if ( is_network_admin() ) { // Only change site options if network admin
	        $default_id = get_site_option( 'ms_default_wordpress_email_id' );
	        update_site_option( 'ms_default_wordpress_email_id', $default_id );
        }
    }
    else{
        // Make sure the default template ID doesn't get overwritten!
        $default_id = get_option('default_wordpress_email_id');
        register_setting( 'sendwithus_settings', 'default_wordpress_email_id');
        update_option('default_wordpress_email_id', $default_id);
    }

    // Save settings within wp_options table as 'sendwithus_settings'
    register_setting( 'sendwithus_settings', 'api_key' );

    // Whether user is using multisite functionality or not.
    register_setting( 'sendwithus_settings', 'multisite_enabled' );

    foreach ( $GLOBALS['wp_notifications'] as $key => $value ) {
        register_setting( 'sendwithus_settings', $key );

        if ( get_option($key) == "" ) { 
            // Assign default template.
            update_option($key, $default_id);
        }
    }

    foreach ( $GLOBALS['wp_ms_notifications'] as $key => $value ) {
	    register_setting( 'sendwithus_settings', $key );

        if ( get_site_option($key) == "" ) {
            // Assign default template.
            update_site_option($key, $default_id);
        }
    }
}

add_action('network_admin_edit_reg_settings','register_network_admin_settings');
function register_network_admin_settings(){
	update_site_option('api_key', $_POST['api_key']);

	// Multisite Options
	update_site_option('ms_new_user_network_admin',    $_POST['ms_new_user_network_admin']);
	update_site_option('ms_new_blog_network_admin',    $_POST['ms_new_blog_network_admin']);
	update_site_option('ms_welcome_user_notification', $_POST['ms_welcome_user_notification']);
	update_site_option('ms_welcome_notification',      $_POST['ms_welcome_notification']);
	update_site_option('ms_signup_blog_verification',  $_POST['ms_signup_blog_verification']);
	update_site_option('ms_signup_user_notification',  $_POST['ms_signup_user_notification']);

	wp_redirect(network_admin_url("admin.php?page=sendwithus.php&updated=true"));

	exit;
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

// Creates link to plugin settings in WordPress control panel.
add_action( 'admin_menu', 'activate_sidebar_shortcut' );
function activate_sidebar_shortcut() {
    // Add the shortcut for the plugin settings underneath the 'plugins' sidebar menu.
    add_menu_page( 'sendwithus', 'sendwithus', 'manage_options', 'sendwithus.php', 'sendwithus_conf_main', 'dashicons-email-alt' );

    // Create an area in WordPress to store the settings saved by the user.
    add_action( 'admin_init', 'sendwithus_register_settings' );
}

// Creates link to plugin settings in Network Administrator Control Panel
add_action('network_admin_menu', 'activate_network_sidebar_shortcut');
function activate_network_sidebar_shortcut() {
	// Add the shortcut for the plugin settings underneath the 'plugins' sidebar menu.
	add_menu_page( 'sendwithus', 'sendwithus', 'manage_options', 'sendwithus.php', 'sendwithus_conf_main', 'dashicons-email-alt' );

	// Create an area in WordPress to store the settings saved by the user.
	add_action( 'admin_init', 'sendwithus_register_settings' );
}

/*
add_action('network_admin_menu', 'activate_sidebar_shortcut_network_admin');
function activate_sidebar_shortcut_network_admin(){
    add_menu_page( 'sendwithus', 'sendwithus', 'manage_options', 'sendwithus.php', 'sendwithus_conf_main', 'dashicons-email-alt' );
}
*/
// Warn the user if their api key is invalid
add_action( 'admin_notices', 'sendwithus_no_api_key_warning' );
function sendwithus_no_api_key_warning() {
    $site_url = get_site_url(null, '/wp-admin/admin.php?page=sendwithus.php');
    if ( $GLOBALS['api_key'] == false ) {
        echo "<div id='invalid_key_warning'>
                You are using an invalid sendwithus API key. This means that no site-related emails will be sent!<br/>
                Please <a href='" . $site_url . "''> update it </a> now!
              </div>";
    }
}

?>
