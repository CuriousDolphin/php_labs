<?php
if (isset($_REQUEST['name']) && isset($_REQUEST['surname'])) {
  $name = $_REQUEST['name'];
  $surname = $_REQUEST['surname'];
  setcookie("name", $name);
  setcookie("surname", $surname);
}
if (isset($_COOKIE['name']) && isset($_COOKIE['surname'])) {
  $name = $_COOKIE['name'];
  $surname = $_COOKIE['surname'];
  echo "<h3>Welcome back $name $surname</h3>";
}
if (isset($name) && isset($surname)) {
  echo "<p>Lorem ipsum dolor sit amet, consectetur adipisci elit, sed do eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullamco laboriosam, nisi ut aliquid ex ea commodi consequatur. Duis aute irure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>";
} else {
  echo "<h2>no info about user</h2>";
}
