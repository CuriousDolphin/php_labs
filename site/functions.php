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
    case 'reserve':
      $db = dbConnection();
      if (checkSession()) {  //user logged
        if (isset($_POST['place']) && isset($_POST['row'])) {
          $place = mysqli_real_escape_string($db, $_POST['place']);
          $row = mysqli_real_escape_string($db, $_POST['row']);
        } else {
          $response['error']  = "wrong params";
          echo json_encode($response);
          break;
        }


        if ($row > $GLOBALS['row'] || $row < 0 || $place > $GLOBALS['col'] || $place < 0) {
          $response['error']  = "seat not available";
          echo json_encode($response);
          break;
        }



        $query = "SELECT * FROM tickets WHERE row='$row' and place='$place' ";

        $results = mysqli_query($db, $query);
        mysqli_autocommit($db, false);
        try {
          if ($results->num_rows > 0) { //ticket already in db
            if (isset($results['status'])) {
              switch (strtolower($results['status'])) {
                case 'purchased':
                  $response['error']  = "ticket already purchased";
                  echo json_encode($response);
                  break;

                case 'reserved':
                  $email = mysqli_real_escape_string($db, $_SESSION['email']);
                  $letter = strtolower($place);
                  $label = chr($letter + 65); //genero le lettere

                  $query = "UPDATE tickets SET owner_email = '$email' , status ='reserved' FROM tickets WHERE row='$row' and place='$label'";
                  if (!mysqli_query($db, $query)) { // QUERY
                    throw new Exception("Error Query update");
                  }
                  if (!mysqli_commit($db)) { //COMMIT
                    throw new Exception("Error commit");
                  }
                  mysqli_autocommit($db, true);
                  mysqli_close($db);
                  $response['done']  = "ticket correctly reserved";
                  $response['email']  = $email;
                  echo json_encode($response);
                  break;
                default:
                  break;
              }
            }
          } else { //tickets not in db,insert
            $email = mysqli_real_escape_string($db, $_SESSION['email']);
            $letter = strtolower($place);
            $label = chr($letter + 65); //genero le lettere
            $query = "INSERT INTO Tickets(id, row, place, status, owner_email) VALUES('', '$row', '$label', 'reserved', '$email')";
            if (!mysqli_query($db, $query)) { // QUERY
              throw new Exception("Error Query insert");
            }
            if (!mysqli_commit($db)) { //COMMIT
              throw new Exception("Error commit");
            }
            mysqli_autocommit($db, true);
            mysqli_close($db);
            $response['done']  = "ticket correctly inserted";
            $response['email']  = $email;
            echo json_encode($response);
          }
        } catch (Exception $e) {
          mysqli_rollback($db);
          mysqli_autocommit($db, true);
          mysqli_close($db);
          $error = $e->getMessage();
          $response['error']  = $error;
          echo json_encode($response);
        }
      } else {
        $response['error']  = "timeout expired or user not logged";
        echo json_encode($response);
      }
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
    exit("Internal server error" . mysqli_connect_errno());
  }
  if (!mysqli_select_db($db, "my_db")) {
    array_push($errors, "database error ");
    exit("Selection of DB error");
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
  if ($new || ($diff > 10)) { // new or with inactivity period too long      
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
    return true;
  }
}
