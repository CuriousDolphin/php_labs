<div class="container table-container">

  <!-- Content here -->
  <div class="card mb-3">
    <div class="card-header">
      <h5 class="card-title">Today Trip
        <?php if (isset($_SESSION['email'])) {
          echo "<button type='button' class='btn btn-link'><a class='icon-white' href='login.php'><i class='fas fa-sync-alt'></i></a></button> ";
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
      $reserved;
      $purchased;
      foreach ($tickets as $ticket) {
        $letter = strtolower($ticket['place']);
        $colConverted = ord($letter) - 97; //converte la lettera in nell'ascii corrispondente
        switch ($ticket['status']) {
          case 'reserved':
            if (isset($_SESSION['email'])) {
              $mail = strtolower($ticket['owner_email']);
              $reserved[$ticket['row']][$colConverted] = $mail;
            } else {
              $reserved[$ticket['row']][$colConverted] = 'hidden';
            }
            // $filtered[$ticket['row']][$col];
            break;
          case 'purchased':
            $purchased[$ticket['row']][$colConverted] = 'hidden';
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
                  if (isset($_SESSION['email'])) {
                    if ($reserved[$i][$j] === $myMail) {
                      echo "<td class='cell ml reserved-by-me clickable' (click)='reserve()' id='i{$i}j{$j}'</td>"; //reserved by me -> yellow 
                    } else echo "<td class='cell ml reserved clickable' (click)='reserve()' id='i{$i}j{$j}'</td>"; //reserved but not by me
                  } else {
                    echo "<td class='cell ml reserved clickable' id='i{$i}j{$j}'</td>"; //hidden user not logged
                  }
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
      $nreserved = sizeof($reserved);
      $npurchased = sizeof($purchased);
      $total = $col * $row;
      $free = $total - ($nreserved + $npurchased);
      echo "<p class='card-text no-margin mb-1'>Total Seat: $total </p>";
      echo "<p class='card-text no-margin mb-1'> <span class='cell ml free mr-1' ></span class='n-free'> free: $free  </p>";
      echo "<p class='card-text no-margin mb-1'> <span class='cell ml reserved mr-1' ></span class='n-reserved'> reserved: $nreserved </p>";
      echo "<p class='card-text no-margin mb-1'><span class='cell ml purchased mr-1'></span class='n-purchased'> purchased: $npurchased </p>";

      ?>

      <?php if (isset($_SESSION['email'])) {
        echo "<button type='button' class='btn btn-primary'>Buy</button>";
      }
      ?>
    </div>
  </div>

  <div class='alert' role='alert' id='alert'></div>

</div>
<script type="text/javascript">
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
      var str, converted;
      var npurchased = 0;
      var nreserved = 0;
      var ntot = tickets.lenght
      for (var ind in tickets) {
        converted = tickets[ind].place.toString().toLowerCase().charCodeAt(0) - 97;
        /* USED TO CONVERT CHAR TO NUMBER */
        str = "#i" + tickets[ind].row + 'j' + converted;
        switch (tickets[ind].status) {
          case "purchased":
            npurchased++;
            $(str).removeClass('free');
            $(str).removeClass('reserved');
            $(str).addClass('purchased');
            break;

          case "reserved":
            nreserved++;
            if (userMail) { //reserved by me
              if (userMail === tickets[ind]['owner_email']) {
                $(str).removeClass('free');
                $(str).addClass('reserved-by-me');
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
      $('#n_reserved').text(nreserved.toString());
      $('#n_purchased').text(npurchased.toString());
      console.log('tickets:', tickets);
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
      res = JSON.parse(evt);
      console.log('reserve result', JSON.parse(evt));
      if (res['error']) {
        console.log('error', res['error']);
        $('#alert').text(res['error']);
        $('#alert').addClass('alert-danger');

        $("#alert").fadeToggle()


      }
      if (res['done']) {

        console.log('done! updating seats');
        $('#alert').text(res['done']);
        $('#alert').addClass('alert-success');

        $("#alert").fadeToggle()

        mail = res['email']
        getTickets(mail);
      }
      //return JSON.parse(evt);
      //console.log(JSON.parse(evt));
    })

  }
</script>