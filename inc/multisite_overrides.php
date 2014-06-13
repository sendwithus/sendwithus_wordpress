<?php

/*
 * MULTISITE BASED OVERRIDES
 */

// Problem: These functions aren't pluggable!
// Solution: Filters!

// Filter for when a new user has been activated - notify the network admin.
add_filter("newuser_notify_siteadmin", "swu_newuser_notify_siteadmin", 11, 2);
function swu_newuser_notify_siteadmin($default_message, $user) {
    $api_key = get_site_option('api_key');
	$api = new \sendwithus\API($api_key);

    $email = get_site_option( 'admin_email' );
    $options_site_url = esc_url(network_admin_url('settings.php'));
    $remote_ip = wp_unslash( $_SERVER['REMOTE_ADDR'] );
    if(isset($site_name[1])){
        $default_email_subject = "New user " . $user->user_login . " created at ". $site_name[1];
    }
    else{
        $default_email_subject = "New user " . $user->user_login . " created.";
    }
    $response = $api->send(
        get_site_option('ms_new_user_network_admin'),
        array('address' => $email),
        array(
            'email_data' => array(
                'default_email_subject' => $default_email_subject,
                'user_name' => $user->user_login,
                'remote_ip' => $remote_ip,
                'control_panel' => $options_site_url,
                'default_message' => html_default_message($default_message)
            )
        )
    );

    return false;
}

// Filter for when a new blog is created on a multisite site.
add_filter("newblog_notify_siteadmin", "swu_newblog_notify_siteadmin", 10, 1);
function swu_newblog_notify_siteadmin($default_message) {
	$api_key = get_site_option('api_key');
	$api = new \sendwithus\API($api_key);

    // Extract pertinent information from the message.
    // Maybe a better way to do this? Filter is called after message is assembled...
    preg_match("/New\sSite:\s([^\\n]*)/", $default_message, $site_name);
    preg_match("/URL:\s([^\\n]*)/", $default_message, $site_url);
    preg_match("/Remote\sIP:\s([^\\n]*)/", $default_message, $remote_ip);
    preg_match("/Disable\sthese\snotifications:\s([^\\n]*)/", $default_message, $disable_notifications);

    $email = get_site_option( 'admin_email' );
    //Subject line for default wordpress email
    $default_email_subject = "New blog ".$site_name[1]." created at ".get_option('blogname');
    $response = $api->send(
        get_site_option('ms_new_blog_network_admin'),
        array('address' => $email),
        array(
            'email_data' => array(
                'default_email_subject' => $default_email_subject,
                'site_name' => $site_name[1],
                'site_url' => $site_url[1],
                'remote_ip' => $remote_ip[1],
                'control_panel' => $disable_notifications[1],
                'default_message' => html_default_message($default_message)
            )
        )
    );

    return false;
}

