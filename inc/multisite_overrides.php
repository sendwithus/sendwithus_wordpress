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
            )
        )
    );

    return false;
}