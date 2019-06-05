<?php
include('functions.php');
checkHttps();
checkCookie();
if (!isset($_SESSION['email'])) {
  header('location: index.php');
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
          <h5 class="card-title">Today Trip
            <?php if (isset($_SESSION['email'])) {
              echo "<button type='button' class='btn btn-link'><a class='icon-white' href='personal.php'><i class='fas fa-sync-alt'></i></a></button> ";
            } ?>

          </h5>
        </div>
        <div class="card-body">

          <?php
          if (isset($_SESSION['email']))
            $myMail = $_SESSION['email'];
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
                $mail = strtolower($ticket['owner_email']);
                $reserved[$ticket['row']][$colConverted] = $mail;
                $nreserved++;

                // $filtered[$ticket['row']][$col];
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
                  switch ($reserved[$i][$j]) {
                    case 'hidden': //reserved but not by me
                      echo "<td class='cell ml reserved' id='i{$i}j{$j}'</td>";
                      break;

                    default:
                      if ($reserved[$i][$j] === $myMail)  echo "<td class='cell ml reserved-by-me clickable' (click)='reserve()' id='i{$i}j{$j}'</td>"; //reserved by me -> yellow 
                      else echo "<td class='cell ml reserved clickable' (click)='reserve()' id='i{$i}j{$j}'</td>"; //reserved but not by me

                      break;
                  }
                } else {
                  echo "<td class='cell ml free clickable' id='i{$i}j{$j}'  ></td>"; //FREE
                }
              }
            }
            echo "</tr>";
          }
          echo "</table>";

          $total = $col * $row;
          $free = $total - ($nreserved + $npurchased);
          echo "<p class='card-text no-margin mb-1'>Total Seat: <span id='n-total'> $total</span> </p>";
          echo "<p class='card-text no-margin mb-1 ' > <span class='cell ml free mr-1' ></span> free: <span id='n-free'> $free</span>  </p>";
          echo "<p class='card-text no-margin mb-1 ' ><span class='cell ml reserved mr-1' ></span> reserved: <span id='n-reserved'> $nreserved </span></p>";
          echo "<p class='card-text no-margin mb-1 ' ><span class='cell ml purchased mr-1'></span> purchased: <span id='n-purchased'>$npurchased </span></p>";

          ?>

          <?php if (isset($_SESSION['email'])) {
            echo "<button type='button' class='btn btn-primary'>Buy</button>";
          }
          ?>
        </div>
      </div>
      <div class='alert' role='alert' id='alert'></div>
    </div>


  </div>
  <script type="text/javascript">
    myTickets = new Array();
    $('.clickable').click(
      function(event) {

        id = event.target.id;
        reg = '[i](\d+)[j](\d+)';
        var patt = new RegExp("[i](.+)[j](.+)");
        var res = patt.exec(id.toString()); //estraggo gli indici
        if (res) {
          place = res[1];
          row = res[2];
          console.log('click!', row, place);
          reserve(row, place);


        }

      }
    )

    function getTickets(userMail) {
      $.ajax({
        url: "functions.php",
        type: "POST",
        data: {
          api: "getTickets"
        }
      }).done(function(evt) {
        var tickets = JSON.parse(evt);
        console.log('tickets updated:', tickets);
        var npurchased = 0;
        var nreserved = 0;
        $('td').removeClass('reserved').removeClass('reserved-by-me').removeClass('purchased').addClass('free');
        if (tickets !== "null") {
          // console.log(evt);
          var str, converted;

          var ntot = tickets.lenght;


          for (var ind in tickets) {
            converted = tickets[ind].place.toString().toLowerCase().charCodeAt(0) - 97;
            /* USED TO CONVERT CHAR TO NUMBER */
            str = "#i" + tickets[ind].row + 'j' + converted;
            switch (tickets[ind].status) {
              case "purchased":
                npurchased++;
                $(str).removeClass('free');

                $(str).addClass('purchased');
                break;

              case "reserved":
                nreserved++;
                if (userMail) { //reserved by me
                  if (userMail === tickets[ind]['owner_email']) {
                    $(str).removeClass('free');
                    $(str).addClass('reserved-by-me');
                  } else {
                    $(str).removeClass('free');
                    $(str).addClass('reserved');
                  }
                } else {
                  $(str).removeClass('free');
                  $(str).addClass('reserved');
                }

                break;
              default:
                break;
            }
          }
        }
        var total = $('#n-total').text();
        $('#n-free').html(total - (nreserved + npurchased));
        $('#n-reserved').html(nreserved.toString());
        $('#n-purchased').html(npurchased.toString());
      });
    }

    function reserve(i, j) {
      console.log(i, j);

      return $.ajax({
        url: "functions.php",
        type: "POST",
        data: {
          api: "reserve",
          place: i,
          row: j,
        }
      }).done(function(evt) {
        console.log('reserveaaaaaa', evt);
        res = JSON.parse(evt);

        console.log('reserve result', JSON.parse(evt));
        if (res['error']) {
          console.log('error', res['error']);
          $('#alert').text(res['error']);
          $('#alert').removeClass('alert-success').addClass('alert-danger');
          $('#alert').stop().fadeIn().delay(1500).fadeOut('slow');


        }
        if (res['done']) {

          console.log('done! updating seats');
          $('#alert').text(res['done']);
          $('#alert').removeClass('alert-danger').addClass('alert-success');
          $('#alert').stop().fadeIn().delay(1500).fadeOut('slow');


          mail = res['email']
          getTickets(mail);
        }
        //return JSON.parse(evt);
        //console.log(JSON.parse(evt));
      })

    }
  </script>
</body>

</html>