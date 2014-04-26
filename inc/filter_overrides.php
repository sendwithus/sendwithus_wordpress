<?php
/*
 * FILTER BASED OVERRIDES
 */

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