add_filter("wpmu_welcome_user_notification", "swu_wpmu_welcome_user_notification", 10, 3);
function swu_wpmu_welcome_user_notification( $user_id, $password, $meta = '' ) {
	$api_key = get_site_option('api_key');
	$api = new \sendwithus\API($api_key);

    $user  = get_userdata( $user_id );
    $admin_email = get_site_option( 'admin_email' );

    if ( $admin_email == '' )
        $admin_email = 'support@' . $_SERVER['SERVER_NAME'];

    $current_site = get_current_site();

    if ( empty( $current_site->site_name ) )
        $current_site->site_name = 'WordPress';

    $default_message = get_site_option( 'welcome_user_email' );
    $default_message = apply_filters( 'update_welcome_user_email', $default_message, $user_id, $password, $meta );
    $default_message = str_replace( 'SITE_NAME', $current_site->site_name, $default_message );
    $default_message = str_replace( 'USERNAME', $user->user_login, $default_message );
    $default_message = str_replace( 'PASSWORD', $password, $default_message );
    $default_message = str_replace( 'LOGINLINK', wp_login_url(), $default_message );

    //Subject line for default wordpress email
    $default_email_subject = "Welcome to ".get_option('blogname');

    $response = $api->send(
        get_site_option('ms_welcome_user_notification'),
        array('address' => $user->user_email),
        array(
            'email_data' => array(
                'default_email_subject' => $default_email_subject,
                'user_email' => $user->user_email,
                'user_password' => $password,
                'admin_email' => $admin_email,
                'site_name' => $current_site->site_name,
                'default_message' => html_default_message($default_message)
            )
        )
    );
    
    return false;
}
//Sent to user when a blog is created for them on multisite
add_filter("wpmu_welcome_notification", "swu_wpmu_welcome_notification", 10, 5);
function swu_wpmu_welcome_notification($blog_id, $user_id, $password, $title, $meta ) {
	$api_key = get_site_option('api_key');
	$api = new \sendwithus\API($api_key);

    $current_site = get_current_site();

    $admin_email = get_site_option( 'admin_email' );

    if ( $admin_email == '' )
        $admin_email = 'support@' . $_SERVER['SERVER_NAME'];

    if ( empty( $current_site->site_name ) )
        $current_site->site_name = 'WordPress';

    $default_message = get_site_option( 'welcome_email' );
    if ( $default_message == false )
        $default_message = __( 'Dear User,

Your new SITE_NAME site has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:
Username: USERNAME
Password: PASSWORD
Log in here: BLOG_URLwp-login.php

We hope you enjoy your new site. Thanks!

--The Team @ SITE_NAME' );

    $url = get_blogaddress_by_id($blog_id);
    $user = get_userdata( $user_id );

    $default_message = str_replace( 'SITE_NAME',  $current_site->site_name, $default_message );
    $default_message = str_replace( 'BLOG_TITLE', $title, $default_message );
    $default_message = str_replace( 'BLOG_URL',   $url, $default_message );
    $default_message = str_replace( 'USERNAME',   $user->user_login, $default_message );
    $default_message = str_replace( 'PASSWORD',   $password, $default_message );
  
    //Subject line for default wordpress email
    $default_email_subject = "Your site ".$title." has been added to ".get_option('blogname');

    $response = $api->send(
        get_site_option('ms_welcome_notification'),
        array('address' => $user->user_email),
        array(
            'email_data' => array(
                'default_email_subject' => $default_email_subject,
                'user_email' => $user->user_email,
                'user_password' => $user->password,
                'admin_email' => $admin_email,
                'site_name' => $current_site->site_name,
                'site_url' => $url,
                'default_message' => html_default_message($default_message)
            )
        )
    );

    return false;
}

// Filter for when a new signup has been successful. Used when site registration is enabled.
add_filter("wpmu_signup_blog_notification_email", "swu_wpmu_signup_blog_notification", 10, 8);
function swu_wpmu_signup_blog_notification($content, $domain, $path, $title, $user, $user_email, $key, $meta = '') {
	$api_key = get_site_option('api_key');
	$api = new \sendwithus\API($api_key);

    // Generate the activation link.
    if ( !is_subdomain_install() || get_current_site()->id != 1 )
        $activate_url = network_site_url("wp-activate.php?key=$key");
    else
        $activate_url = "http://{$domain}{$path}wp-activate.php?key=$key"; // @todo use *_url() API
    $activate_url = esc_url($activate_url);

    // Get the administrator's email.
    $admin_email = get_site_option( 'admin_email' );
    if ( $admin_email == '' )
        $admin_email = 'support@' . $_SERVER['SERVER_NAME'];
    $from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );

    // Get the message together
    $default_message = sprintf($content, $activate_url, esc_url( "http://{$domain}{$path}" ), $key);
    //Subject line for default wordpress email
    $default_email_subject = "You have registered successfully for ".get_option('blogname');

    $response = $api->send(
        get_site_option('ms_signup_blog_verification'),
        array('address' => $user_email),
        array(
            'email_data' => array(
                'default_email_subject' => $default_email_subject,
                'domain' => $domain,
                'path' => $path,
                'user_name' => $user,
                'user_email' => $user_email,
                'key' => $key,
                'content' => $content,
                'default_message' => html_default_message($default_message)
            )
        )
    );
 
    return false;

}

// Filter for when a new user has signed up for a multiuser site, but not requested a new site.
add_filter( 'wpmu_signup_user_notification_email', 'swu_wpmu_signup_user_notification', 10, 5 );
function swu_wpmu_signup_user_notification($content, $user, $user_email, $key, $meta = '') {
	$api_key = get_site_option('api_key');
	$api = new \sendwithus\API($api_key);

    $blog_name = get_bloginfo('name');
    $blog_url = network_site_url();

    $message = '/wp-activate.php?key='. $key;
    $url = network_site_url($message);

    $default_message = str_replace("%s",$url,$content);

    //Subject line for default wordpress email
    $default_email_subject = "A new user has signed up at " . $blog_name;

    $response = $api->send(
        get_site_option('ms_signup_user_notification'),
        array('address' => $user_email),
        array(
            'email_data' => array(
                    'default_email_subject' => $default_email_subject,
                    'user_login' => $user,
                    'user_email' => $user_email,
                    'user_registered_date' => current_time('mysql', true),
                    'user_activation_key' => $url,
                    'blog_name' => $blog_name,
                    'blog_url' => $blog_url,
                    'default_message' => html_default_message($default_message)
            )
        )
    );

    return false;
}
?>