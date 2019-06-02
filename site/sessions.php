<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
$t = time();
$diff = 0;
$new = false;
if (isset($_SESSION['time'])) {
  $t0 = $_SESSION['time'];
  $diff = ($t - $t0); // inactivity 
} else {
  $new = true;
}
if ($new || ($diff > 120)) { // new or with inactivity period too long      
  $_SESSION = array();     // If it's desired to kill the session, also delete the session cookie.    
  // Note: This will destroy the session, and not just the session data!    
  if (ini_get("session.use_cookies")) { // PHP using cookies to handle session      
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600 * 24, $params["path"],       $params["domain"], $params["secure"], $params["httponly"]);
  }
  session_destroy();  // destroy session     
  // redirect client to login page   
  exit; // IMPORTANT to avoid further output from the script
} else {
  $_SESSION['time'] = time(); /* update time */
  echo '<html><body>Tempo ultimo accesso aggiornato: ' . $_SESSION['time'] . '</body></html>';
}
