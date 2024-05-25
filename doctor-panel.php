<!DOCTYPE html>
<?php
require 'vendor/autoload.php';
include('func1.php');
include('generate_prescription.php');
$dash_appointments = 0;
$dash_prescriptions = 0;
$doctor = $_SESSION['dname'];
$demail = $_SESSION['demail'];
$dspecialist = $_SESSION['dspecialist'];
$docfee = $_SESSION['dfee'];
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
//var_dump($spreadsheet);
$dash_patient = getPatients();
$dash_appointments = getAppointments();
$dash_prescriptions = getPrescription();

function getPatients()
{
  global $service;
  global $spreadsheetId;
  global $doctor;
  $range = 'Appointments';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $array = [];
  if ($rows) {
    foreach ($rows as $row) {
      if ($row[9] == $doctor) {
        array_push($array, $row[2]);
      }
    }
  }
  $arr = array_unique($array);

  $range = 'registrations';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $result = [];
  if ($rows) {
    foreach ($arr as $pid) {
      foreach ($rows as $row) {
        if ($row[0] == $pid) {
          array_push($result, $row);
          break;
        }
      }
    }
  }

  return $result;
}

function getAppointments()
{
  global $service;
  global $spreadsheetId;
  global $doctor;
  $range = 'Appointments';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $array = [];
  if ($rows) {
    foreach ($rows as $row) {
      if ($row[9] == $doctor) {
        array_push($array, $row);
      }
    }
  }
  return $array;
}


function getPrescription()
{
  global $service;
  global $spreadsheetId;
  global $doctor;
  $range = 'Prescriptions';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $array = [];
  if ($rows) {
    foreach ($rows as $row) {
      if ($row[2] == $doctor) {
        array_push($array, $row);
      }
    }
  }
  return $array;
}


if (isset($_GET['cancel'])) {
  $range = 'Appointments'; // the service will detect the last row of this sheet
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $options = ['valueInputOption' => 'USER_ENTERED'];
  for ($i = 1; $i <= sizeof($rows); $i++) {
    if ($rows[$i][1] == $_GET['ID']) {
      $body = new \Google_Service_Sheets_ValueRange(['values' => [["Cancelled by Doctor"]]]);

      $params = ['valueInputOption' => 'USER_ENTERED'];
      $service->spreadsheets_values->update($spreadsheetId, $range . '!N' . ($i + 1), $body, $params);
      echo "<script>alert('Your appointment successfully cancelled');</script>";
      break;
    }
  }
}

