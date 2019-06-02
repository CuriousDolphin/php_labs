<div class="container table-container">

  <!-- Content here -->
  <?php
  $row = $GLOBALS['row'];
  $col = $GLOBALS['col'];
  $split = $col / 2;
  echo "<table class='table'>";
  for ($i = 0; $i < $row; $i++) {
    echo "<tr name='row' class='row'>";
    for ($j = 0; $j < $col; $j++) {
      if ($j != $split) {
        echo "<td class='cell ml' ></td>";
      } else {
        echo "<td class='cell ml-16' ></td>";
      }
    }
    echo "</tr>";
  }
  echo "</table>";
  ?>

</div>