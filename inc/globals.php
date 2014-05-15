<?php
/*
 * GLOBAL VARIABLES
 */

$GLOBALS['wp_notifications'] = array(
    'new_user'                       => array(
        'event'       => 'New User Created',
        'description' => 'Activated when a new user signs up for an account. Sent to administrator.',
        'display_parameters' => '<input type="button" class="parameters_button" id="new_user" name="display_parameters" value="Display parameters"',
        'parameters'  => '   
            <ul>
                <li><strong>user_login</strong>  - User\'s numeric ID.</li>
                <li><strong>user_password</strong> - User\'s plaintext password.</li>
                <li><strong>caps</strong> - Capabilities the user has been given.</li>
                <li><strong>blog_name</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
        /*
        default_message:

        New user registration on your site WordPress Test: 
        Username: atestuser 
        E-mail: dcqnsht+md1qlo@sharklasers.com 
        Password: zte2OeyWsl6M 
        http://localhost/~kyle/wordpress/wp-login.php
        */
    ),
    'new_comment'                    => array(
        'event'       => 'New Comment Posted',
        'description' => 'Activated when a user posts a comment. Sent to administrator.',
        'display_parameters' => '<input type="button" class="parameters_button" id="new_comment" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>comment_ID</strong> - Comment\'s numeric ID.</li>
                <li><strong>comment_post_ID</strong> - Post\'s numeric ID.</li>
                <li><strong>comment_author</strong> - Comment author\'s name.</li>
                <li><strong>comment_author_email</strong> - Comment author\'s email.</li>
                <li><strong>comment_author_IP</strong> - Comment author\'s IP address.</li>
                <li><strong>comment_date</strong> - Date the comment was posted.</li>
                <li><strong>comment_date_gmt</strong> - GMT date the comment was posted.</li>
                <li><strong>comment_content</strong> - Content of the comment.</li>
                <li><strong>comment_karma</strong> - Numerical karma given to the comment.</li>
                <li><strong>comment_agent</strong> - Comment\'s agent information (Browser, Operating System, etc.).</li>
                <li><strong>comment_type</strong> - Commment\'s type if meaningful (pingback / trackback) or \'empty\' for normal comments.</li>
                <li><strong>comment_parent</strong> - Parent comment\'s numerical ID.</li>
                <li><strong>user_id</strong> - Numerical user ID of the commenter.</li>
                <li><strong>blog_name</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
        /*
        default_message:

        New comment on your post "Hello world!" 
        Author : 123qwe (IP: 127.0.0.1 , ) 
        E-mail : ct5ru7h+l4rqho@sharklasers.com 
        URL : 
        Whois : http://whois.arin.net/rest/ip/127.0.0.1 
        Comment: 
        Another new one. 

        You can see all comments on this post here: 
        http://localhost.com/2014/05/01/hello-world/#comments 

        Permalink: http://localhost.com/2014/05/01/hello-world/#comment-4 
        Trash it: http://localhost.com/wp-admin/comment.php?action=trash&c=4 
        Spam it: http://localhost.com/wp-admin/comment.php?action=spam&c=4 
        */
    ),
    'awaiting_approval'              => array(
        'event'       => 'User Comment Awaiting Approval',
        'description' => 'Activated when \'comment must be manually approved\' is set in control panel and a comment is posted. Sent to administrator.',
        'display_parameters' => '<input type="button" class="parameters_button" id="awaiting_approval" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>comment_ID</strong> - Comment\'s numeric ID.</li>
                <li><strong>comment_post_ID</strong> - Post\'s numeric ID.</li>
                <li><strong>comment_author</strong> - Comment author\'s name.</li>
                <li><strong>comment_author_email</strong> - Comment author\'s email.</li>
                <li><strong>comment_author_IP</strong> - Comment author\'s IP address.</li>
                <li><strong>comment_date</strong> - Date the comment was posted.</li>
                <li><strong>comment_date_gmt</strong> - GMT date the comment was posted.</li>
                <li><strong>comment_content</strong> - Content of the comment.</li>
                <li><strong>comment_karma</strong> - Numerical karma given to the comment.</li>
                <li><strong>comment_agent</strong> - Comment\'s agent information (Browser, Operating System, etc.).</li>
                <li><strong>comment_type</strong> - Commment\'s type if meaningful (pingback / trackback) or \'empty\' for normal comments.</li>
                <li><strong>comment_parent</strong> - Parent comment\'s numerical ID.</li>
                <li><strong>user_id</strong> - Numerical user ID of the commenter.</li>
                <li><strong>blog_name</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
        /*
        default_message:

        A new comment on the post "Hello world!" is waiting for your approval 
        http://localhost.com/2014/05/01/hello-world/ 

        Author : 123qwe (IP: 127.0.0.1 , ) 
        E-mail : ct5ru7h+l4rqho@sharklasers.com 
        URL : 
        Whois : http://whois.arin.net/rest/ip/127.0.0.1 
        Comment: 
        Another test 

        Approve it: http://localhost.com/wp-admin/comment.php?action=approve&c=3 
        Trash it: http://localhost.com/wp-admin/comment.php?action=trash&c=3 
        Spam it: http://localhost.com/wp-admin/comment.php?action=spam&c=3 
        Currently 0 comments are waiting for approval. Please visit the moderation panel: 
        http://localhost.com/wp-admin/edit-comments.php?comment_status=moderated 
        */
    ),
    'password_change_notification'   => array(
        'event'       => 'Password Change Performed (Notify Admin)',
        'description' => 'Activated when a user changes their password via a \'Lost your password?\' email. Sent to administrator.',
        'display_parameters' => '<input type="button" class="parameters_button" id="password_change_notification" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user_login</strong> - User\'s login name.</li>
                <li><strong>display_name</strong> - How the user\'s name is displayed on the site.</li>
                <li><strong>user_nicename</strong> - User\'s nicename.</li>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>user_password</strong> - User\'s hashed password.</li>
                <li><strong>user_url</strong> - User\'s URL, if provided.</li>
                <li><strong>user_registered</strong> - Date the user registered.</li>
                <li><strong>blog_name</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
        /*
        default_message:

        Password Lost and Changed for user: kyle 
        */
    ),
    'password_reset'                 => array(
        'event'       => 'Password Reset Requested (Notify User)',
        'description' => 'Activated when a user attempts to change their password via "Lost your password?" Sent to requesting user.',
        'display_parameters' => '<input type="button" class="parameters_button" id="password_reset" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user_login</strong> - User\'s login name.</li>
                <li><strong>user_nicename</strong> - User\'s nicename.</li>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>reset_url</strong> - URL allowing the user to reset their password.</li>
                <li><strong>blog_name</strong> - Name of the blog.</li>
                <li><strong>default_message</strong> - Default WordPress email content.</li>
            </ul>'
        /*
        default_message:

        Someone requested that the password be reset for the following account: 

        http://localhost.com/ 

        Username: kyle 

        If this was a mistake, just ignore this email and nothing will happen. 

        To reset your password, visit the following address: 


        http://localhost.com/wp-login.php?action=rp&key=sDAGd13qVt0lvK8yNBCr&login=kyle
        */
    )
);

