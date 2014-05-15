<?php
/*
 *  MISCELLANEOUS FUNCTIONS
 */


function sendwithus_validate_settings($args) {
    // Used to validate settings passed to the plugin.
    echo("Sanitized!<br/>");
    return $args;
}

// Wrapper for the emails() function in the API
function getTemplates() {
    $api_key = get_option('api_key');
    $api = new \sendwithus\API($api_key);
    $response = $api->emails();

    foreach ($response as $template) {
        $template_names[] = $template->name;
    }

   if(!(in_array("default_message",$template_names))){
        $response = $api->create_email('default_message',
            '{{email_subject}}',
            '<html><head></head><body>{{default_message}}</body></html>');
        $response = $api->emails();
    }
    return $response;
}

// Get the API key for use as a global variable.
function getAPIKey() {
    return get_option('api_key');
}

// Generate a template selection drop down list;
// value = template id
// text = template name
function generateTemplateSelection($name, $array) {
    $input_code = '<select name="' . $name . '" style="width: 100%">';
    $current_template = get_option($name);

    foreach ($array as $template) {
        if($template->id == $current_template){
            $input_code .= '<option value=' . $template->id . ' selected>' . $template->name . '</option>';
        }
        else {
            $input_code .= '<option value=' . $template->id . '>' . $template->name . '</option>';
        }
    }

    $input_code .= '</select>';
    return $input_code;
}

// Generate table body from the wp_notification arrays
function generateTemplateTable($notification_array) {
    foreach ($notification_array as $current => $text) {
        echo '<tr><td style="width: 49%; padding-bottom: 0px;"><strong>' . $text['event'] .'</strong>';
        echo '<div class="' . $current . '-description"><p class="description">' . $text['description'] . '</p></div>';
        echo '</td><td style="text-align: right; padding-bottom: 0px;">';
        echo generateTemplateSelection($current, $GLOBALS['templates']);
        echo '<div class="button display_info parameters_button ' . $current . '">Show parameters sent to sendwithus</div>';
        echo '</td></tr>';
        echo '<tr><td colspan="2" style="padding-top: 0px;">';
        echo generateParameterListing($current, $text);
        echo '</td></tr>';
    }
}

// Generate code to display/hide parameters sent with events.
function generateParameterListing($name, $parameterData) {
    $parameterListing = '
        <span class="parameters ' . $name . '">' . $parameterData['parameters'] . 
        '</span>';

    return $parameterListing;
}

// Make 'default_message' HTML friendly.
function htmlDefaultMessage($default_message) {
    // Convert newline into line breaks.
    return preg_replace('/\\n/', '<br>', $default_message);
}

?>
