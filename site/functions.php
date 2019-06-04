<?php
session_start();
$GLOBALS['col'] = 6;
$GLOBALS['row'] = 10;
$errors = array();
// AJAX API
if (isset($_POST['api'])) {
  $cmd = $_POST['api'];
  switch ($cmd) {
    case 'getTickets':
      $tickets = array();

      $db = dbConnection();
      $query = "SELECT * FROM tickets";
      $res = mysqli_query($db, $query);
      if ($res->num_rows > 0) {

        while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
          array_push($tickets, $row);
        }
        echo json_encode($tickets);
      } else {
        mysqli_close($db);
        echo "[]";
      }
      mysqli_free_result($res);
      mysqli_close($db);
      break;
    default:
      break;
  }
}
// login user
if (isset($_POST['app_login'])) {
  $db = dbConnection();
  $email = mysqli_real_escape_string($db, $_POST['email']);
  /* SANITIZE STRING */
  $password = $_POST['password'];

  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $password = md5($password);
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
      $_SESSION['email'] = $email;
      $_SESSION['time'] = time();
      mysqli_close($db);
      // $_SESSION['success'] = "You are now logged in";
      header('location: index.php');
    } else {
      array_push($errors, "Wrong email/password ");
    }
  }
  mysqli_close($db);
}
function getTickets()
{
  $tickets = array();

  $db = dbConnection();
  $query = "SELECT * FROM tickets";
  $res = mysqli_query($db, $query);
  if ($res->num_rows > 0) {

    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
      array_push($tickets, $row);
    }
    return $tickets;
  } else {
    mysqli_close($db);
    return "[]";
  }
  mysqli_free_result($res);
  mysqli_close($db);
}
function checkHttps()
{
  if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') { } else {  // Redirect su HTTPS  
    // eventuale distruzione sessione e cookie relativo  
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
  }
}
function getsize()
{
  return $GLOBALS['col'] . "_" . $GLOBALS['row'];
}
function checkCookie()
{
  setcookie("test_cookie", "test", time() + 3600, '/');
  if (count($_COOKIE) == 0) {
    echo "<h2>Enable cookies </h2>";
    exit();
  }
}
function dbConnection()
{
  $db = mysqli_connect("localhost", "root", "");
  if (mysqli_connect_errno()) {
    array_push($errors, "database error ");
    die("Internal server error" . mysqli_connect_errno());
  }
  if (!mysqli_select_db($db, "my_db")) {
    array_push($errors, "database error ");
    die("Selection of DB error");
  }
  return $db;
}
function checkSession()
{
  $t = time();
  $diff = 0;
  $new = false;
  if (isset($_SESSION['time'])) {
    $t0 = $_SESSION['time'];
    $diff = ($t - $t0);     // inactivity  time
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
    return false; // IMPORTANT to avoid further output from the script
  } else {
    $_SESSION['time'] = time(); /* update time */
    echo '<html><body>Tempo ultimo accesso aggiornato: ' . $_SESSION['time'] . '</body></html>';
    return true;
  }
}