$GLOBALS['wp_ms_notifications'] = array(
    'ms_new_user_network_admin'    => array(
        'event'       => 'New User Notification - Notify Network Admin',
        'description' => 'Activates when a new user signs up for the site, notifies the site admin.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_new_user_network_admin" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user_name</strong> - User name.</li>
                <li><strong>remote_ip</strong> - IP address of new user.</li>
                <li><strong>control_panel</strong> - WordPress control panel URL.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'  
        /*
        default_message:

        New User: testuserforsite
        Remote IP: 127.0.0.1

        Disable these notifications: http://localhost.com/wp-admin/network/settings.php 
        */      
    ),
    'ms_new_blog_network_admin'    => array(
        'event'       => 'New Blog Notification - Notify Network Admin',
        'description' => 'Activates when a new blog is created on the site, notifies the site admin.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_new_blog_network_admin" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>site_name</strong> - New blog\'s name.</li>
                <li><strong>site_url</strong> - New blog\'s URL.</li>
                <li><strong>remote_ip</strong> - IP of blog registrant (?).</li>
                <li><strong>control_panel</strong> -  WordPress control panel URL.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'       
        /*
        default_message:

        New Site: testuserforsite
        URL: http://testuserforsite.localhost.com
        Remote IP: 127.0.0.1

        Disable these notifications: http://localhost.com/wp-admin/network/settings.php         
        */ 
    ),
    'ms_new_user_success'          => array(
        'event'       => 'New User Success - Notify User',
        'description' => 'Activated when a new user signs up for the site, notifies the user.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_new_user_success" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>domain</strong> - Domain name of new site.</li>
                <li><strong>path</strong> - Path name of new site.</li>
                <li><strong>key</strong> - Unique key to activate new site. Passed as paramter (ex. /wp-activate.php?key=9ef0f34833088971)</li>
                <li><strong>user_name</strong> - User\'s login name.</li>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>content</strong> - Unformatted version of default_message, with no domain/path or links.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'        
        /*
        default_message: 

        To activate your blog, please click the following link:
        http://testuserforsite.localhost.com/wp-activate.php?key=9ef0f34833088971
        After you activate, you will receive *another email* with your login.
        After you activate, you can visit your site here:
        http://testuserforsite.localhost.com/ 
        */
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
                <li><strong>user_activation_key</strong> - Complete URL for the user to activate their account.</li>
                <li><strong>blog_name</strong> - Name of the blog the user signed up for.</li>
                <li><strong>blog_url</strong> - URL of the blog the user signed up for.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'

        /*
        default_message:

        To activate your user, please click the following link: 
        http://localhost.com/wp-activate.php?key=538bc64f7637476c 
        After you activate, you will receive *another email* with your login.
        */        
    ),
    'ms_welcome_notification'      => array(
        'event'       => 'New Blog Welcome - Notify User',
        'description' => 'Activates when a blog creation is successful.',
        'display_parameters' => '<input type="button" class="parameters_button" id="ms_welcome_notification" name="display_parameters" value="Display parameters"',
        'parameters'  => '
            <ul>
                <li><strong>user_email</strong> - User\'s email address.</li>
                <li><strong>user_password</strong> - User\'s plaintext password.</li>
                <li><strong>admin_email</strong> - Admin\'s email address.</li>
                <li><strong>site_name</strong> - New blog\'s name.</li>
                <li><strong>site_url</strong> - New blog\'s URL.</li>
                <li><strong>default_message</strong> - The default WordPress email content.</li>
            </ul>'
        /*
        Dear User, 

        Your new WordPress Test Sites site has been successfully set up at: 
        http://testuserforsite.localhost.com/ 

        You can log in to the administrator account with the following information: 
        Username: testuserforsite 
        Password: 8YuSjVhVqj3D 
        Log in here: http://testuserforsite.localhost.com/wp-login.php 

        We hope you enjoy your new site. Thanks! 

        --The Team @ WordPress Test Sites    
        */
    )
);

?>