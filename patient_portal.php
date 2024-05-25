<!DOCTYPE html>
<?php

include('func.php');
include('generate_prescription.php');
// include('newfunc.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//require 'vendor/autoload.php';
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


$dash_appointments = getAppointments();
$dash_prescriptions = getPrescription();
$dash_all_records = getAllRecords();
$dash_heartrate = getHeartRate($dash_all_records);
$dash_bodytemp = getBodyTemp($dash_all_records);
$dash_alcohol = getAlcohol($dash_all_records);
$dash_medical_checkups = 3;
$dash_healthrecords = (int)(count($dash_heartrate) + count($dash_bodytemp) + count($dash_alcohol));


$pid = $_SESSION['pid'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$fname = $_SESSION['fname'];
$gender = $_SESSION['gender'];
$lname = $_SESSION['lname'];
$contact = $_SESSION['contact'];

function getAppointments()
{
  global $service;
  global $spreadsheetId;
  $range = 'Appointments';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $array = [];
  if ($rows) {
    foreach ($rows as $row) {
      if ($row[2] == $_SESSION['pid']) {
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
  $range = 'Prescriptions';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $array = [];
  if ($rows) {
    foreach ($rows as $row) {
      if ($row[3] == $_SESSION['pid']) {
        array_push($array, $row);
      }
    }
  }
  return $array;
}

function getAllRecords()
{
  global $service;
  global $spreadsheetId;
  $range = $_SESSION['pid'];
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  return $rows;
}


function getheartRate($dash_all_records)
{
  $rows = $dash_all_records;
  $array = [];
  if ($rows) {
    foreach ($rows as $row) {
      if ($row[7] == "HeartRate" && $row[8] != "Ready") {
        array_push($array, $row);
      }
    }
  }
  return $array;
}

function getBodyTemp($dash_all_records)
{
  $rows = $dash_all_records;
  $array = [];
  if ($rows) {
    foreach ($rows as $row) {
      if ($row[7] == "BodyTemp" && $row[8] != "Ready") {
        array_push($array, $row);
      }
    }
  }
  return $array;
}

function getAlcohol($dash_all_records)
{
  $rows = $dash_all_records;
  $array = [];
  if ($rows) {
    foreach ($rows as $row) {
      if ($row[7] == "BloodPressure" && $row[8] != "Ready") {
        array_push($array, $row);
      }
    }
  }
  return $array;
}

if (isset($_POST['app-submit'])) {
  $pid = $_SESSION['pid'];
  $username = $_SESSION['username'];
  $email = $_SESSION['email'];
  $fname = $_SESSION['fname'];
  $lname = $_SESSION['lname'];
  $gender = $_SESSION['gender'];
  $contact = $_SESSION['contact'];
  $doctor = $_POST['doctor'];
  # $fees=$_POST['fees'];
  $docFees = $_POST['docFees'];
  $specialization = $_POST['spec'];
  $appdate = $_POST['appdate'];
  $apptime = $_POST['apptime'];
  $cur_date = date("d-m-y");
  date_default_timezone_set('Asia/Kolkata');
  $cur_time = date("H:i:sa");
  $booking = date('m/d/Y H:i:s');
  $apptime1 = strtotime($apptime);
  $appdate1 = strtotime($appdate);
  $appointment_id = "A" . date("ydmhis");

  if (date("Y-m-d", $appdate1) >= $cur_date) {
    if ((date("Y-m-d", $appdate1) == $cur_date and date("H:i:s", $apptime1) > $cur_time) or date("Y-m-d", $appdate1) > $cur_date) {

      // $newRow = [$booking, $appointment_id, $pid, $fname, $lname, $contact, $gender, $email, $specialization, $doctor, $docFees, $appdate, $apptime, "Active"];
      $rows = [[$booking, $appointment_id, $pid, $fname, $lname, $contact, $gender, $email, $specialization, $doctor, $docFees, $appdate, $apptime, "Active"]]; // you can append several rows at once
      $valueRange = new \Google_Service_Sheets_ValueRange();
      $valueRange->setValues($rows);
      $range = 'Appointments'; // the service will detect the last row of this sheet
      $options = ['valueInputOption' => 'USER_ENTERED'];
      $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);

      $mail = new PHPMailer(true);
      //$mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;
      $mail->Username = 'rahulagarwal1126@gmail.com'; // YOUR gmail email
      $mail->Password = 'nyyradlrrlhiqbvq'; // YOUR gmail password
      // Sender and recipient settings
      $mail->setFrom('rahulagarwal1126@gmail.com', 'Rahul');
      $mail->addAddress($email, $fname . " " . $lname);
      $mail->addReplyTo('rahulagarwal1126@gmail.com', 'Rahul'); // to set the reply to

      // Setting the email content
      $mail->IsHTML(true);
      $mail->Subject = "Appointment Booked Successfully! Send email using Gmail SMTP and PHPMailer";
      $mail->Body = "
      <img src='https://dpu.edu.in/img/logo.png' style='height:50px;'>
        <h2>Wireless HealthCare System using IoMT with integration of Big Data</h2>
        <h2>Patient Details</h2>
        <h3>----------------------------</h3>
        <h3>Patient Details</h3>
        <h3>Patient ID : $pid </h3>
        <h3>Patient Name : $fname $lname </h3>
        <h3>Mobile No. : $contact  </h3>
        <h3>Gender : $gender  </h3>
        <h3>Email : $email </h3>
        <h3>----------------------------</h3>
        <h3>Doctor Details</h3>
        <h3>Appointment ID : $appointment_id </h3>
        <h3>Specialization : $specialization </h3>
        <h3>Doctr Name: $doctor </h3>
        <h3>Doctor Fee : $docFees  </h3>
        <h3>Appointment Date : $appdate  </h3>
        <h3>Appointment Time : $apptime </h3>
        <h3>----------------------------</h3>
        <p>Thankyou for Booking Appointment!! Have a Nice day.</p>
        <h3>Appointment Booked on $booking</h3>";
      $mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';
      $mail->send();

      echo "<script>alert('Your appointment successfully booked!! Mail Sent');</script>";
    } else {
      echo "<script>alert('Select a time or date in the future!');</script>";
    }
  } else {
    echo "<script>alert('Select a time or date in the future!');</script>";
  }
}


if (isset($_GET['cancel'])) {
  $range = 'Appointments'; // the service will detect the last row of this sheet
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $options = ['valueInputOption' => 'USER_ENTERED'];
  for ($i = 1; $i <= sizeof($rows); $i++) {
    if ($rows[$i][1] == $_GET['ID']) {
      $body = new \Google_Service_Sheets_ValueRange(['values' => [["Cancelled by Patient"]]]);

      $params = ['valueInputOption' => 'USER_ENTERED'];
      $service->spreadsheets_values->update($spreadsheetId, $range . '!N' . ($i + 1), $body, $params);
      echo "<script>alert('Your appointment successfully cancelled');</script>";
      break;
    }
  }
}


if (isset($_POST["detect_heart_rate"])) {
  date_default_timezone_set('Asia/Kolkata');
  $date_time = date('m/d/Y H:i:s');
  $pid = $_SESSION['pid'];
  $username = $_SESSION['username'];
  $email = $_SESSION['email'];
  $fname = $_SESSION['fname'];
  $lname = $_SESSION['lname'];
  $gender = $_SESSION['gender'];
  $contact = $_SESSION['contact'];
  $rows = [[$date_time, $pid, $fname, $lname, $email, $contact, $gender, "HeartRate", "Ready"]]; // you can append several rows at once
  $valueRange = new \Google_Service_Sheets_ValueRange();
  $valueRange->setValues($rows);
  $range = $pid; // the service will detect the last row of this sheet
  $options = ['valueInputOption' => 'USER_ENTERED'];
  $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
  echo "<script>alert('Ready to Detect Heart Rate!!');</script>";
}

if (isset($_POST["detect_body_temp"])) {
  date_default_timezone_set('Asia/Kolkata');
  $date_time = date('m/d/Y H:i:s');
  $pid = $_SESSION['pid'];
  $username = $_SESSION['username'];
  $email = $_SESSION['email'];
  $fname = $_SESSION['fname'];
  $lname = $_SESSION['lname'];
  $gender = $_SESSION['gender'];
  $contact = $_SESSION['contact'];
  $rows = [[$date_time, $pid, $fname, $lname, $email, $contact, $gender, "BodyTemp", "Ready"]]; // you can append several rows at once
  $valueRange = new \Google_Service_Sheets_ValueRange();
  $valueRange->setValues($rows);
  $range = $pid; // the service will detect the last row of this sheet
  $options = ['valueInputOption' => 'USER_ENTERED'];
  $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
  echo "<script>alert('Ready to Detect Body Temp!!');</script>";
}

if (isset($_POST["detect_alcohol"])) {
  date_default_timezone_set('Asia/Kolkata');
  $date_time = date('m/d/Y H:i:s');
  $pid = $_SESSION['pid'];
  $username = $_SESSION['username'];
  $email = $_SESSION['email'];
  $fname = $_SESSION['fname'];
  $lname = $_SESSION['lname'];
  $gender = $_SESSION['gender'];
  $contact = $_SESSION['contact'];
  $rows = [[$date_time, $pid, $fname, $lname, $email, $contact, $gender, "BloodPressure", "Ready"]]; // you can append several rows at once
  $valueRange = new \Google_Service_Sheets_ValueRange();
  $valueRange->setValues($rows);
  $range = $pid; // the service will detect the last row of this sheet
  $options = ['valueInputOption' => 'USER_ENTERED'];
  $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
  echo "<script>alert('Ready to Detect Alcohol!!');</script>";
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
          <span id="lblTitle" class="hidden-xs" style="color:White;font-size:X-Large;">Hospital Management System | Patient Portal</span>
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
          <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
            <a href="#" class="d-block" style="color:white"><?php echo $username ?> </a>
          </div>
        </div>
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <i class="nav-icon fas fa-id-card-alt"></i>
          </div>
          <div class="info">
            <a href="#" class="d-block" style="color:white">Patient Id : <?php echo $pid ?> </a>
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
              <a href="#make-medical-checkup" id="list-make-medical-checkup" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon 	fa fa-medkit"></i>
                <p>
                  Medical Checkup
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo $dash_medical_checkups ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-heart-rate" id="list-heart-rate-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon 	fa fa-heartbeat"></i>
                <p>
                  Heart Rate Report
                  <i class="right fas fa-angle-left"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_heartrate) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-body-temp" id="list-body-temp-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon 	fas fa-crutch"></i>
                <p>
                  Body Temperature report
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_bodytemp) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-alcohol" id="list-alcohol-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon 	fas fa-tachometer-alt"></i>
                <p>
                  Blood Pressure Report
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_alcohol) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-home" id="list-home-list" data-toggle="list" role="tab" aria-controls="home" class="nav-link">
                <i class="nav-icon 	far fa-edit"></i>
                <p>
                  Book Appointments
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
            </li>

            <li class="nav-item">
              <a href="#app-hist" id="list-pat-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fas fa-notes-medical"></i>
                <p>
                  Appointment History
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_appointments) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-pres" id="list-pres-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fas fa-file-prescription"></i>
                <p>
                  Prescription
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
                        echo $dash_healthrecords;
                        ?></h3>

                      <p>Health Records</p>
                    </div>
                    <div class="icon">
                      <i class="	fas fa-book-medical"></i>
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
                          echo $dash_medical_checkups
                          ?></h3>

                      <p>Medical Checkups</p>
                    </div>
                    <div class="icon">
                      <i class="	fa fa-medkit"></i>
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
                        echo sizeof($dash_appointments)
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
                <div class="col-12 col-sm-6 col-md-3 ">
                  <!-- small card -->
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>
                        <?php
                        echo sizeof($dash_prescriptions)
                        ?></h3>

                      <p>Presciptions</p>
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
                      <h3 class="card-title">Latest Health Records</h3>

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
                              <th>Date</th>
                              <th>Time</th>
                              <th>Type</th>
                              <th>Result</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php


                            $rows = $dash_all_records;
                            if ($rows) {
                              for ($idx = $dash_healthrecords - 1; $idx >= ($dash_healthrecords - 6) && $idx >= 0; $idx--) {
                                if ($rows[$idx][8] != "Ready") {
                            ?>
                                  <td>
                                    <?php echo date('d/M/Y', strtotime($rows[$idx][0])); ?>
                                  </td>
                                  <td>
                                    <?php echo date('h:i:sa', strtotime($rows[$idx][0])); ?>
                                  </td>
                                  <td>
                                    <?php echo $rows[$idx][7]; ?>
                                  </td>
                                  <td>
                                    <?php echo (float)($rows[$idx][8]);
                                    if ($rows[$idx][7] == "HeartRate") echo "BPM";
                                    elseif ($rows[$idx][7] == "BodyTemp") echo "F";
                                    elseif ($rows[$idx][7] == "BodyAlcohol") echo "Al";
                                    ?>
                                  </td>
                                  </tr>
                            <?php }
                              }
                            } ?>
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

      <!-- MEDICAL CHECKUP -->
      <div class="tab-pane fade" id="make-medical-checkup" role="tabpanel" aria-labelledby="list-make-medical-checkup">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Medical Checkup</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Medical Chekup</li>
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
              <div class="row">
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="card-title">Make Medical Checkups</h5>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <div class="btn-group">
                          <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="dropdown-item">Action</a>
                            <a href="#" class="dropdown-item">Another action</a>
                            <a href="#" class="dropdown-item">Something else here</a>
                            <a class="dropdown-divider"></a>
                            <a href="#" class="dropdown-item">Separated link</a>
                          </div>
                        </div>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">

                      <form class="form-group" method="post" action="patient_portal.php">
                        <div class="row">
                          <div class="col-md-4">
                            <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-danger"></i> <i class="fa fa-heartbeat fa-stack-1x fa-inverse"></i> </span>
                            <input type="submit" name="detect_heart_rate" value="Detect Heart Rate" class="btn btn-danger" id="inputbtn">
                          </div>
                          <div class="col-md-4">
                            <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-warning"></i> <i class="fas fa-crutch fa-stack-1x fa-inverse"></i> </span>
                            <input type="submit" name="detect_body_temp" value="Detect Body Temperature" class="btn btn-warning" id="inputbtn">
                          </div>
                          <div class="col-md-4">
                            <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fas fa-tachometer-alt fa-stack-1x fa-inverse"></i> </span>
                            <input type="submit" name="detect_alcohol" value="Detect Blood Pressure" class="btn btn-primary" id="inputbtn">
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                  <!-- /.card -->
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
      <!-- /MEDICAL CHECKUP -->

      <!-- HEARTRATE -->
      <div class="tab-pane fade" id="list-heart-rate" role="tabpanel" aria-labelledby="list-heart-rate-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Heart Rate Report</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Heart Rate Report</li>
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
                      <h3 class="card-title">Heath Rate Report</h3>

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
                              <th>Date</th>
                              <th>Time</th>
                              <th>HeartRate</th>
                              <th>Result</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $rows = $dash_heartrate;
                            $heart_normal = 0;
                            $heart_low = 0;
                            $heart_high = 0;
                            $heartRate_graph = [];
                            if ($rows) {
                              foreach ($rows as $row) {
                                if ($row[7] == "HeartRate" && $row[8] != "Ready") {
                                  array_push($heartRate_graph, array("x" => date('Y-m-d H:i:s', strtotime($row[0])), "y" => $row[8]));
                            ?>
                                  <td>
                                    <?php echo date('d/M/Y', strtotime($row[0])); ?>
                                  </td>
                                  <td>
                                    <?php echo date('h:i:sa', strtotime($row[0])); ?>
                                  </td>
                                  <td>
                                    <?php echo (float)($row[8]); ?> BPM
                                  </td>
                                  <td>
                                    <?php if (((float)$row[8]) <= 60) {
                                      $heart_low++;
                                      echo  "<span class='badge badge-warning'>";
                                      echo "Low";
                                      echo "</span>"; ?>
                                    <?php } else if (((float)$row[8]) <= 100) {
                                      $heart_normal++;
                                      echo  "<span class='badge badge-success'>";
                                      echo "Normal";
                                      echo "</span>";
                                    ?>

                                    <?php } else {
                                      $heart_high++;
                                      echo  "<span class='badge badge-danger'>";
                                      echo "High";
                                      echo "</span>";
                                    } ?>
                                  </td>
                                  </tr>
                            <?php }
                              }
                            } ?>
                          </tbody>
                        </table>
                      </div>
                      <!-- /.table-responsive -->
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
                <!-- /.col -->

                <!-- /.col -->
              </div>
              <!-- /.row -->
              <div class="row">
                <div class="col-12">
                  <!-- LINE CHART -->
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Interactive Chart</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <canvas id="HRinteractive"></canvas>
                      <!-- <canvas id="heartrate-doughnut" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas> -->
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->

                </div>
                <!-- /.col -->

                <div class="col-6">
                  <!-- DONUT CHART -->
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Result Chart</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <canvas id="HRdoughnut"></canvas>
                      <!-- <canvas id="heartrate-doughnut" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas> -->
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->

                </div>
                <!-- /.col -->

                <!-- /.col -->
                <div class="col-6">
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Result Criteria</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <img class="card-img" src="https://images.sampletemplates.com/wp-content/uploads/2016/02/20103613/Heart-Rate-Monitor-Chart.jpeg">
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->

                </div>
              </div>

            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /HEARTRATE -->

      <!-- BODY TEMPERATURE -->
      <div class="tab-pane fade" id="list-body-temp" role="tabpanel" aria-labelledby="list-body-temp-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Body Temperatue Report</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Body Temperatue Report</li>
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
                      <h3 class="card-title">Body Temperatue Report</h3>

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
                              <th>Date</th>
                              <th>Time</th>
                              <th>BodyTemperature</th>
                              <th>Result</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php

                            $rows = $dash_bodytemp;
                            $bodytemp_results = [0, 0, 0, 0, 0, 0, 0];
                            $bodytemp_linegraph = [];

                            if ($rows) {
                              foreach ($rows as $row) {
                                if ($row[7] == "BodyTemp" && $row[8] != "Ready") {
                                  array_push($bodytemp_linegraph, array("x" => date('Y-m-d H:i:s', strtotime($row[0])), "y" => $row[8]));
                            ?>
                                  <td>
                                    <?php echo date('d/M/Y', strtotime($row[0])); ?>
                                  </td>
                                  <td>
                                    <?php echo date('h:i:sa', strtotime($row[0])); ?>
                                  </td>
                                  <td>
                                    <?php echo (float)($row[8]); ?> F </td>
                                  <td>
                                    <?php if (((float)$row[8]) <= 77) {
                                      $bodytemp_results[0]++;
                                      echo "Cessation of cardiac and Respiratory functions"; ?>
                                    <?php } else if (((float)$row[8]) <= 82.4) {
                                      $bodytemp_results[1]++;
                                      echo "Severe Hypothermia";
                                    ?>
                                    <?php } else if (((float)$row[8]) <= 96.8) {
                                      $bodytemp_results[2]++;
                                      echo "Hypothermia";
                                    ?>
                                    <?php } else if (((float)$row[8]) <= 100.4) {
                                      $bodytemp_results[3]++;
                                      echo "Normal";
                                    ?>
                                    <?php } else if (((float)$row[8]) <= 105.8) {
                                      $bodytemp_results[4]++;
                                      echo "Pyrexia";
                                    ?>
                                    <?php } else if (((float)$row[8]) <= 111.2) {
                                      $bodytemp_results[5]++;
                                      echo "Hyper Pyrexia";
                                    ?>
                                    <?php } else {
                                      $bodytemp_results[6]++;
                                      echo "Irreversiblle cell damage and death";
                                    } ?>
                                    </tr>
                              <?php }
                              }
                            } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.row -->

              <div class="row">

                <div class="col-12">
                  <!-- LINE CHART -->
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Interactive Chart</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <canvas id="BTinteractive"></canvas>
                      <!-- <canvas id="heartrate-doughnut" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas> -->
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->

                </div>
                <!-- /.col -->
              </div>

              <div class="row">
                <div class="col-6">
                  <!-- DONUT CHART -->
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Result Chart</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <canvas id="BTdoughnut"></canvas>
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->

                </div>
                <!-- /.col -->
                <div class="col-6">
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Result Criteria</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <img class="card-img" src="https://img.tfd.com/medical/Davis/Tabers/t05.jpg">
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->

                </div>
              </div>

            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /BODY TEMPERATURE -->

      <!-- BLOOD PRESSURE -->
      <div class="tab-pane fade" id="list-alcohol" role="tabpanel" aria-labelledby="list-alcohol-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Blood Pressure Report</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Blood Pressure Report</li>
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
                      <h3 class="card-title">Blood Pressure Report</h3>

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
                              <th>Date</th>
                              <th>Time</th>
                              <th>Blood Pressure</th>
                              <th>Result</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php

                            $rows = $dash_alcohol;

                            $bodytemp_results = [0, 0, 0, 0, 0, 0, 0];
                            if ($rows) {

                              foreach ($rows as $row) {
                                if ($row[7] == "BloodPressure" && $row[8] != "Ready") {
                            ?>
                                  <td>
                                    <?php echo date('d/M/Y', strtotime($row[0])); ?>
                                  </td>
                                  <td>
                                    <?php echo date('h:i:sa', strtotime($row[0])); ?>
                                  </td>
                                  <td>
                                    <?php echo (float)($row[8]); ?> mmHg </td>
                                  <td>
                                    <?php if (((float)$row[8]) <= 80) {
                                      $bodytemp_results[0]++;
                                      echo "Normal"; ?>
                                    <?php } else if (((float)$row[8]) <= 129) {
                                      $bodytemp_results[1]++;
                                      echo "Elevated";
                                    ?>
                                    <?php } else if (((float)$row[8]) <= 139) {
                                      $bodytemp_results[2]++;
                                      echo "Hypertension Stage 1";
                                    ?>
                                    <?php } else if (((float)$row[8]) <= 180) {
                                      $bodytemp_results[3]++;
                                      echo "Hypertension Stage 2";
                                    ?>
                                    <?php } else{
                                      $bodytemp_results[4]++;
                                      echo "Hypertensive Crises";
                                    }?>
                                    </tr>
                              <?php }
                              }
                            } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.row -->

              <div class="row">
                <div class="col-12">
                  <!-- interactive chart -->
                  <div class="card card-primary card-outline">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="far fa-chart-bar"></i> Interactive Area Chart
                      </h3>

                      <div class="card-tools">
                        Real time
                        <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                          <button type="button" class="btn btn-default btn-sm active" data-toggle="on">On</button>
                          <button type="button" class="btn btn-default btn-sm" data-toggle="off">Off</button>
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                          <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div id="interactive" style="height: 300px;"></div>
                    </div>
                    <!-- /.card-body-->
                  </div>
                  <!-- /.card -->

                </div>
                <!-- /.col -->
              </div>

              <div class="row">
                <div class="col-6">
                  <!-- DONUT CHART -->
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Result Chart</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <canvas id="BTdoughnut"></canvas>
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->

                </div>
                <!-- /.col -->
                <div class="col-6">
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Result Criteria</h3>

                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <img class="card-img" src="https://www.heart.org/-/media/Images/Health-Topics/High-Blood-Pressure/Rainbow-Chart/blood-pressure-readings-chart.jpg">
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->

                </div>
              </div>

            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /BLOOD PRESSURE -->


      <!--BOOK APPOINTMENT -->
      <div class="tab-pane fade" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Book Appointment</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Book Appointment</li>
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
                <div class="col-12">
                  <div class="card card-primary card-outline">
                    <div class="card-header border-transparent">
                      <h3 class="card-title text-center">Create Appointment</h3>

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
                      <form class="form-group" method="post" action="patient_portal.php">
                        <div class="row">

                          <div class="col-md-4">
                            <label for="spec">Specialization:</label>
                          </div>
                          <div class="col-md-8">
                            <select name="spec" class="form-control" id="spec">
                              <option value="" disabled selected>Select Specialization</option>
                              <?php
                              $range = 'doctordb';
                              $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                              $rows = $response->getValues();
                              $arr = [];
                              $idx = 0;
                              for ($i = 1; $i < sizeof($rows); $i++) {
                                $arr[$idx++] = $rows[$i][3];
                              }
                              $arrunique = array_unique($arr);
                              foreach ($arrunique as $spec) {
                                echo '<option data-value="' . $spec . '">' . $spec . '</option>';
                              }
                              ?>
                            </select>
                          </div>

                          <br><br>

                          <script>
                            document.getElementById('spec').onchange = function foo() {
                              let spec = this.value;
                              console.log(spec)
                              let docs = [...document.getElementById('doctor').options];

                              docs.forEach((el, ind, arr) => {
                                arr[ind].setAttribute("style", "");
                                if (el.getAttribute("data-spec") != spec) {
                                  arr[ind].setAttribute("style", "display: none");
                                }
                              });
                            };
                          </script>

                          <div class="col-md-4"><label for="doctor">Doctors:</label></div>
                          <div class="col-md-8">
                            <select name="doctor" class="form-control" id="doctor" required="required">
                              <option value="" disabled selected>Select Doctor</option>

                              <?php
                              $range = 'doctordb';
                              $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                              $rows = $response->getValues();
                              foreach ($rows as $row) {
                                $username = $row[0];
                                $price = $row[4];
                                $spec = $row[3];
                                echo '<option value="' . $username . '" data-value="' . $price . '" data-spec="' . $spec . '">' . $username . '</option>';
                              } ?>
                            </select>
                          </div><br /><br />


                          <script>
                            document.getElementById('doctor').onchange = function updateFees(e) {
                              var selection = document.querySelector(`[value=${this.value}]`).getAttribute('data-value');
                              document.getElementById('docFees').value = selection;
                            };
                          </script>
                          <div class="col-md-4"><label for="consultancyfees">
                              Consultancy Fees
                            </label></div>
                          <div class="col-md-8">
                            <!-- <div id="docFees">Select a doctor</div> -->
                            <input class="form-control" type="text" name="docFees" id="docFees" readonly="readonly" />
                          </div><br><br>

                          <div class="col-md-4"><label>Appointment Date</label></div>
                          <div class="col-md-8"><input type="date" class="form-control datepicker" name="appdate"></div><br><br>

                          <div class="col-md-4"><label>Appointment Time</label></div>
                          <div class="col-md-8">
                            <!-- <input type="time" class="form-control" name="apptime"> -->
                            <select name="apptime" class="form-control" id="apptime" required="required">
                              <option value="" disabled selected>Select Time</option>
                              <option value="08:00:00">8:00 AM</option>
                              <option value="10:00:00">10:00 AM</option>
                              <option value="12:00:00">12:00 PM</option>
                              <option value="14:00:00">2:00 PM</option>
                              <option value="16:00:00">4:00 PM</option>
                            </select>

                          </div><br><br>

                          <div class="col-md-4">
                            <input type="submit" name="app-submit" value="Create new entry" class="btn btn-primary" id="inputbtn">
                          </div>
                          <div class="col-md-8"></div>
                        </div>
                      </form>
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
      <!-- /BOOK APPOINTMENT -->

      <!-- APPOINTMENT HISTORY -->
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
                              <th>Appointment ID</th>
                              <th>Specialization</th>
                              <th>Doctor Name</th>
                              <th>Consultancy Fees</th>
                              <th>Appointment Date</th>
                              <th>Appointment Time</th>
                              <th>Current Status</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $rows = $dash_appointments;
                            if ($rows) {
                              foreach ($rows as $row) {
                            ?>
                                <td>
                                  <?php echo $row[1]; ?>
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
                                  <?php echo $row[11]; ?>
                                </td>
                                <td>
                                  <?php echo $row[12]; ?>
                                </td>
                                <td>
                                  <?php echo $row[13]; ?>
                                </td>

                                <td>
                                  <?php if ($row[13] == "Active") { ?>
                                    <a href="patient_portal.php?ID=<?php echo $row[1] ?>&cancel=update" onClick="return confirm('Are you sure you want to cancel this appointment ?')" title="Cancel Appointment" tooltip-placement="top" tooltip="Remove"><button class="btn btn-danger">Cancel</button></a>
                                  <?php } else {

                                    echo "Cancelled";
                                  } ?>

                                </td>
                                </tr>
                            <?php }
                            }
                            ?>
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
      <!-- /APPOINTMENT HISTORY -->


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
                              <th>Doctor Name</th>
                              <th>Appointment ID</th>
                              <th>Appointment Date</th>
                              <th>Appointment Time</th>
                              <th>Diseases</th>
                              <th>Allergies</th>
                              <th>Prescriptions</th>
                              <th>Download</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $rows = $dash_prescriptions;
                            if ($rows) {
                              foreach ($rows as $row) {
                            ?>
                                <td>
                                  <?php echo $row[0]; ?>
                                </td>
                                <td>
                                  <?php echo $row[2]; ?>
                                </td>
                                <td>
                                  <?php echo $row[1]; ?>
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
                                    <a href="generate_prescription.php?ID=<?php echo $row[1] ?>" target="_blank">
                                      <input type="hidden" name="ID" value="<?php echo $row[1] ?>" />
                                      <input type="submit" name="generate_pres" class="btn btn-success" value="Download" />
                                    </a>
                                  </form>
                                </td>


                                </tr>
                            <?php }
                            }
                            ?>
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
  <!-- ChartJS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

  <script>
    window.onload = function() {


      // HEART RATE LINE CHART
      var x = new Chart(document.getElementById("HRinteractive"), {
        type: 'line',
        data: {
          datasets: [{
            label: "Heart Rate ",
            data: <?php echo json_encode($heartRate_graph, JSON_NUMERIC_CHECK); ?>,
          }]
        },
        options: {
          responsive: true,
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'day'
              }

            }
          }
        }
      });
      // HEART RATE DOUGHNUT CHART
      const heartrate_doughnut = document.getElementById('HRdoughnut');
      const HRdoughnut = new Chart(heartrate_doughnut, {
        type: 'doughnut',
        data: {
          labels: [
            'Low',
            'Normal',
            'High',
          ],
          datasets: [{
            label: 'Heart Beat',
            data: [<?php echo $heart_low ?>, <?php echo $heart_normal ?>, <?php echo $heart_high ?>],
            backgroundColor: [
              'yellow',
              'green',
              'red'
            ],

            hoverOffset: 4
          }]
        }
      });

      // HEART RATE LINE CHART
      var y = new Chart(document.getElementById("BTinteractive"), {
        type: 'line',
        data: {
          datasets: [{
            label: "Body Temperature",
            data: <?php echo json_encode($bodytemp_linegraph, JSON_NUMERIC_CHECK); ?>,
          }]
        },
        options: {
          responsive: true,
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'day'
              }

            }
          }
        }
      });

      // BODYTEMP DOUGHNUT CHART
      const bodyTemp_doughnut = document.getElementById('BTdoughnut');
      const BTdoughnut = new Chart(bodyTemp_doughnut, {
        type: 'doughnut',
        data: {
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
            label: 'Body Temperatures',
            data: <?php echo json_encode($bodytemp_results, JSON_NUMERIC_CHECK); ?>,
            backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', 'black'],
            hoverOffset: 4
          }]
        }
      });

    }
  </script>

  <script>
    // // Body TEMPERATURE DOUGHNUT CHART
    // const bodyTemp_doughnut = document.getElementById('BTdoughnut')
    // var bodyTemp_donutData = {
    //   labels: [
    //     'Cessation of cardiac and Respiratory functions',
    //     'Severe Hypothermia',
    //     'Hypothermia',
    //     'Normal',
    //     'Pyrexia',
    //     'Hyper Pyrexia',
    //     'Irreversiblle cell damage and death',
    //   ],
    //   datasets: [{
    //     data: <?php echo $bodytemp_results ?>,
    //     backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', 'black'],
    //   }]
    // }
    // var bodyTemp_donutOptions = {
    //   maintainAspectRatio: false,
    //   responsive: true,
    // }
    // new Chart(bodyTemp_doughnut, {
    //   type: 'doughnut',
    //   data: heartrate_donutData,
    //   options: heartrate_donutOptions
    // })
  </script>
</body>

</html>