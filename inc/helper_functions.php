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
    $input_code = '<select name="' . $name . '">';
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
        echo '<tr><td style="width: 49%;"><strong id="event_name">' . $text['event'] .'</strong> <div class="' . $current . '-display show">' . $text['display_parameters'] . '</div>';
            echo '<div class="' . $current . '-description">' . $text['description'] . '</div>';
            echo generateParameterListing($current, $text);
        echo '</td><td style="text-align: right;">';
        echo generateTemplateSelection($current, $GLOBALS['templates']);
        echo '</td></tr>';
    }
}

// Generate code to display/hide parameters sent with events.
function generateParameterListing($name, $parameterData) {
    $parameterListing = '
        <div class="parameters_'.$name.'" style="display: none;">' . $parameterData['parameters'] . 
        '</div>';

    return $parameterListing;
}

// Make 'default_message' HTML friendly.
function htmlDefaultMessage($default_message) {
    // Convert newline into line breaks.
    return preg_replace('/\\n/', '<br>', $default_message);
}

?>
