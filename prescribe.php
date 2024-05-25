<!DOCTYPE html>
<?php
include('func1.php');
$client = new \Google_Client();
$client->setApplicationName('Google Sheets API');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
// credentials.json is the key file we downloaded while setting up our Google Sheets API
$path = 'credentials.json';
$client->setAuthConfig($path);
// configure the Sheets Service
$service = new \Google_Service_Sheets($client);
// the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
$spreadsheetId = '1VEVOIKqIWC7CTGTPGrdj27JuAiufwDUQl6Zht-cnvE0';
$spreadsheet = $service->spreadsheets->get($spreadsheetId);

$pid = '';
$ID = '';
$appdate = '';
$apptime = '';
$fname = '';
$lname = '';
$doctor = $_SESSION['dname'];
if (isset($_GET['pid']) && isset($_GET['ID']) && ($_GET['appdate']) && isset($_GET['apptime']) && isset($_GET['fname']) && isset($_GET['lname'])) {
  $pid = $_GET['pid'];
  $ID = $_GET['ID'];
  $fname = $_GET['fname'];
  $lname = $_GET['lname'];
  $appdate = $_GET['appdate'];
  $apptime = $_GET['apptime'];
}



if (isset($_POST['prescribe']) && isset($_POST['pid']) && isset($_POST['ID']) && isset($_POST['appdate']) && isset($_POST['apptime']) && isset($_POST['lname']) && isset($_POST['fname'])) {
  $appdate = $_POST['appdate'];
  $apptime = $_POST['apptime'];
  $disease = $_POST['disease'];
  $allergy = $_POST['allergy'];
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $pid = $_POST['pid'];
  $appID = $_POST['ID'];
  $prescription = $_POST['prescription'];
  date_default_timezone_set('Asia/Kolkata');
  $prescribed_on = date('m/d/Y H:i:s');

  $newRow = [$prescribed_on, $appID, $doctor, $pid, $fname, $lname, $appdate, $apptime, $disease, $allergy, $prescription];
  $rows = [$newRow]; // you can append several rows at once
  $valueRange = new \Google_Service_Sheets_ValueRange();
  $valueRange->setValues($rows);


  $range1 = 'Prescriptions'; // the service will detect the last row of this sheet
  $options = ['valueInputOption' => 'USER_ENTERED'];
  $service->spreadsheets_values->append($spreadsheetId, $range1, $valueRange, $options);
  echo "<script>alert('Prescribed successfully!');</script>";


  $range2 = 'Appointments'; // the service will detect the last row of this sheet
  $response = $service->spreadsheets_values->get($spreadsheetId, $range2);
  $rows = $response->getValues();
  $options = ['valueInputOption' => 'USER_ENTERED'];
  for ($i = 1; $i <= sizeof($rows); $i++) {
    if ($rows[$i][1] == $appID) {
      $body = new \Google_Service_Sheets_ValueRange(['values' => [["Prescribed on " . $prescribed_on]]]);
      $params = ['valueInputOption' => 'USER_ENTERED'];
      $service->spreadsheets_values->update($spreadsheetId, $range2 . '!N' . ($i + 1), $body, $params);
      break;
    }
  }
}

?>



