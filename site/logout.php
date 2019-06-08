<?php

session_destroy();

if (ini_get("session.use_cookies")) {
  $_SESSION = array();
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 3600 * 24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

  //imposto il cookie nel passato,per farlo eliminare
}
header('HTTP/1.1 307 temporary redirect');
header('Location: index.php');

exit; // IMPORTANT to avoid further output from the script
