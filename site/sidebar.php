<div class="sidebar-wrapper col-md-2">
  <nav class="sidebar">

    <!-- Links -->
    <ul class="nav flex-column">
      <?php
      if (isset($_SESSION['email'])) {
        echo "
            <li class='nav-item'>
              <a class='nav-link' href='logout.php'>Logout</a>
            </li>
      		";
      } else {
        echo "
    				<li class='nav-item'>
      		  		<a class='nav-link' href='login.php'>Login</a>
      			</li>
      			<li class='nav-item'>
      		  		<a class='nav-link' href='register.php'>Signup</a>
      			</li>
      		";
      }
      ?>
    </ul>
  </nav>
</div>