<html>

<head>
  <title>HMS</title>
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
  <link rel="stylesheet" type="text/css" href="style1.css">
  <link rel="stylesheet" type="text/css" href="style3.css">
  <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
  <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous"> -->

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

  <style>
    .form-control {
      border-radius: 0.75rem;
    }
  </style>

  <script>
    var check = function() {
      if (document.getElementById('password').value ==
        document.getElementById('cpassword').value) {
        document.getElementById('message').style.color = '#5dd05d';
        document.getElementById('message').innerHTML = 'Matched';
      } else {
        document.getElementById('message').style.color = '#f55252';
        document.getElementById('message').innerHTML = 'Not Matching';
      }
    }

    function alphaOnly(event) {
      var key = event.keyCode;
      return ((key >= 65 && key <= 90) || key == 8 || key == 32);
    };

    function checklen() {
      var pass1 = document.getElementById("password");
      if (pass1.value.length < 6) {
        alert("Password must be at least 6 characters long. Try again!");
        return false;
      }
    }
  </script>

</head>

<!------ Include the above in your HEAD tag ---------->

<body style="background: -webkit-linear-gradient(left, #8A2387, #F27121);">
  <!-- NavBar Start -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <div class="container">

      <a class="navbar-brand js-scroll-trigger" href="#" style="margin-top: 10px;margin-left:-65px;font-family: 'IBM Plex Sans', sans-serif;">
        <h2><img src="https://dpu.edu.in/img/logo.png" style="height:50px;"></h2>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item" style="margin-right: 40px;">
            <a class="nav-link js-scroll-trigger" href="index.php" style="color: white;font-family: 'IBM Plex Sans', sans-serif;">
              <h6>LOGIN</h6>
            </a>
          </li>
          <li class="nav-item" style="margin-right: 40px;">
            <a class="nav-link js-scroll-trigger" href="patient_register.php" style="color: white;font-family: 'IBM Plex Sans', sans-serif;">
              <h6>REGISTER</h6>
            </a>
          </li>

          <li class="nav-item" style="margin-right: 40px;">
            <a class="nav-link js-scroll-trigger" href="services.html" style="color: white;font-family: 'IBM Plex Sans', sans-serif;">
              <h6>ABOUT US</h6>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="contact.html" style="color: white;font-family: 'IBM Plex Sans', sans-serif;">
              <h6>CONTACT</h6>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- NavBar End -->
  <div class="area">
    <ul class="circles">

      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>

    </ul>
    <div class="container register" style="font-family: 'IBM Plex Sans', sans-serif; ">
      <div class="row">
        <div class="col-md-3 register-left" style="margin-top: 10%;right: 5%">
          <!-- <img src="https://image.ibb.co/n7oTvU/logo_white.png" alt=""/> -->
          <h3>Wireless HealthCare System using IoMT with integration of Big Data</h3>

        </div>

        <div class="col-md-9 ">


          <div class="card-body" style="font-family: 'IBM Plex Sans', sans-serif; background: white;border-radius: 50px; padding:0px">
            <i class="fa fa-hospital-o fa-3x" aria-hidden="true" style="color:#0062cc"></i>
            <br>
            <div class="row  m-5 ">
              <center>
                <h3>Patient Register</h3>
              </center>
              <form method="post" action="func2.php">
                <div class="row register-form">
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="text" class="form-control" placeholder="First Name *" name="fname" onkeydown="return alphaOnly(event);" required />
                    </div>
                    <div class="form-group">
                      <input type="email" class="form-control" placeholder="Your Email *" name="email" />
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control" placeholder="Password *" id="password" name="password" onkeyup='check();' required />
                    </div>

                    <div class="form-group">
                      <div class="maxl">
                        <label class="radio inline">
                          <input type="radio" name="gender" value="Male" checked>
                          <span> Male </span>
                        </label>
                        <label class="radio inline">
                          <input type="radio" name="gender" value="Female">
                          <span>Female </span>
                        </label>
                      </div>
                      <a href="index.php">Already have an account?</a>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="text" class="form-control" placeholder="Last Name *" name="lname" onkeydown="return alphaOnly(event);" required />
                    </div>

                    <div class="form-group">
                      <input type="tel" minlength="10" maxlength="10" name="contact" class="form-control" placeholder="Your Phone *" />
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control" id="cpassword" placeholder="Confirm Password *" name="cpassword" onkeyup='check();' required /><span id='message'></span>
                    </div>
                    <input type="submit" class="btnRegister" name="patsub1" onclick="return checklen();" value="Register" />
                  </div>

                </div>
              </form>

            </div>
          </div>
        </div>
        <!-- Tab Bar Start -->
      </div>
    </div>
  </div>




</body>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

</html>