<?php

/**
 * Redirects the web page.
 */
function redirect_to($url) {
    header('Location: '.server_uri_with_lang($url));
}

/**
 * Returns the server host.
 */
function server_host() {
    $host  = $_SERVER['HTTP_HOST'];
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
        return 'https://'.$host;
    } else {
        return 'http://'.$host;
    }
}

/**
 * Returns the server URI.
 */
function server_uri($postfix='') {
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
        return 'https://'.$host.$uri.'/'.$postfix;
    } else {
        return 'http://'.$host.$uri.'/'.$postfix;
    }
}

/**
 * Echoes a variable.
 *
 * If the variable is not set, the default value is echoed.
 */
function echo_var(&$var, $default='') {
    echo isset($var) ? $var : $default;
}

/**
 * Development mode is used for tests.
 */
function is_develoment_mode() {
    global $registry;
    return strcmp($registry->execution_mode, 'development') == 0;
}

/**
 * Production mode is the default one.
 */
function is_production_mode() {
    global $registry;
    return strcmp($registry->execution_mode, 'development') != 0;
}

/**
 * Echoes a message to the user that we are running in development mode.
 */
function development_mode_warning() {
    if (is_develoment_mode()) {
        echo '<div class="warning">System is running in development mode!</div>';
    }
}

/**
 * Display the errors.
 */
function output_errors($errors) {
    if (is_array($errors)) {
      $output = '<ul class="error">';
      foreach ($errors as $error) {
        $output.= '<li>'.$error.'</li>';
      }
      $output.= '</ul>';
      echo $output;
    } else {
      echo '<div class="error">'.$errors.'</div>';
    }
}

/**
 * Display a notice message.
 */
function output_notice($notice) {
  echo '<div class="notice">'.$notice.'</div>';
}

/**
 * Builds a form token for secure submit.
 */
function form_token($new_token=false) {
    if ($new_token) {
    /*** set a form token ***/
        $form_token = md5( uniqid('auth', true) );

    /*** set the session form token ***/
        $_SESSION['form_token'] = $form_token;
    }

    return $_SESSION['form_token'];
}

/**
 * Echoes text in html format.
 */
function echo_html_text($text) {
  echo get_html_text($text);
}

/**
 * returns text in html format.
 */
function get_html_text($text) {
  return htmlentities(stripslashes($text), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate an email address.
 * Provide email address (raw input)
 * Returns true if the email address has the email
 * address format and the domain exists.
 */
function is_valid_email($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}

function link_to($href='', $text='', $alt='') {
  return <<<LINK
<a href="{$href}" alt="{$alt}">{$text}</a>
LINK;
}

/**
 * Returns a $_POST variable or default value (null) if it is not set.
 */
function getPOST($var, $default=NULL) {
  return isset($_POST[$var]) ? $_POST[$var] : $default;
}

/**
 * Returns a $_GET variable or default value (null) if it is not set.
 */
function getGET($var, $default=NULL) {
  return isset($_GET[$var]) ? $_GET[$var] : $default;
}

/**
 *
 */
function import_stylesheet($path) {
  $full_path = server_uri('public/stylesheets/'.$path);
  return '@import "'.$full_path.'?o='.rand(1, 100).'";';
}