<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HMS</title>
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark" style="background:linear-gradient(to right, #962827, #cc4a49, #942e2d)">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <span id="lblTitle" class="hidden-xs" style="color:White;font-size:X-Large;">Hospital Management System | Doctor Portal(Prescribe)</span>
        </li>
      </ul>
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Full Screen Menu -->
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="doctor-panel.php"><i class="fas fa-sign-out-alt"></i> Back</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout1.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background:linear-gradient(to right, #972928, #b93231, #9f2c2b) ">
      <!-- Brand Logo -->
      <a href="#" class="brand-link bg-white ">
        <img src="https://dpu.edu.in/img/logo.png" alt="AdminLTE Logo" class="brand-image ml-lg-3">
      </a>
      <!-- Sidebar -->
      <div class="sidebar" style="background: url(https://engg.dpuerp.in/assets/images/user-img-background.jpg) no-repeat no-repeat">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block" style="color:white">Doctor</a>
          </div>
        </div>
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <i class="nav-icon fas fa-id-card-alt"></i>
          </div>
          <div class="info">
            <a href="#" class="d-block" style="color:white">Doctor Portal(Prescribe)</a>
          </div>
        </div>
        <!-- SidebarSearch Form -->
        <div class="form-inline">
          <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </div>
          </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item ">
              <a href="#list-dash" id="list-dash-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  Prescribe
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>


    <!-- TAB BAR START -->
    <div class="tab-content" id="nav-tabContent">

      <!-- DASHBOARD -->
      <div class="tab-pane fade  show active" id="list-dash" role="tabpanel" aria-labelledby="list-dash-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Prescribe</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Prescribe</li>
                  </ol>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
          </div>
          <!-- /.content-header -->

          <!-- Main content -->
          <section class="content">
            <div class="container-fluid">
              <!-- Info boxes -->
              <div class="row">

                <div class="col-12">
                  <div class="card card-primary card-outline">
                    <div class="card-header border-transparent">
                      <h3 class="card-title text-center">Prescribe</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <form class="form-group" method="post" action="prescribe.php" name="prescribeform">
                        <div class="row">

                          <div class="col-md-4">
                            <label>Appointment ID: </label>
                          </div>
                          <div class="col-md-8">
                            <label><?php echo $ID  ?> </label>

                          </div>
                          <br><br>

                          <div class="col-md-4">
                            <label>PID: </label>
                          </div>
                          <div class="col-md-8">
                            <label><?php echo $pid  ?> </label>

                          </div>
                          <br><br>

                          <div class="col-md-4">
                            <label>Patient Name:</label>
                          </div>
                          <div class="col-md-8">
                            <label><?php echo $fname . " " . $lname ?> </label>

                          </div>

                          <br><br>

                          <div class="col-md-4"><label for="consultancyfees">
                              Disease:
                            </label></div>
                          <div class="col-md-8">
                            <!-- <div id="docFees">Select a doctor</div> -->
                            <textarea id="disease" cols="86" rows="5" name="disease" required></textarea>
                          </div><br><br>
                          <div class="col-md-4"><label for="consultancyfees">
                              Allergies:
                            </label></div>
                          <div class="col-md-8">
                            <!-- <div id="docFees">Select a doctor</div> -->
                            <textarea id="allergy" cols="86" rows="5" name="allergy" required></textarea>
                          </div><br><br>

                          <div class="col-md-4"><label for="consultancyfees">
                              Prescription:
                            </label></div>
                          <div class="col-md-8">
                            <!-- <div id="docFees">Select a doctor</div> -->
                            <textarea id="prescription" cols="86" rows="10" name="prescription" required></textarea>
                          </div><br><br>

                          <input type="hidden" name="fname" value="<?php echo $fname ?>" />
                          <input type="hidden" name="lname" value="<?php echo $lname ?>" />
                          <input type="hidden" name="appdate" value="<?php echo $appdate ?>" />
                          <input type="hidden" name="apptime" value="<?php echo $apptime ?>" />
                          <input type="hidden" name="pid" value="<?php echo $pid ?>" />
                          <input type="hidden" name="ID" value="<?php echo $ID ?>" />
                          <br><br><br><br>
                          <div class="col-md-4">
                            <input type="submit" name="prescribe" value="Prescribe" class="btn btn-primary" id="inputbtn">
                          </div>
                          <div class="col-md-8"></div>
                        </div>
                      </form>
                    </div>
                  </div>

                </div>

                <!-- fix for small devices only -->
                <div class="clearfix hidden-md-up"></div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /DASHBOARD -->





    </div>
    <!-- TAB BAR START -->

    <!-- Content Wrapper. Contains page content -->

    <!-- /.content-wrapper -->

  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4-->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>

  <script>
    // HEART RATE DOUGHNUT CHART
    const heartrate_doughnut = document.getElementById('heartrate_doughnut')
    var heartrate_donutData = {
      labels: [
        'Low',
        'Normal',
        'High',
      ],
      datasets: [{
        data: [<?php echo $heart_low ?>, <?php echo $heart_normal ?>, <?php echo $heart_high ?>],
        backgroundColor: ['yellow', 'green', 'red'],
      }]
    }
    var heartrate_donutOptions = {
      maintainAspectRatio: false,
      responsive: true,
    }
    new Chart(heartrate_doughnut, {
      type: 'doughnut',
      data: heartrate_donutData,
      options: heartrate_donutOptions
    })


    // Body TEMPERATURE DOUGHNUT CHART
    const bodyTemp_doughnut = document.getElementById('bodyTemp_doughnut')
    var bodyTemp_donutData = {
      labels: [
        'Cessation of cardiac and Respiratory functions',
        'Severe Hypothermia',
        'Hypothermia',
        'Normal',
        'Pyrexia',
        'Hyper Pyrexia',
        'Irreversiblle cell damage and death',
      ],
      datasets: [{
        data: <?php echo $bodytemp_results ?>,
        backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', 'black'],
      }]
    }
    var bodyTemp_donutOptions = {
      maintainAspectRatio: false,
      responsive: true,
    }
    new Chart(bodyTemp_doughnut, {
      type: 'doughnut',
      data: heartrate_donutData,
      options: heartrate_donutOptions
    })
  </script>
</body>

</html>