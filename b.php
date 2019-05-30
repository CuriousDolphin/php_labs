<?php 
  if(isset($_COOKIE['country'])){
    $var=$_COOKIE['country'];
    echo $var;
  }else{
    echo "cookie unavailable!";
  }
?>