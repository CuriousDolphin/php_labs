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
      $row = $GLOBALS['row'];
      $col = $GLOBALS['col'];
      $split = $col / 2;
      $tickets = getTickets();
      $prenoted;
      $buyed;
      foreach ($tickets as $ticket) {
        $letter = strtolower($ticket['place']);
        $colConverted = ord($letter) - 97; //converte la lettera in nell'ascii corrispondente
        switch ($ticket['status']) {
          case 'prenoted':
            $prenoted[$ticket['row']][$colConverted] = 1;
            // $filtered[$ticket['row']][$col];
            break;
          case 'buyed':
            $buyed[$ticket['row']][$colConverted] = 1;
            break;
          default:
            break;
        }
      }
      $nprenoted = sizeof($prenoted);
      $nbuyed = sizeof($buyed);
      $total = $col * $row;
      echo "<p class='card-text no-margin'>Total Seat: $total </p>";
      echo "<p class='card-text no-margin'>Prenoted: $nprenoted </p>";
      echo "<p class='card-text no-margin'>Buyed: $nbuyed </p>";
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
          if (isset($prenoted[$i][$j])) echo "<td class='cell ml prenoted' id='i{$i}j{$j}'</td>";
          else if (isset($buyed[$i][$j])) echo "<td class='cell ml buyed' id='i{$i}j{$j}'</td>";
          else echo "<td class='cell ml free' id='i{$i}j{$j}' ></td>";

          if ($j == $split) {
            echo "<div class='margin-x'></div>";
          }
        }
      }
      echo "</tr>";
      echo "</table>";
      ?>

      <?php if (isset($_SESSION['email'])) {
        echo "<button type='button' class='btn btn-primary'>Buy</button>";
      }
      ?>
    </div>
  </div>
</div>
<!-- <script type="text/javascript">
    $.ajax({
      url: "functions.php",
      type: "POST",
      data: {
        api: "getTickets"
      }
    }).done(function(evt) {
      console.log(JSON.parse(evt));
      var tickets = JSON.parse(evt);
      var str, converted;
      for (var ind in tickets) {
        converted = tickets[ind].place.toString().toLowerCase().charCodeAt(0) - 97;
        /* USED TO CONVERT CHAR TO NUMBER */

        str = "#i" + tickets[ind].row + 'j' + converted;
        switch (tickets[ind].status) {
          case "buyed":
            $(str).removeClass('free');
            $(str).addClass('buyed');
            break;

          case "prenoted":
            $(str).removeClass('free');
            $(str).addClass('prenoted');
            break;

          default:
            break;
        }


      }
      console.log('tickets:', tickets);
    });
  </script> -->