if (isset($_GET['prescribe'])) {

  $pid = $_GET['pid'];
  $ID = $_GET['ID'];
  $appdate = $_GET['appdate'];
  $apptime = $_GET['apptime'];
  $disease = $_GET['disease'];
  $allergy = $_GET['allergy'];
  $prescription = $_GET['prescription'];
  echo "<script>alert('Prescribed successfully!');</script>";
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
          <span id="lblTitle" class="hidden-xs" style="color:White;font-size:X-Large;">Hospital Management System | Doctor Portal</span>
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
            <a href="#" class="d-block" style="color:white"><?php echo $doctor ?> </a>
          </div>
        </div>
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <i class="nav-icon fas fa-id-card-alt"></i>
          </div>
          <div class="info">
            <a href="#" class="d-block" style="color:white">Specialist : <?php echo $dspecialist ?> </a>
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
                  Dashboard
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-patient" id="list-patient-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fa fa-user"></i>
                <p>
                  Patients
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_patient) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#app-hist" id="list-pat-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fas fa-notes-medical"></i>
                <p>
                  Appointments
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_appointments) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-pres" id="list-pres-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fas fa-file-prescription"></i>
                <p>
                  Prescriptions
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_prescriptions) ?></span>
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
                  <h1 class="m-0">Dashboard</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
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

                <div class="col-12 col-sm-6 col-md-3">
                  <div class="small-box bg-info">
                    <div class="inner">
                      <h3>
                        <?php
                        echo sizeof($dash_patient);
                        ?></h3>

                      <p>Patients</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-user"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                      More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                  </div>
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-3">
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3><?php
                          echo sizeof($dash_appointments);
                          ?></h3>

                      <p>Appointments</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-notes-medical"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                      More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-3">
                  <!-- small card -->
                  <div class="small-box bg-warning">
                    <div class="inner">
                      <h3>
                        <?php
                        echo sizeof($dash_prescriptions)
                        ?></h3>

                      <p>Prescriptions</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-file-prescription"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                      More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-3">
                  <!-- small card -->
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>
                        <?php
                        echo 0;
                        ?></h3>

                      <p>Notifications</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-bell"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                      More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix hidden-md-up"></div>
                <!-- /.col -->
              </div>
              <!-- /.row -->


              <!-- Main row -->
              <div class="row">
                <!-- Left col -->
                <div class="col-md-8">
                  <!-- MAP & BOX PANE -->
                  <!-- TABLE: LATEST ORDERS -->
                  <div class="card">
                    <div class="card-header border-transparent">
                      <h3 class="card-title">Doctor Details</h3>

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
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table m-0">

                          <tbody>
                            <tr>
                              <td>
                                Name
                              </td>
                              <td>
                                <?php echo $doctor ?>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                Email
                              </td>
                              <td>
                                <?php echo $demail ?>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                Specialist
                              </td>
                              <td>
                                <?php echo $dspecialist ?>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                Fee
                              </td>
                              <td>
                                <?php echo $docfee ?>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <!-- /.table-responsive -->
                    </div>
                    <!-- /.card-footer -->
                  </div>
                  <!-- /.card -->

                </div>
                <!-- /.col -->

                <div class="col-md-4">
                  <!-- Info Boxes Style 2 -->
                  <div class="info-box mb-3 bg-warning">
                    <span class="info-box-icon"><i class="fas fa-tag"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Indian Population</span>
                      <span class="info-box-number">140.76 Crore(2021)</span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                  <div class="info-box mb-3 bg-success">
                    <span class="info-box-icon"><i class="far fa-heart"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Death Rate</span>
                      <span class="info-box-number">9.1 per 1,000 people.</span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                  <div class="info-box mb-3 bg-danger">
                    <span class="info-box-icon"><i class="	fa fa-user-md"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Doctors Ratio</span>
                      <span class="info-box-number">1 doctor for every 1,000 people</span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                  <div class="info-box mb-3 bg-info">
                    <span class="info-box-icon"><i class="far fa-comment"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Gender Ratio</span>
                      <span class="info-box-number">943</span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->

                  <!-- PRODUCT LIST -->
                </div>
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



      <!-- PATIENTS -->
      <div class="tab-pane fade" id="list-patient" role="tabpanel" aria-labelledby="list-patient-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Patients</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Patients</li>
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

              <!-- Main row -->
              <div class="row">
                <!-- Left col -->
                <div class="col-12">
                  <!-- /.row -->

                  <!-- TABLE: LATEST ORDERS -->
                  <div class="card">
                    <div class="card-header border-transparent">
                      <h3 class="card-title">Patient Details</h3>

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
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table m-0">
                          <thead>
                            <tr>
                              <th scope="col">Patient ID</th>
                              <th scope="col">First Name</th>
                              <th scope="col">Last Name</th>
                              <th scope="col">Gender</th>
                              <th scope="col">Email</th>
                              <th scope="col">Contact</th>

                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            // Fetch the rows

                            $rows = $dash_patient;
                            if ($rows) {
                              foreach ($rows as $row) { {
                            ?>
                                  <tr>
                                    <td>
                                      <?php echo $row[0]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[1]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[2]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[4]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[5]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[3]; ?>
                                    </td>
                                  </tr>
                            <?php }
                              }
                            } ?>
                            <!--  -->
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /PATIENTS -->

      <!-- APPOINTMENTS -->
      <div class="tab-pane fade" id="app-hist" role="tabpanel" aria-labelledby="app-hist-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Appointment History</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Appointment History</li>
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

              <!-- Main row -->
              <div class="row">
                <!-- Left col -->
                <div class="col-12">
                  <!-- /.row -->

                  <!-- TABLE: LATEST ORDERS -->
                  <div class="card">
                    <div class="card-header border-transparent">
                      <h3 class="card-title">Appointment History</h3>

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
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table m-0">
                          <thead>
                            <tr>
                              <th scope="col">Appointment ID</th>
                              <th scope="col">Patient ID</th>
                              <th scope="col">First Name</th>
                              <th scope="col">Last Name</th>
                              <th scope="col">Gender</th>
                              <th scope="col">Email</th>
                              <th scope="col">Contact</th>
                              <th scope="col">Appointment Date</th>
                              <th scope="col">Appointment Time</th>
                              <th scope="col">Current Status</th>
                              <th scope="col">Action</th>
                              <th scope="col">Prescribe</th>

                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            // Fetch the rows

                            $rows = $dash_appointments;
                            if ($rows) {
                              foreach ($rows as $row) {
                                if ($row[9] == $doctor) {
                            ?>
                                  <tr>
                                    <td>
                                      <?php echo $row[1]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[2]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[3]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[4]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[6]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[7]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[5]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[11]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[12]; ?>
                                    </td>
                                    <td>
                                      <?php echo $row[13]; ?>
                                    </td>

                                    <td>
                                      <?php if (($row[13] == "Active")) { ?>
                                        <a href="doctor-panel.php?ID=<?php echo $row[1] ?>&cancel=update" onClick="return confirm('Are you sure you want to cancel this appointment ?')" title="Cancel Appointment" tooltip-placement="top" tooltip="Remove"><button class="btn btn-danger">Cancel</button></a>
                                      <?php } else if (str_contains($row[13], 'Prescribed')) {
                                        echo '-';
                                      } else echo "Cancelled";

                                      ?>

                                    </td>

                                    <td>

                                      <?php if (($row[13] == "Active")) { ?>
                                        <a href="prescribe.php?pid=<?php echo $row[2] ?>&ID=<?php echo $row[1] ?>&fname=<?php echo $row[3] ?>&lname=<?php echo $row[4] ?>&appdate=<?php echo $row[11] ?>&apptime=<?php echo $row[12] ?>" tooltip-placement="top" tooltip="Remove" title="prescribe">
                                          <button class="btn btn-success">Prescibe</button></a>
                                      <?php } else if (str_contains($row[13], 'Prescribed')) {
                                        echo '<form method="get">
                                        <a href="generate_prescription.php?ID=' . $row[1] . '" target="_blank">
                                          <input type="hidden" name="ID" value="' . $row[1] . '" />
                                          <input type="submit" name="generate_pres" class="btn btn-success" value="Download" />
                                        </a>
                                      </form>';
                                      } else echo "Cancelled"; ?>

                                    </td>
                                  </tr>
                            <?php }
                              }
                            } ?>
                            <!--  -->
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /APPOINTMENTS -->


      <!-- PRESCRIPTIONS -->
      <div class="tab-pane fade" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Prescriptions</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Prescriptions</li>
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
              <!-- Main row -->
              <div class="row">
                <!-- Left col -->
                <div class="col-12">
                  <div class="card">
                    <div class="card-header border-transparent">
                      <h3 class="card-title">Prescriptions</h3>

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
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table m-0">
                          <thead>
                            <tr>
                              <th>Prescribed On</th>
                              <th>Appointment ID</th>
                              <th>Patient ID</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Appointment Date</th>
                              <th>Appointment Time</th>
                              <th>Disease</th>
                              <th>Allergy</th>
                              <th>Prescribe</th>
                              <th>Download</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $rows = $dash_prescriptions;
                            $array = [];
                            foreach ($rows as $row) {
                              if ($row[2] == $doctor) {
                            ?>
                                <tr>
                                  <td>
                                    <?php echo $row[0]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[1]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[3]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[4]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[5]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[6]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[7]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[8]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[9]; ?>
                                  </td>
                                  <td>
                                    <?php echo $row[10]; ?>
                                  </td>
                                  <td>
                                    <form method="get">
                                      <a href="generate-prescription.php?ID=<?php echo $row[1] ?>" target="_blank">
                                        <input type="hidden" name="ID" value="<?php echo $row[1] ?>" />
                                        <input type="submit" name="generate_pres" class="btn btn-success" value="Download" />
                                      </a>
                                    </form>
                                  </td>
                                </tr>
                            <?php }
                            } ?>

                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /PRESCRIPTIONS -->





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

</body>

</html>