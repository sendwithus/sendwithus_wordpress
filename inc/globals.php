<?php
/*
 * GLOBAL VARIABLES
 */

$GLOBALS['wp_notifications'] = array(
    'new_user'                       => array(
        'event'       => 'New User Created',
        'description' => 'Activated when a new user is created by an external user.',
        'display_parameters' => '<input type="button" class="parameters_button" id="new_user" name="display_parameters" value="Display parameters"',
        'parameters'  => '   
            <ul>
                <li><strong>user_login</strong>  - Numeric ID of the user.</li>
                <li><strong>password</strong> - User\'s plaintext password.</li>
                <li><strong>first_name</strong> - First name of the new user.</li>
                <li><strong>last_name</strong> - Last name of the new user.</li>
                <li><strong>caps</strong> - Individual capabilities the user has been given.</li>
                <li><strong>blogname</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
    ),
    'new_comment'                    => array(
        'event'       => 'New Comment Posted',
        'description' => 'Activated when a new comment is posted by a user. Email is sent to administrator of the blog.',
        'display_parameters' => '<input type="button" class="parameters_button" id="new_comment" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>comment_ID</strong> - Numeric ID of the comment.</li>
                <li><strong>comment_post_ID</strong> - Numeric ID of the post.</li>
                <li><strong>comment_author</strong> - Comment author\'s name.</li>
                <li><strong>comment_author_email</strong> - Comment author\'s email.</li>
                <li><strong>comment_author_url</strong> - Comment author\'s URL if provided.</li>
                <li><strong>comment_author_IP</strong> - Comment author\'s IP address.</li>
                <li><strong>comment_date</strong> - Date the comment was posted.</li>
                <li><strong>comment_date_gmt</strong> - GMT date the comment was posted.</li>
                <li><strong>comment_content</strong> - Content of the comment.</li>
                <li><strong>comment_karma</strong> - Numerical karma given to the comment.</li>
                <li><strong>comment_approved</strong> - Returns a 1 for approved, 0 for not approved.</li>
                <li><strong>comment_agent</strong> - Comment\'s agent information (browser, Operating System, etc.).</li>
                <li><strong>comment_type</strong> - Commment\'s type if meaningful (pingback|trackback), and empty for normal comments.</li>
                <li><strong>comment_parent</strong> - Parent comment\'s numerical ID.</li>
                <li><strong>user_id</strong> - Numerical user ID of the comment poster</li>
                <li><strong>blogname</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
    ),
    'awaiting_approval'              => array(
        'event'       => 'User Comment Awaiting Approval',
        'description' => 'Activated when comment must be manually approved is set and a comment is posted.',
        'display_parameters' => '<input type="button" class="parameters_button" id="awaiting_approval" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>comment_ID</strong> - Numeric ID of the comment.</li>
                <li><strong>comment_post_ID</strong> - Numeric ID of the post.</li>
                <li><strong>comment_author</strong> - Comment author\'s name.</li>
                <li><strong>comment_author_email</strong> - Comment author\'s email.</li>
                <li><strong>comment_author_url</strong> - Comment author\'s url if provided.</li>
                <li><strong>comment_author_IP</strong> - Comment author\'s IP address.</li>
                <li><strong>comment_date</strong> - Date the comment was posted.</li>
                <li><strong>comment_date_gmt</strong> - GMT date the comment was posted.</li>
                <li><strong>comment_content</strong> - Content of the comment.</li>
                <li><strong>comment_karma</strong> - Numerical karma given to the comment.</li>
                <li><strong>comment_approved</strong> - Returns a 1 for approved, 0 for not approved.</li>
                <li><strong>comment_agent</strong> - Comment\'s agent information (browser, Operating System, etc.).</li>
                <li><strong>comment_type</strong> - Comment\'s type if meaningful (pingback|trackback), and empty for normal comments.</li>
                <li><strong>comment_parent</strong> - Parent comment\'s numerical ID.</li>
                <li><strong>user_id</strong> - Numerical user ID of the commenter.</li>
                <li><strong>blogname</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
    ),
    'password_change_notification'   => array(
        'event'       => 'Password Change Requested (Notify Admin)',
        'description' => 'Activated when a user attempts to change their password via "Lost your password?", notifies the site admin.',
        'display_parameters' => '<input type="button" class="parameters_button" id="password_change_notification" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user_login</strong> - User login name.</li>
                <li><strong>user_pass</strong> - User\'s plaintext password.</li>
                <li><strong>user_nicename</strong> - User\'s nicename.</li>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>user_url</strong> - User\'s URL, if provided.</li>
                <li><strong>user_registered</strong> - Date the user registered.</li>
                <li><strong>user_activation_key</strong> - URL allowing the user to reset their password.</li>
                <li><strong>user_status</strong> - What parameter contains.</li>
                <li><strong>display_name</strong> - What parameter contains.</li>
                <li><strong>spam</strong> - What parameter contains.</li>
                <li><strong>deleted</strong> - What parameter contains.</li>
                <li><strong>blogname</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
    ),
    'password_reset'                 => array(
        'event'       => 'Password Reset Requested (Notify User)',
        'description' => 'Activated when a user attempts to change their password via "Lost your password?", notifies the user.',
        'display_parameters' => '<input type="button" class="parameters_button" id="password_reset" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user_login</strong> - User\'s login name.</li>
                <li><strong>reset_url</strong> - URL allowing the user to reset their password.</li>
                <li><strong>user_nicename</strong> - User\'s nicename.</li>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>blogname</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
    )
);

