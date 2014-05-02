<?php
/*
 * GLOBAL VARIABLES
 */

$GLOBALS['wp_notifications'] = array(
    'new_user'                       => array(
        'event'       => 'New User Created',
        'description' => 'Activated when a new user is created by an external user.',
        'parameters'  => '   
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'
    ),
    'new_comment'                    => array(
        'event'       => 'New Comment Posted',
        'description' => 'Activated when a new comment is posted by a user. Email is sent to administrator of the blog.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'
    ),
    'awaiting_approval'              => array(
        'event'       => 'User Comment Awaiting Approval',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'
    ),
    'password_change_notification'   => array(
        'event'       => 'Password Change Requested (Notify Admin)',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'
    ),
    'password_reset'                 => array(
        'event'       => 'Password Reset Requested (Notify User)',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'
    )
);

$GLOBALS['wp_ms_notifications'] = array(
    'ms_new_user_network_admin'    => array(
        'event'       => 'New User Notification - Notify Network Admin',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'        
    ),
    'ms_new_blog_network_admin'    => array(
        'event'       => 'New Blog Notification - Notify Network Admin',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'        
    ),
    'ms_new_user_success'          => array(
        'event'       => 'New User Success - Notify User',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'        
    ),
    'ms_new_blog_success'          => array(
        'event'       => 'New Blog Success - Notify User',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'        
    ),
    'ms_welcome_user_notification' => array(
        'event'       => 'New User Welcome - Notify User',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'        
    ),
    'ms_welcome_notification'      => array(
        'event'       => 'New Blog Welcome - Notify User',
        'description' => 'Placeholder description.',
        'parameters'  => '
            <ul>
                <li>Example1 - What parameter contains.</li>
                <li>Example2 - What parameter contains.</li>
            </ul>'        
    )
);

?>