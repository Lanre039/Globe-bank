<?php 

    // add the leading '/' if not present
    function url_for($script_path) {
        // add the leading '/' if not present
        if($script_path[0] != '/') {
            $script_path = "/" . $script_path;
        }
        return WWW_ROOT . $script_path;
    }
      
    function u($string=""){
        // encode reserved characters in string
        return urlencode($string);
    }

    function raw_u($string=""){
        // encode reserved characters in string
        return rawurlencode($string);
    }

    function h($string=""){
        // make html chars harmless
        return htmlspecialchars($string);
    }

    function error_404() {
        header($_SERVER['SERVER_PROTOCOL'] . " 404 Not found");
        exit();
    }
    function error_500() {
        header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
        exit();
    }
    function redirect_to($location) {
        // to redirect page
        header("Location: " . $location);
        exit;
    }
    
    function is_post_request() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    function is_get_request() {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }


    function display_errors($errors=array()) {
        $output = '';
        if(!empty($errors)) {
          $output .= "<div class=\"errors\">";
          $output .= "Please fix the following errors:";
          $output .= "<ul>";
          foreach($errors as $error) {
            $output .= "<li>" . h($error) . "</li>";
          }
          $output .= "</ul>";
          $output .= "</div>";
        }
        return $output;
    }

    function get_and_clear_session_message() {
        if(isset($_SESSION['message']) && $_SESSION['message'] != '') {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
            return $message;
        }
    }

    function display_session_message() {
        $message = get_and_clear_session_message();
        if (!is_blank($message)) {
            return '<div id="message">' . h($message) . '</div>';
        }
    }
      

?>