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