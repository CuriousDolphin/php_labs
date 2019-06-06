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


        echo json_encode("null");
      }
      mysqli_free_result($res);
      mysqli_close($db);
      break;
    case 'buy':
      if (!checkSession()) {
        $response['error']  = "timeout expired";
        echo json_encode($response);
        exit();
      }

      if (!isset($_POST['tickets'])) {
        $response['error']  = "no reservation for insert!";
        echo json_encode($response);
        exit();
      }
      $tickets = json_decode($_POST['tickets'], true);
      $db = dbConnection();
      mysqli_autocommit($db, false);
      $maxRow = $GLOBALS['row'];
      $maxCol = $GLOBALS['col'];
      try {
        foreach ($tickets as $ticket) {
          if (isset($ticket['row'])  && isset($ticket['place'])) {

            $row1 = intval($ticket['row']);
            $letter = strtolower($ticket['place']);
            $place  = ord($letter) - 97;
            //check esistenza row e col
            if (($row1 > $maxRow)  || ($row1 < 0) || ($place > $maxCol) || ($place < 0)) {
              // throw new Exception("seat  $row1 $letter ($place) not available row:col=" . $maxRow . "____" . $maxRow);
              throw new Exception("seat  $row1 $letter ($place)  row:col=" .  $maxRow . "____" . $maxCol);
            }

            $email = mysqli_real_escape_string($db, $_SESSION['email']);
            $row = mysqli_real_escape_string($db, $row1);
            $letter = mysqli_real_escape_string($db, $ticket['place']);
            $query = "SELECT * from tickets where row='$row' and place='$letter'"; //cerco il ticket se esiste controllo che non sia stato acquistato,se non esiste lo acquisto
            $res = mysqli_query($db, $query);
            if (!$res) { // QUERY
              throw new Exception("Error query search $query");
            }

            if ($res->num_rows > 0) { //posto prenotato o comprato
              $tic = mysqli_fetch_array($res, MYSQLI_ASSOC);
              $status = $tic['status'];

              if (strtolower($status) === "purchased")
                throw new Exception("Error seat already purchased $row");

              //update biglietto
              $query = "UPDATE tickets SET owner_email = '$email' , status ='purchased' WHERE row='$row' and place='$letter'";
              $res = mysqli_query($db, $query);
              if (!$res) { // QUERY
                throw new Exception("Error query update place $query");
              }
              if (!mysqli_commit($db)) { //COMMIT
                throw new Exception("Error commit");
              }
            } else { //POSTO LIBERO,LO compro
              $query = "INSERT INTO Tickets(id, row, place, status, owner_email) VALUES('', '$row', '$letter', 'purchased', '$email')";
              $res = mysqli_query($db, $query);
              if (!$res) { // QUERY
                throw new Exception("Error query insert place $query");
              }
              if (!mysqli_commit($db)) { //COMMIT
                throw new Exception("Error commit");
              }
            }
            mysqli_autocommit($db, true);
          } else {
            throw new Exception("bad input data");
          }
        }
        mysqli_autocommit($db, true);
        mysqli_close($db);
        $response['done']  = "Done! Purchase correctly elaborated";
        $response['email']  = $email;
        echo json_encode($response);
      } catch (Exception $e) {
        mysqli_rollback($db);
        mysqli_autocommit($db, true);
        mysqli_close($db);
        $error = $e->getMessage();
        $response['error']  = $error;
        $response['email']  = $_SESSION['email'];
        echo json_encode($response);
      }


      break;
    case 'reserve':

      if (!checkSession()) {
        $response['error']  = "timeout expired";
        echo json_encode($response);
        // header('HTTP/1.1 307 temporary redirect');
        //header('Location: login.php');
        exit();
      }
      // redirect client to login page   
      $db = dbConnection();

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
      $letter = strtolower($place);
      $label = chr($letter + 65); //genero le lettere
      $query = "SELECT * FROM tickets WHERE row='$row' and place='$label'";
      $results = mysqli_query($db, $query);
      mysqli_autocommit($db, false);
      try {
        if (mysqli_num_rows($results) > 0) { //ticket already in db
          $res = mysqli_fetch_array($results, MYSQLI_ASSOC);
          if (isset($res['status'])) {
            switch (strtolower($res['status'])) {
              case 'purchased':
                //$response['error']  = "ticket already purchased";
                //echo json_encode($response);

                throw new Exception("ticket already purchased");
                break;

              case 'reserved':
                $email = mysqli_real_escape_string($db, $_SESSION['email']);
                if ($res['owner_email'] !== $email) {
                  $query = "UPDATE tickets SET owner_email = '$email' , status ='reserved' WHERE row='$row' and place='$label'";
                  if (!mysqli_query($db, $query)) { // QUERY
                    throw new Exception("Error Query update $query");
                  }
                  if (!mysqli_commit($db)) { //COMMIT
                    throw new Exception("Error commit");
                  }

                  $response['done']  = "reservation correctly update";
                  $response['email']  = $email;
                  $response['row'] = $row;
                  $response['place'] = $label;
                  echo json_encode($response);
                } else {
                  $query = "DELETE FROM tickets WHERE row='$row' and place='$label'";
                  if (!mysqli_query($db, $query)) { // QUERY
                    throw new Exception("Error Query delete reservation");
                  }
                  if (!mysqli_commit($db)) { //COMMIT
                    throw new Exception("Error commit");
                  }
                  $response['done']  = "reservation deleted by user";
                  $response['email']  = $email;
                  $response['row'] = $row;
                  $response['place'] = $label;
                  echo json_encode($response);
                }

                break;
              default:
                break;
            }
          }
          mysqli_autocommit($db, true);
          mysqli_close($db);
        } else { //tickets not in db,insert
          $email = mysqli_real_escape_string($db, $_SESSION['email']);

          $query = "INSERT INTO Tickets(id, row, place, status, owner_email) VALUES('', '$row', '$label', 'reserved', '$email')";
          if (!mysqli_query($db, $query)) { // QUERY
            throw new Exception("Error reservation insert");
          }
          if (!mysqli_commit($db)) { //COMMIT
            throw new Exception("Error commit");
          }
          mysqli_autocommit($db, true);
          mysqli_close($db);
          $response['done']  = "reservation correctly inserted";
          $response['email']  = $email;
          $response['row'] = $row;
          $response['place'] = $label;
          echo json_encode($response);
        }
      } catch (Exception $e) {

        $email = $_SESSION['email'];
        mysqli_rollback($db);
        mysqli_autocommit($db, true);
        mysqli_close($db);
        $error = $e->getMessage();
        $response['email']  = $email;
        $response['error']  = $error;
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
    return $tickets;
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
  if ($new || ($diff > 120)) { // new or with inactivity period too long      
    $_SESSION = array();     // If it's desired to kill the session, also delete the session cookie.    
    // Note: This will destroy the session, and not just the session data!    
    if (ini_get("session.use_cookies")) { // PHP using cookies to handle session      
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 3600 * 24, $params["path"],       $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();  // destroy session     

    return false; // IMPORTANT to avoid further output from the script
  } else {
    $_SESSION['time'] = time(); /* update time */
    return true;
  }
}
