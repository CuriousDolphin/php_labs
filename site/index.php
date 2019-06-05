<?php
include('functions.php');
checkHttps();
checkCookie();
if (isset($_SESSION['email'])) {
  header('location: personal.php');
}
?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="css/styles.css">
  <!-- FONT AWESOME -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.1.1.min.js">
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <title>Volagratis</title>
</head>

<body>
  <noscript>
    Javascript is not enabled. Please, enable it!
  </noscript>
  <?php include('header.php') ?>
  <div class="main-container">
    <?php include('sidebar.php') ?>
    <div class="container table-container">

      <!-- Content here -->
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="card-title">Today Trip</h5>
        </div>
        <div class="card-body">

          <?php

          $row = $GLOBALS['row'];
          $col = $GLOBALS['col'];
          $split = $col / 2;
          $tickets = getTickets();
          $reserved = array();
          $purchased = array();
          $nreserved = 0;
          $npurchased = 0;
          foreach ($tickets as $ticket) {
            $letter = strtolower($ticket['place']);
            $colConverted = ord($letter) - 97; //converte la lettera in nell'ascii corrispondente
            switch ($ticket['status']) {
              case 'reserved':
                $reserved[$ticket['row']][$colConverted] = 'hidden';
                $nreserved++;
                break;
              case 'purchased':
                $purchased[$ticket['row']][$colConverted] = 'hidden';
                $purchased++;
                break;
              default:
                break;
            }
          }
          echo "<div class='label-wrapper'>";
          for ($j = 0; $j < $col; $j++) {
            $label = chr($j + 65); //genero le lettere
            echo "<div class='label'>$label</div>";
          }
          echo "</div>";

          echo "<table class='table'>";

          for ($i = 0; $i < $row; $i++) {
            echo "<tr name='row' class='row'>";
            for ($j = 0; $j < $col; $j++) {
              if (isset($purchased[$i][$j])) {
                echo "<td class='cell ml purchased' id='i{$i}j{$j}'</td>"; //purchased
              } else {
                if (isset($reserved[$i][$j])) { //reserved
                  echo "<td class='cell ml reserved' id='i{$i}j{$j}'</td>";
                } else {
                  echo "<td class='cell ml free ' id='i{$i}j{$j}'  ></td>"; //FREE
                }
              }
            }
            echo "</tr>";
          }
          echo "</table>";

          $total = $col * $row;
          $free = $total - ($nreserved + $npurchased);
          echo "<p class='card-text no-margin mb-1'>Total Seat: $total </p>";
          echo "<p class='card-text no-margin mb-1'> <span class='cell ml free mr-1' ></span > free: $free  </p>";
          echo "<p class='card-text no-margin mb-1'> <span class='cell ml reserved mr-1' ></span > reserved: $nreserved </p>";
          echo "<p class='card-text no-margin mb-1'><span class='cell ml purchased mr-1'></span > purchased: $npurchased </p>";

          ?>


        </div>
      </div>



    </div>
  </div>

</body>

</html>