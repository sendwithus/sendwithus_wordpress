<?php
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

// Filter for when a new user has been activated - notify the network admin.
add_filter ("newuser_notify_siteadmin", "swu_newuser_notify_siteadmin", 10, 2);

function swu_newuser_notify_siteadmin($msg, $user) {
    $api = new \sendwithus\API($GLOBALS['api_key']);    

    $email = get_site_option( 'admin_email' );
    $options_site_url = esc_url(network_admin_url('settings.php'));
    $remote_ip = wp_unslash( $_SERVER['REMOTE_ADDR'] );

    $msg .= "Test add";

    $response = $api->send(
        get_option(''),
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

add_filter( 'wpmu_signup_user_notification_email', 'swu_wpmu_signup_user_notification', 10, 5 );

function swu_wpmu_signup_user_notification($content, $user, $user_email, $key, $meta = '') {
    $api = new \sendwithus\API($GLOBALS['api_key']);

    $blog_name = get_bloginfo('name');
    $blog_url = network_site_url();

    $message = '/wp-activate.php?key='. $key;  
    $url = network_site_url($message);

    $content = str_replace("%s",$url,$content);
    
    $response = $api->send(
        get_option('ms_welcome_user_notification'),
        array('address' => $user_email),
        array(
            'email_data' => array(
                    'default_message' => $content,
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
?>