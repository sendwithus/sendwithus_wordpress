<?php
/*
 * FUNCTION OVERRIDE BASED OVERRIDES
 */

// Replace new comment alert with sendwithus
if (!function_exists('wp_notify_postauthor')) {
    function wp_notify_postauthor($comment_id) {
        $api = new \sendwithus\API($GLOBALS['api_key']);

        $comment = get_comment($comment_id);
        $post    = get_post( $comment->comment_post_ID );
        $author  = get_userdata( $post->post_author );

        /* Begin code to generate 'default_message' */
        switch ( $comment->comment_type ) {
            case 'trackback':
                $default_message  = sprintf( __( 'New trackback on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: website name, 2: author IP, 3: author domain */
                $default_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $default_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $default_message .= __('Excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                $default_message .= __('You can see all trackbacks on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
                break;
            case 'pingback':
                $default_message  = sprintf( __( 'New pingback on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: comment author, 2: author IP, 3: author domain */
                $default_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $default_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $default_message .= __('Excerpt: ') . "\r\n" . sprintf('[...] %s [...]', $comment->comment_content ) . "\r\n\r\n";
                $default_message .= __('You can see all pingbacks on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
                break;
            default: // Comments
                $default_message  = sprintf( __( 'New comment on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: comment author, 2: author IP, 3: author domain */
                $default_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $default_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
                $default_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $default_message .= sprintf( __('Whois  : http://whois.arin.net/rest/ip/%s'), $comment->comment_author_IP ) . "\r\n";
                $default_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                $default_message .= __('You can see all comments on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
                break;
        }
        $default_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
        $default_message .= sprintf( __('Permalink: %s'), get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment_id ) . "\r\n";

        if ( user_can( $post->post_author, 'edit_comment', $comment_id ) ) {
            if ( EMPTY_TRASH_DAYS )
                $default_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
            else
                $default_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
            $default_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";
        }
        /* End code to generate 'default_message' */

        //Subject line for default wordpress email
        $default_email_subject = "New comment posted at ".get_option('blogname');

        $response = $api->send(
            get_option('new_comment'),
            array('address' => $author->user_email),
            array(
                'email_data' => array(
                    'default_email_subject' => $default_email_subject,
                    'comment_id' => $comment->comment_ID,
                    'comment_post_id' => $comment->comment_post_ID,
                    'comment_author' => $comment->comment_author,
                    'comment_author_email' => $comment->comment_author_email,
                    'comment_author_ip_address' => $comment->comment_author_IP,
                    'comment_date' => $comment->comment_date,
                    'comment_date_gmt' => $comment->comment_date_gmt,
                    'comment_content' => $comment->comment_content,
                    'comment_karma' => $comment->comment_karma,
                    'comment_agent' => $comment->comment_agent,
                    'comment_type' => $comment->comment_type,
                    'comment_parent' => $comment->comment_parent,
                    'user_id' => $comment->user_id,
                    'blog_name' => get_option('blogname'),
                    'default_message'=> html_default_message($default_message)
                )
            )
        );
    }
}

// Replace new user email
if (!function_exists('wp_new_user_notification')) {
    function wp_new_user_notification($user_id, $plaintext_pass = "") {
	    // Stops the user from receiving a redundant email in multisite mode
	    if(is_network_admin()){
		    return;
	    }
        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);
        $api = new \sendwithus\API($GLOBALS['api_key']);

        $user = get_userdata( $user_id );

        // Below is used to create 'default_message'
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $default_message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n";
        $default_message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n";
        $default_message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";
        $default_message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
        $default_message .= wp_login_url() . "\r\n";

        //Subject line for default wordpress email
        $default_email_subject = "New user created at ".get_option('blogname');

        $response = $api->send(
            get_option('new_user'),
            array('address' => $user_email),
            array(
                'email_data' => array(
                    'default_email_subject' => $default_email_subject,
                    'user_login' => $user_login,
                    'user_password' => $plaintext_pass,
                    'caps' => $user->caps,
                    'blog_name' => get_option('blogname'),
                    'default_message' => html_default_message($default_message)
                )
            )
        );
    }
}

// Use swu to send comments awaiting moderation to all moderators
if (!function_exists('wp_notify_moderator')) {
    function wp_notify_moderator($comment_id) {
        if ( 0 == get_option( 'moderation_notify' ) )
            return true;

        $comment = get_comment($comment_id);
        $post = get_post($comment->comment_post_ID);
        $user = get_userdata( $post->post_author );

        // Send to the administration and to the post author if the author can modify the comment.
        $emails = array( get_option( 'admin_email' ) );
        if ( user_can( $user->ID, 'edit_comment', $comment_id ) && ! empty( $user->user_email ) ) {
            if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) )
                $emails[] = $user->user_email;
        }

        /**
         * Filter the list of recipients for comment moderation emails.
         *
         * @since 3.7.0
         *
         * @param array $emails     List of email addresses to notify for comment moderation.
         * @param int   $comment_id Comment ID.
         */
        $emails = apply_filters( 'comment_moderation_recipients', $emails, $comment_id );

        /* Begin code to generate 'default_message' */
        switch ( $comment->comment_type ) {
            case 'trackback':
                $default_message  = sprintf( __('A new trackback on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $default_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $default_message .= sprintf( __('Website : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $default_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $default_message .= __('Trackback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
            case 'pingback':
                $default_message  = sprintf( __('A new pingback on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $default_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $default_message .= sprintf( __('Website : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $default_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $default_message .= __('Pingback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
            default: // Comments
                $default_message  = sprintf( __('A new comment on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $default_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $default_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $default_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
                $default_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $default_message .= sprintf( __('Whois  : http://whois.arin.net/rest/ip/%s'), $comment->comment_author_IP ) . "\r\n";
                $default_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
        }

        $default_message .= sprintf( __('Approve it: %s'),  admin_url("comment.php?action=approve&c=$comment_id") ) . "\r\n";
        if ( EMPTY_TRASH_DAYS )
            $default_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
        else
            $default_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
        $default_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";

        $default_message .= sprintf( _n('Currently %s comment is waiting for approval. Please visit the moderation panel:',
                'Currently %s comments are waiting for approval. Please visit the moderation panel:', $comments_waiting), number_format_i18n($comments_waiting) ) . "\r\n";
        $default_message .= admin_url("edit-comments.php?comment_status=moderated") . "\r\n";
        /* End code to generate 'default_message' */

        //Subject line for default wordpress email
        $default_email_subject = "Comments are waiting approval at ".get_option('blogname');

        $api = new \sendwithus\API($GLOBALS['api_key']);

        foreach ( $emails as $email ) {
            $response = $api->send(
                get_option('awaiting_approval'),
                array('address' => $email),
                array(
                    'email_data' => array(
                        'default_email_subject' => $default_email_subject,
                        'comment_id' => $comment->comment_ID,
                        'comment_post_id' => $comment->comment_post_ID,
                        'comment_author' => $comment->comment_author,
                        'comment_author_email' => $comment->comment_author_email,
                        'comment_author_ip_address' => $comment->comment_author_IP,
                        'comment_date' => $comment->comment_date,
                        'comment_date_gmt' => $comment->comment_date_gmt,
                        'comment_content' => $comment->comment_content,
                        'comment_karma' => $comment->comment_karma,
                        'comment_agent' => $comment->comment_agent,
                        'comment_type' => $comment->comment_type,
                        'comment_parent' => $comment->comment_parent,
                        'user_id' => $comment->user_id,
                        'blog_name' => get_option('blogname'),
                        'default_message' => html_default_message($default_message)
                    )
                )
            );
        }

        return true;
    }
}

if (!function_exists('wp_password_change_notification')) {
    function wp_password_change_notification( $user ) {

        $default_message = sprintf(__('Password Lost and Changed for user: %s'), $user->user_login) . "\r\n";
        $blogname = get_bloginfo('name');
        $api = new \sendwithus\API($GLOBALS['api_key']);
        error_log(print_r($blogname,true));

        //Subject line for default wordpress email
        $default_email_subject = "Password reset request at ".$blogname;
        $response = $api->send(
            get_option('password_change_notification'),
            array('address' => get_option('admin_email')),
            array(
                'email_data' => array(
                    'default_email_subject' => $default_email_subject,
                    'user_login' => $user->user_login,
                    'display_name' => $user->display_name,
                    'user_nicename' => $user->user_nicename,
                    'user_email' => $user->user_email,
                    'user_password' => $user->user_pass,
                    'user_url' => $user->user_url,
                    'user_registered' => $user->user_registered,
                    'blog_name' => $blogname,
                    'default_message' => html_default_message($default_message)
                )
            )
        );
    }
}

// Adds a function to occur when the filter retrieve_password_message is called
add_filter ('retrieve_password_message', 'reset_password_notification', 10, 3 );
function reset_password_notification($content, $key, $user_login_id = NULL) {
    if($user_login_id != NULL){
        $user = get_user_by('login', $user_login_id);
    }
    else{
        //Grabs the information about the user attempting to reset their password
        $input = filter_input( INPUT_POST, 'user_login', FILTER_SANITIZE_STRING );
        if( is_email( $input ) ) {
            $user = get_user_by( 'email', $input );
        } else {
            $user = get_user_by( 'login', sanitize_user( $input ) );
        }

        $user_info = get_userdata($user->ID);
    }

    //Creates a string to hold the end section of the password reset link
    $message = 'wp-login.php?action=rp&key='. $key. '&login='.$user->user_login;
    //Appends the password reset link to the url of the site we want the password to be reset on
    $url = network_site_url($message);
    //Gets the blogname
    $blogname = get_bloginfo('name');

    // Modify the message content to include the URL
    $content .= $url;

    //Subject line for the default wordpress email
    $default_email_subject = get_option('blogname').' Password Reset';

    //Create a new SWU email with the password reset information
    $api = new \sendwithus\API($GLOBALS['api_key']);

    $response = $api->send(
        get_option('password_reset'),
        array('address' => $user->user_email),
        array(
            'email_data' => array(
                'default_email_subject' => $default_email_subject,
                'user_login' => $user->user_login,
                'user_nicename' => $user->user_nicename,
                'user_email' => $user->user_email,
                'reset_url' => $url,
                'blog_name' => $blogname,
                'default_message' => html_default_message($content)
            )
        )
    );
    error_log(print_r($response, true));


    return false;
}
?>