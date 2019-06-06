<?php include('functions.php');

checkHttps();
checkCookie();

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
      <h2>Insert your data to signup</h2>
      <form>
        <div class="form-group">
          <label for="email">Email address</label>
          <input type="email" class="form-control" name="email" placeholder="Enter email" required>

        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Insert your password" required />
        </div>
        <!-- <div class="form-group">
          <label for="password">Repeat password</label>
          <input type="password" class="form-control" name="password-repeat" id="password-repeat" placeholder="repeat your password" required />
        </div> -->
        <!-- <button type="submit" class="btn btn-primary" name="app_signup" id="app_signup">Submit</button> -->

      </form>
      <button onclick="validate()">asdasd</button>
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
      <div class='alert' role='alert' id='alert'></div>
    </div>
  </div>
  </div>

</body>
<script type="text/javascript">
  function validate() {
    var psw = $("#password").val();

    str = "(?=.*[a-z])(?=.*[A-Z0-9])"
    var regexp_psw = new RegExp(str);
    console.log(regexp_psw, psw);
    if (!regexp_psw.test(psw)) { //error
      //alert('error password');
      $("#alert").html("The password must contain at least one lowercase char and one number OR one uppercase char");
      $('#alert').addClass('alert-danger');
      $('#alert').fadeIn().delay(1000).fadeOut();
      return false;
    }
    $('#alert').removeClass('alert-danger');
    return true;
  }
</script>

</html>

</html>