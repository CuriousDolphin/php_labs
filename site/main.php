<div class="container table-container">

  <!-- Content here -->
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Today Trip</h5>
      <?php
      $row = $GLOBALS['row'];
      $col = $GLOBALS['col'];
      $split = $col / 2;

      echo "<table class='table'>";
      for ($i = 0; $i < $row; $i++) {
        echo "<tr name='row' class='row'>";
        for ($j = 0; $j < $col; $j++) {
          if ($j != $split) {
            echo "<td class='cell ml free' id='i{$i}j{$j}' >
        </td>";
          } else {
            echo "<td class='cell ml-16 free' id='i{$i}j{$j}'></td>";
          }
        }
        echo "</tr>";
      }
      echo "</table>";
      ?>

      <?php if (isset($_SESSION['email'])) {
        echo "<button type='button' class='btn btn-primary'>Buy</button>";
      }
      ?>
    </div>
  </div>
</div>
<script type="text/javascript">
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
</script>