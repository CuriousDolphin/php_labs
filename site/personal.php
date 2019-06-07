<?php
include('functions.php');
$errors = array();
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
            <?php {
              echo "<button type='button' class='btn btn-info reload' id='reload'  ><a class='icon-white' ><i class='fas fa-sync-alt'></i></a></button> ";
            } ?>

          </h5>
        </div>
        <div class="card-body">

          <?php

          $myMail = $_SESSION['email'];
          $row = $GLOBALS['row'];
          $col = $GLOBALS['col'];
          $split = $col / 2;
          $tickets = getTickets();
          $reserved = array();
          $purchased = array();
          $nreserved = 0;
          $npurchased = 0;
          $reservedByMe = array();
          $purchasedByMe = array();
          foreach ($tickets as $ticket) {
            $letter = strtolower($ticket['place']);
            $colConverted = ord($letter) - 97; //converte la lettera in nell'ascii corrispondente
            $mail = strtolower($ticket['owner_email']);
            switch ($ticket['status']) {
              case 'reserved':
                $reserved[$ticket['row']][$colConverted] = $mail;
                $nreserved++;
                if ($mail === $myMail) { //my reservation
                  array_push($reservedByMe, $ticket);
                }
                // $filtered[$ticket['row']][$col];
                break;
              case 'purchased':
                $purchased[$ticket['row']][$colConverted] = 'hidden';
                $npurchased++;
                if ($mail === $myMail) { //my reservation
                  array_push($purchasedByMe, $ticket);
                }
                break;
              default:
                break;
            }
          }
          echo "<div class='left-container'>";
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
                      echo "<td class='cell ml reserved btn ' id='i{$i}j{$j}'</td>";
                      break;

                    default:
                      if ($reserved[$i][$j] === $myMail) {
                        echo "<td class='cell ml reserved-by-me clickable btn ' (click)='reserve()' id='i{$i}j{$j}'</td>"; //reserved by me -> yellow 

                      } else echo "<td class='cell ml reserved clickable btn ' (click)='reserve()' id='i{$i}j{$j}'</td>"; //reserved but not by me

                      break;
                  }
                } else {
                  echo "<td class='cell ml free clickable btn' id='i{$i}j{$j}'  ></td>"; //FREE
                }
              }
            }
            echo "</tr>";
          }
          echo "</table>";
          echo "</div>";
          $total = $col * $row;
          $free = $total - ($nreserved + $npurchased);

          echo "<div class='stats-container'>";
          echo "<div class='stats'>";
          echo "<p class='card-text no-margin mb-1'>Total Seat: <span id='n-total'> $total</span> </p>";
          echo "<p class='card-text no-margin mb-1 ' > <span class='cell ml free mr-1' ></span> free: <span id='n-free'> $free</span>  </p>";
          echo "<p class='card-text no-margin mb-1 ' ><span class='cell ml reserved mr-1' ></span> reserved: <span id='n-reserved'> $nreserved </span></p>";
          echo "<p class='card-text no-margin mb-1 ' ><span class='cell ml purchased mr-1'></span> purchased: <span id='n-purchased'>$npurchased </span></p>";
          echo "</div>";
          echo "<div class='my-stats'>";
          echo "<p class='card-text no-margin mb-1 my-reservation'><i class='fas fa-shopping-cart mr-2'></i> Cart: <br>";
          echo "<span id='reservation-list' class='ml-3'>";
          foreach ($reservedByMe as $tmp) {
            echo " " . $tmp['row'] . $tmp['place'];
          }
          echo "</span></p></div>";
          echo "<div class='my-stats'><p class='card-text no-margin mb-1 my-reservation'><i class='fas fa-dollar-sign'></i> Own: <br>";
          echo "<span id='purchase-list' class='ml-3'>";
          foreach ($purchasedByMe as $tmp) {
            echo " " . $tmp['row'] . $tmp['place'];
          }
          echo "</span></p></div>";
          if (count($reservedByMe) > 0)  echo "<button type='button' class='btn btn-primary' id='buy'>Buy</button>";
          else echo "<button type='button' class='btn btn-primary hidden' id='buy'>Buy</button>";

          echo "</div>";

          ?>


          </>
        </div>

      </div>
      <div class='alert' role='alert' id='alert'></div>
      <?php
      if (isset($_GET['error']))
        echo "<div class='alert alert-danger' role='alert' id='getAlert'>" . $_GET['error'] . "</div>";
      if (isset($_GET['done']))
        echo "<div class='alert alert-success' role='alert' id='getAlert'>" . $_GET['done'] . "</div>";
      ?>
    </div>
    <script type="text/javascript">
      myReservedTickets = new Array();
      myTickets = new Array();
      $('#reload').click(function() {
        window.location = 'personal.php';
      });


      $('#getAlert').delay(2000).fadeOut();
      $('.clickable').click(
        function(event) {

          id = event.target.id;
          reg = '[i](\d+)[j](\d+)';
          var patt = new RegExp("[i](.+)[j](.+)");
          var res = patt.exec(id.toString()); //estraggo gli indici
          if (res) {
            place = res[1];
            row = res[2];

            reserve(row, place);


          }

        }
      )

      $('#buy').click(function() {
          /* if (myReservedTickets.length <= 0) {
            $('#alert').text("You dont have reserved tickets");
            $('#alert').removeClass('alert-success').addClass('alert-danger');
            $('#alert').finish().fadeIn().delay(1000).fadeOut();
            return;
          } */
          myReservedTickets = new Array();
          $('.reserved-by-me').each(
            function() {
              console.log('reserved by me_>', this.id);
              if (!this.id)
                return;
              ticket = new Object;
              ticket['row'] = this.id[1];
              ticket['place'] = String.fromCharCode(Number(this.id[3]) + 65);
              // ticket['status'] = "purchased";
              myReservedTickets.push(ticket);
            }
          )
          console.log('invio al server', myReservedTickets);
          $.ajax({
            url: "functions.php",
            type: "POST",
            data: {
              api: "buy",
              tickets: JSON.stringify(myReservedTickets)
            }
          }).done(function(evt) {
            console.log('buy result:', evt);
            res = JSON.parse(evt);
            if (res['error']) {
              console.log('error', res['error']);
              $('#alert').text(res['error']);
              $('#alert').removeClass('alert-success').addClass('alert-danger');
              $('#alert').finish().fadeIn().delay(1000).fadeOut();
              if (res['error'] === "timeout expired") {
                window.location = 'login.php?msg=SessionTimeOut';
              }
              //document.cookie = "message=" + res['error'];
              window.location = 'personal.php?error=' + res['error'];

            }
            if (res['done']) {
              console.log('done! purchased elaborated');
              //  document.cookie = "message=" + res['done'];
              window.location = 'personal.php?done=' + res['done'];
              $('#alert').text(res['done']);
              $('#alert').removeClass('alert-danger').addClass('alert-success');
              $('#alert').finish();
              $('#alert').fadeIn().delay(1000).fadeOut();
            }
            mail = res['email']
            //getTickets(mail);
          })

        }


      );


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
          var free = Number($('#n-free').html());
          var reserved = Number($('#n-reserved').html());
          var purchased = Number($('#n-purchased').html());
          res = JSON.parse(evt);
          console.log(res);
          if (res['error']) {
            console.log('error->', res['error']);

            str = "#i" + j + "j" + i;
            $(str).removeClass("reserved-by-me").addClass("free");
            if (res['error'] === "ticket already purchased") {
              $(str).removeClass("reserved-by-me").removeClass("free").addClass("purchased");
              console.log('siamooo dentroooooo');
              //incremento purchased
              free -= 1
              purchased += 1
              $('#n-free').html(free);
              $('#n-purchased').html(purchased);

            }
            $('#alert').text(res['error']);
            $('#alert').removeClass('alert-success').addClass('alert-danger');
            $('#alert').finish().fadeIn().delay(1000).fadeOut();
            //console.log('ecoooooolooo', res['error']);
            if (res['error'] == "timeout expired") {
              window.location = 'login.php?msg=SessionTimeOut';
            }
            mail = res['email']

          }
          if (res['done']) {
            console.log('done->', res['done']);
            str = "#i" + j + "j" + i;
            switch (res['done']) {
              case "reservation correctly update":
                if ($(str).hasClass("reserved-by-me")) { //giallo->verde
                  $(str).removeClass("reserved-by-me").addClass("free").removeClass("reserved");

                  free += 1
                  reserved -= 1
                  $('#n-free').html(free);
                  $('#n-reserved').html(reserved);

                } else if ($(str).hasClass("reserved")) { //arancione->giallo
                  $(str).removeClass("reserved").addClass("reserved-by-me");
                  free -= 1
                  reserved += 1
                  $('#n-free').html(free);
                  $('#n-reserved').html(reserved);

                } else {
                  free -= 1
                  reserved += 1
                  $('#n-free').html(free);
                  $('#n-reserved').html(reserved);

                  $(str).removeClass("free").removeClass("reserved").addClass("reserved-by-me");
                }

                break;

              case "reservation correctly inserted": //giallo
                free -= 1
                reserved += 1
                $('#n-free').html(free);
                $('#n-reserved').html(reserved);
                $(str).removeClass("free").addClass("reserved-by-me");
                $(str).removeClass("reserved");
                //aggiornamento stat
                break;


              case 'reservation deleted by user':
                free += 1
                reserved -= 1
                $('#n-free').html(free);
                $('#n-reserved').html(reserved);
                console.log('im in exact case');
                $(str).removeClass("reserved-by-me");
                $(str).removeClass("reserved").addClass('free');
                break;




            }

            console.log('done! updating seats');
            $('#alert').text(res['row'] + res['place'] + " " + res['done']);
            $('#alert').removeClass('alert-danger').addClass('alert-success');
            $('#alert').finish();
            $('#alert').fadeIn().delay(1000).fadeOut();
            mail = res['email'];


            $('#buy').removeClass('hidden');

            cartString = ''


            $('.reserved-by-me').each(
              function(evt) {
                cartString += this.id[1] + String.fromCharCode(Number(this.id[3]) + 65) + " ";
              }
            )
            $('#reservation-list').html(cartString);



          }

        })

      }

      function getTickets(userMail) { //get all tickets AJAX

        //  console.log('get tickets ', userMail, str, i, j);
        mode = str;
        $.ajax({
          url: "functions.php",
          type: "POST",
          data: {
            api: "getTickets"
          }
        }).done(function(evt) {



          myReservedTickets = new Array();
          myTickets = new Array();
          var tickets = JSON.parse(evt);
          console.log('--tickets updated:', tickets);
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

                  if (userMail === tickets[ind]['owner_email']) {

                    $(str).removeClass('free');
                    $(str).removeClass('btn');
                    $(str).addClass('purchased');


                    myTickets.push(tickets[ind]);

                  } else {


                    if (full) { //aggiornamento singolo

                      $(str).removeClass('free');
                      $(str).removeClass('btn');
                      $(str).addClass('purchased');
                    }
                  }
                  break;

                case "reserved":

                  if (userMail) { //reserved by me
                    if (userMail === tickets[ind]['owner_email']) {
                      $(str).removeClass('free');
                      $(str).addClass('reserved-by-me');
                      myReservedTickets.push(tickets[ind]);
                      nreserved++;
                    } else {

                      $(str).removeClass('free');
                      $(str).addClass('reserved');
                      nreserved++;

                    }
                  } else {

                    $(str).removeClass('free');
                    $(str).addClass('reserved');
                    nreserved++;

                  }

                  break;
                default:
                  break;
              }

            }
          }
          str = '';
          myTickets.forEach(function(ticket) {
            str += " " + ticket['row'] + ticket['place'];
          })
          $('#purchase-list').html(str);
          //console.log('myPurchasedTickets updates', myTickets);


          str = '';
          myReservedTickets.forEach(function(ticket) {
            str += " " + ticket['row'] + ticket['place'];
          })
          if (str === '') str = 'empty';
          $('#reservation-list').html(str);
          console.log('myReservedTickets updates', myReservedTickets);
          if (myReservedTickets.length > 0) {
            $('#buy').removeClass('hidden');
          } else {
            $('#buy').addClass('hidden');
          }
          var total = $('#n-total').text();
          $('#n-free').html(total - (nreserved + npurchased));
          $('#n-reserved').html(nreserved.toString());
          $('#n-purchased').html(npurchased.toString());
        });
      }
    </script>
</body>

</html>