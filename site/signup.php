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


    <div class="container form-container">
      <div class="card form-card">
        <i class="fas fa-plane-departure logo"></i>
        <h2>Insert your data to signup</h2>
        <form method="post" action="signup.php" >
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" name="email" placeholder="Enter email" required>

          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input oninput="check(this)" name="password" type="password" class="form-control" name="password" id="password" placeholder="Insert your password" required />
            <br>
          </div>

          <br>

          <button type="submit" name="app_signup" value="app_signup" id="app_signup" class="btn btn-primary">Submit</button>

        </form>
      </div>

      <div class="error-container container" >
        <!-- ERRORS -->


        <?php if (count($errors) > 0) : ?>
          <?php echo "<div class='alert alert-danger' role='alert' id='getAlert'>" ?>
          <?php foreach ($errors as $error) : ?>
            <?php echo $error ?>
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
  $('#getAlert').delay(2000).fadeOut();
  
  function check(input) {
    str = "(?=.*[a-z])(?=.*[A-Z0-9])"
    var regexp_psw = new RegExp(str);
    if (!regexp_psw.test(input.value.toString())) {
      input.setCustomValidity("The password must contain at least one lowercase char and one number OR one uppercase char");
   } else {
      // input is fine -- reset the error message
      input.setCustomValidity("");
    }
  }
  function validate(event) {
    var psw = $("#password").val();

    str = "(?=.*[a-z])(?=.*[A-Z0-9])"
    var regexp_psw = new RegExp(str);
    console.log(regexp_psw, psw);
    if (!regexp_psw.test(psw)) { //error
      // alert('error password');
      event.preventDefault();
      event.stopPropagation();
      $("#alert").html("The password must contain at least one lowercase char and one number OR one uppercase char");
      $('#alert').addClass('alert-danger');
      $('#alert').fadeIn().delay(1000).fadeOut();
      //return false;
    }
    $('#alert').removeClass('alert-danger');
    // return true;
  }
</script>

</html>

</html>