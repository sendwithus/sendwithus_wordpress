<?php

/*
 * FUNCTION OVERRIDE BASED OVERRIDES
 */

// Replace new comment alert with sendwithus
if (!function_exists('wp_notify_postauthor')) {
    function wp_notify_postauthor($comment_id)
    {
        $api = new \sendwithus\API($GLOBALS['api_key']);

        $comment = get_comment($comment_id);
        $post    = get_post( $comment->comment_post_ID );
        $author  = get_userdata( $post->post_author );

        /* Begin code to generate 'default_message' */
        switch ( $comment->comment_type ) {
            case 'trackback':
                $notify_message  = sprintf( __( 'New trackback on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: website name, 2: author IP, 3: author domain */
                $notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= __('Excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                $notify_message .= __('You can see all trackbacks on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
                break;
            case 'pingback':
                $notify_message  = sprintf( __( 'New pingback on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: comment author, 2: author IP, 3: author domain */
                $notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= __('Excerpt: ') . "\r\n" . sprintf('[...] %s [...]', $comment->comment_content ) . "\r\n\r\n";
                $notify_message .= __('You can see all pingbacks on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
                break;
            default: // Comments
                $notify_message  = sprintf( __( 'New comment on your post "%s"' ), $post->post_title ) . "\r\n";
                /* translators: 1: comment author, 2: author IP, 3: author domain */
                $notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= sprintf( __('Whois  : http://whois.arin.net/rest/ip/%s'), $comment->comment_author_IP ) . "\r\n";
                $notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                $notify_message .= __('You can see all comments on this post here: ') . "\r\n";
                /* translators: 1: blog name, 2: post title */
                $subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
                break;
        }
        $notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
        $notify_message .= sprintf( __('Permalink: %s'), get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment_id ) . "\r\n";

        if ( user_can( $post->post_author, 'edit_comment', $comment_id ) ) {
            if ( EMPTY_TRASH_DAYS )
                $notify_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
            else
                $notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
            $notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";
        }
        /* End code to generate 'default_message' */

        $response = $api->send(
            get_option('new_comment'),
            array('address' => $author->user_email),
            array(
                'email_data' => array(
                    'comment_ID' => $comment->comment_ID,
                    'comment_post_ID' => $comment->comment_post_ID,
                    'comment_author' => $comment->comment_author,
                    'comment_author_email' => $comment->comment_author_,
                    'comment_author_url' => $comment->comment_author_url,
                    'comment_author_IP' => $comment->comment_author_IP,
                    'comment_date' => $comment->comment_date,
                    'comment_date_gmt' => $comment->comment_date_gmt,
                    'comment_content' => $comment->comment_content,
                    'comment_karma' => $comment->comment_karma,
                    'comment_approved' => $comment->comment_approved,
                    'comment_agent' => $comment->comment_agent,
                    'comment_type' => $comment->comment_type,
                    'comment_parent' => $comment->comment_parent,
                    'user_id' => $comment->user_id,
                    'blogname' => get_option('blogname'),
                    'default_message'=> htmlDefaultMessage($notify_message)
                )
            )
        );
    }
}

// Replace new user email
if (!function_exists('wp_new_user_notification')) {
    function wp_new_user_notification($user_id, $plaintext_pass = "")
    {
        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        $api = new \sendwithus\API($GLOBALS['api_key']);

        $user = get_userdata( $user_id );

        // Below is used to create 'default_message'
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n";
        $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n";
        $message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";
        $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
        $message .= wp_login_url() . "\r\n";

        $response = $api->send(
            get_option('new_user'),
            array('address' => $user_email),
            array(
                'email_data' => array(
                    'user_login' => $user_login,
                    'password' => $plaintext_pass,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'caps' => $user->caps,
                    'blogname' => get_option('blogname'),
                    'default_message' => htmlDefaultMessage($message)
                )
            )
        );
    }
}

// Use swu to send comments awaiting moderation to all moderators
if (!function_exists('wp_notify_moderator')) {
    function wp_notify_moderator($comment_id)
    {
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
                $notify_message  = sprintf( __('A new trackback on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $notify_message .= sprintf( __('Website : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= __('Trackback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
            case 'pingback':
                $notify_message  = sprintf( __('A new pingback on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $notify_message .= sprintf( __('Website : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= __('Pingback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
            default: // Comments
                $notify_message  = sprintf( __('A new comment on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
                $notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
                $notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
                $notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
                $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
                $notify_message .= sprintf( __('Whois  : http://whois.arin.net/rest/ip/%s'), $comment->comment_author_IP ) . "\r\n";
                $notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
                break;
        }

        $notify_message .= sprintf( __('Approve it: %s'),  admin_url("comment.php?action=approve&c=$comment_id") ) . "\r\n";
        if ( EMPTY_TRASH_DAYS )
            $notify_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
        else
            $notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
        $notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";

        $notify_message .= sprintf( _n('Currently %s comment is waiting for approval. Please visit the moderation panel:',
                'Currently %s comments are waiting for approval. Please visit the moderation panel:', $comments_waiting), number_format_i18n($comments_waiting) ) . "\r\n";
        $notify_message .= admin_url("edit-comments.php?comment_status=moderated") . "\r\n";
        /* End code to generate 'default_message' */

        $api = new \sendwithus\API($GLOBALS['api_key']);

        foreach ( $emails as $email ) {
            $response = $api->send(
                get_option('awaiting_approval'),
                array('address' => $email),
                array(
                    'email_data' => array(
                        'comment_ID' => $comment->comment_ID,
                        'comment_post_ID' => $comment->comment_post_ID,
                        'comment_author' => $comment->comment_author,
                        'comment_author_email' => $comment->comment_author_,
                        'comment_author_url' => $comment->comment_author_url,
                        'comment_author_IP' => $comment->comment_author_IP,
                        'comment_date' => $comment->comment_date,
                        'comment_date_gmt' => $comment->comment_date_gmt,
                        'comment_content' => $comment->comment_content,
                        'comment_karma' => $comment->comment_karma,
                        'comment_approved' => $comment->comment_approved,
                        'comment_agent' => $comment->comment_agent,
                        'comment_type' => $comment->comment_type,
                        'comment_parent' => $comment->comment_parent,
                        'user_id' => $comment->user_id,
                        'blogname' => get_option('blogname'),
                        'default_message' => htmlDefaultMessage($notify_message)
                    )
                )
            );
        }

        return true;
    }
}

if (!function_exists('wp_password_change_notification')) {
    function wp_password_change_notification( $user )
    {
        $message = sprintf(__('Password Lost and Changed for user: %s'), $user->user_login) . "\r\n";

        $api = new \sendwithus\API($GLOBALS['api_key']);

        $response = $api->send(
            get_option('password_change_notification'),
            array('address' => get_option('admin_email')),
            array(
                'email_data' => array(
                    'user_login' => $user->user_login,
                    'user_pass' => $user->user_pass,
                    'user_nicename' => $user->user_nicename,
                    'user_email' => $user->user_email,
                    'user_url' => $user->user_url,
                    'user_registered' => $user->user_registered,
                    'user_activation_key' => $user->user_activation_key,
                    'user_status' => $user->user_status,
                    'display_name' => $user->display_name,
                    'spam' => $user->spam,
                    'deleted' =>$user->deleted,
                    'default_message' => htmlDefaultMessage($message)
                )
            )
        );
    }
}

// Adds a function to occur when the filter retrieve_password_message is called
add_filter ("retrieve_password_message", "reset_password_notification", 10, 2 );

function reset_password_notification($content, $key) {
    //Grabs the information about the user attempting to reset their password
    $input = filter_input( INPUT_POST, 'user_login', FILTER_SANITIZE_STRING );

    if( is_email( $input ) ) {
        $user = get_user_by( 'email', $input );
    } else {
        $user = get_user_by( 'login', sanitize_user( $input ) );
    }

    $user_info = get_userdata($user->ID);

    //Creates a string to hold the end section of the password reset link
    $message = 'wp-login.php?action=rp&key='. $key. '&login='.$user->user_login;
    //Appends the password reset link to the url of the site we want the password to be reset on
    $url = network_site_url($message);
    //Gets the blogname
    $blogname = get_bloginfo('name');

    // Modify the message content to include the URL
    $content .= $url;

    //Create a new SWU email with the password reset information
    $api = new \sendwithus\API($GLOBALS['api_key']);
    $response = $api->send(
        get_option('password_reset'),
        array('address' => $user->user_email),
        array(
            'email_data' => array(
                'user_login' => $user->user_login,
                'reset_url' => $url,
                'user_nicename' => $user->user_nicename,
                'user_email' => $user->user_email,
                'blog_name' => $blogname,
                'default_message' => htmlDefaultMessage($content)
            )
        )
    );

    return false;
}

?>