<?php
function sendwithus_validate_settings($args) {
    // Used to validate settings passed to the plugin.
    echo("Sanitized!<br/>");
    return $args;
}

// Wrapper for the emails() function in the API
function getTemplates(){
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
function generateTemplateSelection($name, $array)
{
    if (get_option('api_key')) {
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

    } else {
        echo "<p>Please set your API Key</p>";
    }
}

// Generate table body from the wp_notification arrays
function generateTemplateTable($notification_array)
{
    foreach ($notification_array as $name => $text) {
        echo '<tr><td>' . $text;
        echo '</td><td>';
        echo generateTemplateSelection($name,$GLOBALS['templates']);
        echo '</td></tr>';
    }
}

// Make 'default_message' HTML friendly.
function htmlDefaultMessage($default_message) {
    // Convert newline into line breaks.
    return preg_replace('/\\n/', '<br>', $default_message);
}
