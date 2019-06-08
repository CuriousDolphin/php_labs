<nav class="navbar navbar-dark bg-primary">
  <a class="navbar-brand margin-left" href="index.php"> <i class="fas fa-plane-departure margin-right"></i>Volagratis</a>
  <div class="user-info">
    <?php

    if (isset($_SESSION['email'])) {
      $mail = $_SESSION['email'];
      echo "<p>$mail</p>";
      if (isset($_SESSION['time'])) {
        $time = $_SESSION['time'];

        echo "<p> Last time access ";
        echo date(' m/d/Y H:i:s', $time) . "</p>";
      }
    }
    ?>
    <div>
</nav>