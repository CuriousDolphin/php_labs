<?php include('functions.php');

checkHttps();
checkCookie();
if (isset($_SESSION['email'])) {
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
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
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


    <div class="container form-container">
      <div class="card form-card">
        <i class="fas fa-plane-departure logo"></i>
        <form method="post" action="login.php">
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" name="email" placeholder="Enter email" required>

          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Insert your password" required />
          </div>
          <button type="submit" class="btn btn-primary" name="app_login" id="app_login">Submit</button>
        </form>
      </div>
      <div class="error-container container">
        <!-- ERRORS -->
        <?php if (isset($_GET['msg'])) {

          echo "<div class='alert alert-danger' role='alert'>Timeout Session expired,you need to login again</div>";
        } ?>

        <?php if (count($errors) > 0) : ?>
          <?php echo "<div class='alert alert-danger' role='alert'>" ?>
          <?php foreach ($errors as $error) : ?>
            <?php echo "<p>$error</p>" ?>
          <?php endforeach ?>
          <?php echo "</div>" ?>
        <?php endif ?>
      </div>
    </div>
  </div>

  </div>

</body>

</html>