<?php
/*
 * MULTISITE BASED OVERRIDES
 */

// Problem: These functions aren't pluggable!
// Solution: Filters!

// Filter for when a new blog is created on a multisite site.
add_filter("newblog_notify_siteadmin", "swu_newblog_notify_siteadmin", 10, 1);

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

// Filter for when a new user has been activated - notify the network admin.
add_filter("newuser_notify_siteadmin", "swu_newuser_notify_siteadmin", 10, 2);

function swu_newuser_notify_siteadmin($msg, $user) {
    $api = new \sendwithus\API($GLOBALS['api_key']);    

    $email = get_site_option( 'admin_email' );
    $options_site_url = esc_url(network_admin_url('settings.php'));
    $remote_ip = wp_unslash( $_SERVER['REMOTE_ADDR'] );

    $msg .= "Test add";

    $response = $api->send(
        get_option('ms_new_user_network_admin'),
        array('address' => $email),
        array(
            'email_data' => array(
                'user' => $user->user_login,
                'site_url' => $options_site_url,
                'remote_ip' => $remote_ip,
                'default_message' => htmlDefaultMessage($msg)
            )
        )
    );

    return false;
}

// Filter for when a new signup has been successful. Used when site registration is enabled.
add_filter("wpmu_signup_blog_notification_email", "swu_wpmu_signup_blog_notification", 10, 8);

function swu_wpmu_signup_blog_nofitication($content, $domain, $path, $title, $user, $user_email, $key, $meta) {
    $api = new \sendwithus\API($GLOBALS['api_key']); 

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

    $response = $api->send(
        get_option('ms_new_user_network_admin'),
        array('address' => $user_email),
        array(
            'email_data' => array(
                'default_message' => htmlDefaultMessage($default_message)
                )
            )
        );
 
    return false;

}

// Filter for when a new user has signed up for a multiuser site.
add_filter( 'wpmu_signup_user_notification', 'swu_wpmu_signup_user_notification', 10, 4 );

function swu_wpmu_signup_user_notification($user, $user_email, $key, $meta = '') {
    $api = new \sendwithus\API($GLOBALS['api_key']);
    $blog_name = get_bloginfo('name');
    $blog_url = network_site_url();

    $message = '/wp-activate.php?key='. $key;  
    $url = network_site_url($message);

    $response = $api->send(
        get_option('ms_welcome_user_notification'),
        array('address' => $user_email),
        array(
            'email_data' => array(
                    'user_login' => $user,
                    'user_email' => $user_email,
                    'registered' => current_time('mysql', true),
                    'activation_key' => $url,
                    'blog_name' => $blog_name,
                    'blog_url' => $blog_url,
                    'meta' => $meta
            )
        )
    );

    return false;
}