<?php
/*
 * GLOBAL VARIABLES
 */

$GLOBALS['wp_notifications'] = array(
    'new_user'                       => array(
        'event'       => 'New User Created',
        'description' => 'Activated when a new user is created by an external user.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_new_user" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <br />
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '   
            <ul>
                <li>user_login - Returns the numeric ID of the user.</li>
                <li>password - Returns the user\'s plaintext password.</li>
                <li>first_name - Returns the first name of the new user.</li>
                <li>last_name - Returns the last name of the new user.</li>
                <li>caps - Returns the individual capabilities the user has been given.</li>
                <li>blogname - Returns the name of the blog.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'
    ),
    'new_comment'                    => array(
        'event'       => 'New Comment Posted',
        'description' => 'Activated when a new comment is posted by a user. Email is sent to administrator of the blog.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_new_comment" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>comment_ID - Returns the numeric ID of the comment.</li>
                <li>comment_post_ID - Returns the numberic ID of the post.</li>
                <li>comment_author - Returns the comment author\'s name.</li>
                <li>comment_author_email - Returns the comment author\'s email.</li>
                <li>comment_author_url - Returns the comment author\'s url if provided.</li>
                <li>comment_author_IP - Returns the comment author\'s IP address.</li>
                <li>comment_date - Returns the date the comment was posted.</li>
                <li>comment_date_gmt - Returns the gmt date the comment was posted.</li>
                <li>comment_content - Returns the content of the comment.</li>
                <li>comment_karma - Returns the numerical karma given to the comment.</li>
                <li>comment_approved - Returns a 1 for approved, 0 for not approved.</li>
                <li>comment_agent - Returns The comment\'s agent (browser, Operating System, etc.).</li>
                <li>comment_type - Returns The comment\'s type if meaningfull (pingback|trackback), and empty for normal comments.</li>
                <li>comment_parent - Returns the parent comment\'s numerical ID.</li>
                <li>user_id - Returns the numerical user ID of the comment poster</li>
                <li>blogname - Returns the name of the blog.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'
    ),
    'awaiting_approval'              => array(
        'event'       => 'User Comment Awaiting Approval',
        'description' => 'Activated when comment must be manually approved is set and a comment is posted.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_awaiting_approval" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>comment_ID - Returns the numeric ID of the comment.</li>
                <li>comment_post_ID - Returns the numberic ID of the post.</li>
                <li>comment_author - Returns the comment author\'s name.</li>
                <li>comment_author_email - Returns the comment author\'s email.</li>
                <li>comment_author_url - Returns the comment author\'s url if provided.</li>
                <li>comment_author_IP - Returns the comment author\'s IP address.</li>
                <li>comment_date - Returns the date the comment was posted.</li>
                <li>comment_date_gmt - Returns the gmt date the comment was posted.</li>
                <li>comment_content - Returns the content of the comment.</li>
                <li>comment_karma - Returns the numerical karma given to the comment.</li>
                <li>comment_approved - Returns a 1 for approved, 0 for not approved.</li>
                <li>comment_agent - Returns The comment\'s agent (browser, Operating System, etc.).</li>
                <li>comment_type - Returns The comment\'s type if meaningfull (pingback|trackback), and empty for normal comments.</li>
                <li>comment_parent - Returns the parent comment\'s numerical ID.</li>
                <li>user_id - Returns the numerical user ID of the comment poster</li>
                <li>blogname - Returns the name of the blog.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'
    ),
    'password_change_notification'   => array(
        'event'       => 'Password Change Requested (Notify Admin)',
        'description' => 'Activated when a user attempts to change their password via "Lost your password?", notifies the site admin.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_password_change_notification" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>user_login - Returns the user login name.</li>
                <li>user_pass - Returns the user\'s plaintext password.</li>
                <li>user_nicename - Returns the user\'s nicename.</li>
                <li>user_email - Returns the user\'s email address.</li>
                <li>user_url - Returns the user\'s url if provided.</li>
                <li>user_registered - Returns the date the user was registered.</li>
                <li>user_activation_key - Returns the url for the user to change their password.</li>
                <li>user_status - What parameter contains.</li>
                <li>display_name - What parameter contains.</li>
                <li>spam - What parameter contains.</li>
                <li>deleted - What parameter contains.</li>
                <li>blogname - Returns the name of the blog.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'
    ),
    'password_reset'                 => array(
        'event'       => 'Password Reset Requested (Notify User)',
        'description' => 'Activated when a user attempts to change their password via "Lost your password?", notifies the user.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_password_reset" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>user_login - Returns the user login name.</li>
                <li>reset_url - Returns the url for the user to reset their password.</li>
                <li>user_nicename - Returns the user nicename.</li>
                <li>user_email - Returns the user\'s email address.</li>
                <li>blogname - Returns the name of the blog.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'
    )
);

