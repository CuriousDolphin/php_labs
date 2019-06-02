<?php
session_start();
$GLOBALS['col'] = 6;
$GLOBALS['row'] = 10;
$errors = array();

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
