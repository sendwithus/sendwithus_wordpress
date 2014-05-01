<?php
/*
 * GLOBAL VARIABLES
 */

$GLOBALS['wp_notifications'] = array(
    'new_user'                       => array(
        'event'       => 'New User Created',
        'description' => 
            'Activated when a new user is created by an external user.<br/>
            Passes the following parameters to sendwithus:<br/>
            <li>
                <ul>Example1</ul>
                <ul>Example2</ul>
            </li>'
    ),
    'new_comment'                    => array(
        'event'       => 'New Comment Posted',
        'description' =>
            'Activated when a new comment is posted by a user. Email is sent to
            administrator of the blog.<br/>
            <li>
                <ul>Example1</ul>
                <ul>Example2</ul>
            </li>'
    ),
    'awaiting_approval'              => array(
        'event'       => 'User Comment Awaiting Approval',
        'description' =>
            'Placeholder description.'
    ),
    'password_change_notification'   => array(
        'event'       => 'Password Change Requested (Notify Admin)',
        'description' =>
            'Placeholder description.'
    ),
    'password_reset'                 => array(
        'event'       => 'Password Reset Requested (Notify User)',
        'description' => 
            'Placeholder description.'
    )
);

$GLOBALS['wp_ms_notifications'] = array(
    'ms_new_user_network_admin'    => array(
        'event'       => 'New User Notification - Notify Network Admin',
        'description' => 
            'Placeholder description.'
    ),
    'ms_new_blog_network_admin'    => array(
        'event'       => 'New Blog Notification - Notify Network Admin',
        'description' => 
            'Placeholder description.'
    ),
    'ms_new_user_success'          => array(
        'event'       => 'New User Success - Notify User',
        'description' =>
            'Placeholder description.'
    ),
    'ms_new_blog_success'          => array(
        'event'       => 'New Blog Success - Notify User',
        'description' =>
            'Placeholder description.'
    ),
    'ms_welcome_user_notification' => array(
        'event'       => 'New User Welcome - Notify User',
        'description' =>
            'Placeholder description.'
    ),
    'ms_welcome_notification'      => array(
        'event'       => 'New Blog Welcome - Notify User',
        'description' =>
            'Placeholder description.'
    )
);

?>