$GLOBALS['wp_ms_notifications'] = array(
    'ms_new_user_network_admin'    => array(
        'event'       => 'New User Notification - Notify Network Admin',
        'description' => 'Activates when a new user signs up for the site, notifies the site admin.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_ms_new_user_network_admin" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>user - Returns the user name.</li>
                <li>site_url - Returns the wordpress site url.</li>
                <li>remote_ip - What parameter contains.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'        
    ),
    'ms_new_blog_network_admin'    => array(
        'event'       => 'New Blog Notification - Notify Network Admin',
        'description' => 'Activates when a new blog is created on the site, notifies the site admin.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_ms_new_blog_network_admin" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>site_name - Returns the wordpress site name.</li>
                <li>site_url - Returns the wordpress site url.</li>
                <li>remote_ip - What parameter contains.</li>
                <li>disable_notifications - Returns a url to disable this type of notification.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'        
    ),
    'ms_new_user_success'          => array(
        'event'       => 'New User Success - Notify User',
        'description' => 'Activated when a new user signs up for the site, notifies the user.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_ms_new_user_success" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>domain - What parameter contains.</li>
                <li>path - What parameter contains.</li>
                <li>user - returns the user login name.</li>
                <li>user_email - Returns the user\'s email address.</li>
                <li>key - What parameter contains.</li>
                <li>content - What parameter contains.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'        
    ),
    'ms_new_blog_success'          => array(
        'event'       => 'New Blog Success - Notify User',
        'description' => 'Placeholder description.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_ms_new_blog_success" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                We dont have this function
            </ul>'        
    ),
    'ms_welcome_user_notification' => array(
        'event'       => 'New User Welcome - Notify User',
        'description' => 'Activates when a new user creation is successful.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_ms_welcome_user_notification" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>user_login - Returns the user login name.</li>
                <li>user_email - Returns the user\'s email address.</li>
                <li>user_registered_date - Returns the date the user was created.</li>
                <li>user_activation_key - Returns the url for the user to activate their account.</li>
                <li>blogname - Returns the name of the blog.</li>
                <li>blog_url - Returns the url of the blog.</li>
                <li>meta - Returns a blank array</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'        
    ),
    'ms_welcome_notification'      => array(
        'event'       => 'New Blog Welcome - Notify User',
        'description' => 'Activates when a blog creation is successful.',
        'display_parameters' => '<input type="checkbox" id="display_parameters_ms_welcome_notification" name="display_parameters" value="display_parameters"'.
         checked("display_parameters", get_option("display_parameters"))
        .'/>
        <strong>Display descriptions of parameters sent to sendwithus</strong>',
        'parameters'  => '
            <ul>
                <li>user_email - Returns the user\'s email address.</li>
                <li>first_name - Returns the user\'s first name.</li>
                <li>last_name - Returns the user\'s last name.</li>
                <li>password - Returns the user\'s plaintext password.</li>
                <li>admin_email - Returns the admin\'s email address.</li>
                <li>site_name - Returns the wordpress site name.</li>
                <li>site_url - Returns the wordpress site url.</li>
                <li>default_message - Returns the default wordpress email content.</li>
            </ul>'        
    )
);

?>