$GLOBALS['wp_ms_notifications'] = array(
    'ms_new_user_network_admin'    => array(
        'event'       => 'New User Notification - Notify Network Admin',
        'description' => 'Activates when a new user signs up for the site, notifies the site admin.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_new_user_network_admin" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user</strong> - User name.</li>
                <li><strong>site_url</strong> - WordPress site URL.</li>
                <li><strong>remote_ip</strong> - What parameter contains.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'        
    ),
    'ms_new_blog_network_admin'    => array(
        'event'       => 'New Blog Notification - Notify Network Admin',
        'description' => 'Activates when a new blog is created on the site, notifies the site admin.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_new_blog_network_admin" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>site_name</strong> - New blog site name.</li>
                <li><strong>site_url</strong> - New blog site url.</li>
                <li><strong>remote_ip</strong> - What parameter contains.</li>
                <li><strong>disable_notifications</strong> - Returns a url to disable this type of notification.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'        
    ),
    'ms_new_user_success'          => array(
        'event'       => 'New User Success - Notify User',
        'description' => 'Activated when a new user signs up for the site, notifies the user.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_new_user_success" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>domain</strong> - What parameter contains.</li>
                <li><strong>path</strong> - What parameter contains.</li>
                <li><strong>user</strong> - User\'s login name.</li>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>key</strong> - What parameter contains.</li>
                <li><strong>content</strong> - What parameter contains.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'        
    ),
//    'ms_new_blog_success'          => array(
//        'event'       => 'New Blog Success - Notify User',
//        'description' => 'Placeholder description.',
//        'display_parameters' => '<input type="button" class="parameters_button" id="ms_new_blog_success" name="display_parameters" value="Display parameters"'.
//         
//        .'/>',
//        'parameters'  => '
//            <ul>
//                We dont have this function
//            </ul>'
//    ),
    'ms_welcome_user_notification' => array(
        'event'       => 'New User Welcome - Notify User',
        'description' => 'Activates when a new user creation is successful.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_welcome_user_notification" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user_login</strong> - User\'s login name.</li>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>user_registered_date</strong> - Date user was created.</li>
                <li><strong>user_activation_key</strong> - URL for the user to activate their account.</li>
                <li><strong>blogname</strong> - Name of the blog.</li>
                <li><strong>blog_url</strong> - URL of the blog.</li>
                <li><strong>meta</strong> - Returns a blank array</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'        
    ),
    'ms_welcome_notification'      => array(
        'event'       => 'New Blog Welcome - Notify User',
        'description' => 'Activates when a blog creation is successful.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_welcome_notification" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>first_name</strong> - User\'s first name.</li>
                <li><strong>last_name</strong> - User\'s last name.</li>
                <li><strong>password</strong> - User\'s plaintext password.</li>
                <li><strong>admin_email</strong> - Admin\'s email address.</li>
                <li><strong>site_name</strong> - New blog\'s site name.</li>
                <li><strong>site_url</strong> - New blog\'s site URL.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'        
    )
);

?>