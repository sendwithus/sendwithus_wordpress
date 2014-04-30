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

add_filter("wpmu_welcome_user_notification", "swu_wpmu_welcome_user_notification", 10, 3);
function swu_wpmu_welcome_user_notification( $user_id, $password, $meta ){
    $api = new \sendwithus\API($GLOBALS['api_key']);

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

    $response = $api->send(
        get_option('ms_welcome_user_notification'),
        array('address' => $user->user_email),
        array(
            'email_data' => array(
                'user_email' => $user->user_email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'password' => $password,
                'admin_email' => $admin_email,
                'site_name' => $current_site->site_name,
                'default_message' => $default_message
            )
        )
    );

    return false;
}

add_filter("wpmu_welcome_notification", "swu_wpmu_welcome_notification", 10, 5);
function swu_wpmu_welcome_notification($blog_id, $user_id, $password, $title, $meta ) {
    $api = new \sendwithus\API($GLOBALS['api_key']);
    $current_site = get_current_site();
    $url = get_blogaddress_by_id($blog_id);
    $user = get_userdata( $user_id );

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

    $default_message = str_replace( 'SITE_NAME', $current_site->site_name, $default_message );
    $default_message = str_replace( 'BLOG_TITLE', $title, $default_message );
    $default_message = str_replace( 'BLOG_URL', $url, $default_message );
    $default_message = str_replace( 'USERNAME', $user->user_login, $default_message );
    $default_message = str_replace( 'PASSWORD', $password, $default_message );


    $response = $api->send(
        get_option('ms_welcome_notification'),
        array('address' => $user->user_email),
        array(
            'email_data' => array(
                'user_email' => $user->user_email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'password' => $password,
                'admin_email' => $admin_email,
                'site_name' => $current_site->site_name,
                'site_url' => $url,
                'default_message' => htmlDefaultMessage($default_message),
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
