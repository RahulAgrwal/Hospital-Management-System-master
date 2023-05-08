<!DOCTYPE html>
<?php
require 'vendor/autoload.php';
include('newfunc.php');
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

if (isset($_POST['docsub'])) {
  date_default_timezone_set('Asia/Kolkata');
  $doctor = $_POST['doctor'];
  $dpassword = $_POST['dpassword'];
  $demail = $_POST['demail'];
  $spec = $_POST['special'];
  $docFees = $_POST['docFees'];
  $doctor_add_on = date('d-m-y h:i:sa');

  if ($result) {
    // configure the Google Client

    // $client = new \Google_Client();
    // $client->setApplicationName('Google Sheets API');
    // $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    // $client->setAccessType('offline');
    // // credentials.json is the key file we downloaded while setting up our Google Sheets API
    // $path = 'credentials.json';
    // $client->setAuthConfig($path);

    // // configure the Sheets Service
    // $service = new \Google_Service_Sheets($client);

    // // the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
    // $spreadsheetId = '1VEVOIKqIWC7CTGTPGrdj27JuAiufwDUQl6Zht-cnvE0';
    // $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    //var_dump($spreadsheet);
    $newRow = [$doctor, $demail, $dpassword, $spec, $docFees, $doctor_add_on];
    $rows = [$newRow]; // you can append several rows at once
    $valueRange = new \Google_Service_Sheets_ValueRange();
    $valueRange->setValues($rows);
    $range = 'doctordb'; // the service will detect the last row of this sheet
    $options = ['valueInputOption' => 'USER_ENTERED'];
    $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
    echo "<script>alert('Doctor added successfully!');</script>";
  }
}


if (isset($_POST['docsub1'])) {
  $demail = $_POST['demail'];


  // $client = new \Google_Client();
  // $client->setApplicationName('Google Sheets API');
  // $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
  // $client->setAccessType('offline');
  // // credentials.json is the key file we downloaded while setting up our Google Sheets API
  // $path = 'credentials.json';
  // $client->setAuthConfig($path);

  // // configure the Sheets Service
  // $service = new \Google_Service_Sheets($client);

  // // the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
  // $spreadsheetId = '1VEVOIKqIWC7CTGTPGrdj27JuAiufwDUQl6Zht-cnvE0';
  // $spreadSheet = $service->spreadsheets->get($spreadsheetId);
  $sheets = $spreadSheet->getSheets();
  $sheedName = "doctordb";
  $sheetID = -1;
  foreach ($sheets  as  $key => $sheet) {
    if ($sheet->properties->title == $sheedName) {
      $sheetID = $sheet->properties->sheetId;
      break;
    }
  }
  if ($sheetID > -1) {
    $range = 'doctordb'; // the service will detect the last row of this sheet
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $rows = $response->getValues();
    $startIndex = 0;
    $endIndex = 0;
    for ($i = 1; $i < sizeof($rows); $i++) {
      if ($rows[$i][1] == $demail) {
        $startIndex = $i;
        $endIndex = $i + 1;
        break;
      }
    }
    if ($startIndex > 0) {
      $deleteOperation = array(
        'range' => array(
          'sheetId'   => $sheetID, // <======= This mean the very first sheet on worksheet
          'dimension' => 'ROWS',
          'startIndex' => $startIndex, //Identify the starting point,
          'endIndex'  =>  $endIndex //Identify where to stop when deleting
        )
      );
      $deletable_row[] = new Google_Service_Sheets_Request(
        array('deleteDimension' =>  $deleteOperation)
      );
      $delete_body    = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(
        array(
          'requests' => $deletable_row
        )
      );
      $result = $service->spreadsheets->batchUpdate($spreadsheetId, $delete_body);


      echo "<script>alert('Doctor removed successfully!');</script>";
    } else {
      echo "<script>alert('Unable to delete!');</script>";
    }
  } else {
    echo "<script>alert('Unable to delete!');</script>";
  }
}



?>
<html lang="en">

<head>


  <!-- Required meta tags -->
  <meta charset="utf-8">
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" type="text/css" href="font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="style.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <a class="navbar-brand" href="#"><h2><img src="https://dpu.edu.in/img/logo.png" style="height:50px;"></h2></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <script>
      var check = function() {
        if (document.getElementById('dpassword').value ==
          document.getElementById('cdpassword').value) {
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
    </script>

    <style>
      .bg-primary {
        background: -webkit-linear-gradient(left, #3931af, #00c6ff);
      }

      .col-md-4 {
        max-width: 20% !important;
      }

      .list-group-item.active {
        z-index: 2;
        color: #fff;
        background-color: #342ac1;
        border-color: #007bff;
      }

      .text-primary {
        color: #342ac1 !important;
      }

      #cpass {
        display: -webkit-box;
      }

      #list-app {
        font-size: 15px;
      }

      .btn-primary {
        background-color: #3c50c1;
        border-color: #3c50c1;
      }
    </style>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="logout1.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"></a>
        </li>
      </ul>
    </div>
  </nav>
</head>
<style type="text/css">
  button:hover {
    cursor: pointer;
  }

  #inputbtn:hover {
    cursor: pointer;
  }
</style>

<body style="padding-top:50px;">
  <div class="container-fluid" style="margin-top:50px;">
    <h3 style="margin-left: 40%; padding-bottom: 20px;font-family: 'IBM Plex Sans', sans-serif;"> WELCOME ADMIN </h3>
    <div class="row">
      <div class="col-md-4" style="max-width:25%;margin-top: 3%;">
        <div class="list-group" id="list-tab" role="tablist">
          <a class="list-group-item list-group-item-action active" id="list-dash-list" data-toggle="list" href="#list-dash" role="tab" aria-controls="home">Dashboard</a>
          <a class="list-group-item list-group-item-action" href="#list-doc" id="list-doc-list" role="tab" aria-controls="home" data-toggle="list">Doctor List</a>
          <a class="list-group-item list-group-item-action" href="#list-pat" id="list-pat-list" role="tab" data-toggle="list" aria-controls="home">Patient List</a>
          <a class="list-group-item list-group-item-action" href="#list-app" id="list-app-list" role="tab" data-toggle="list" aria-controls="home">Appointment Details</a>
          <a class="list-group-item list-group-item-action" href="#list-pres" id="list-pres-list" role="tab" data-toggle="list" aria-controls="home">Prescription List</a>
          <a class="list-group-item list-group-item-action" href="#list-settings" id="list-adoc-list" role="tab" data-toggle="list" aria-controls="home">Add Doctor</a>
          <a class="list-group-item list-group-item-action" href="#list-settings1" id="list-ddoc-list" role="tab" data-toggle="list" aria-controls="home">Delete Doctor</a>
          <a class="list-group-item list-group-item-action" href="#list-mes" id="list-mes-list" role="tab" data-toggle="list" aria-controls="home">Queries</a>

        </div><br>
      </div>
      <div class="col-md-8" style="margin-top: 3%;">
        <div class="tab-content" id="nav-tabContent" style="width: 950px;">



          <div class="tab-pane fade show active" id="list-dash" role="tabpanel" aria-labelledby="list-dash-list">
            <div class="container-fluid container-fullw bg-white">
              <div class="row">
                <div class="col-sm-4">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-users fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">Doctor List</h4>
                      <script>
                        function clickDiv(id) {
                          document.querySelector(id).click();
                        }
                      </script>
                      <p class="links cl-effect-1">
                        <a href="#list-doc" onclick="clickDiv('#list-doc-list')">
                          View Doctors
                        </a>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="col-sm-4" style="left: -3%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-users fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">Patient List</h4>

                      <p class="cl-effect-1">
                        <a href="#app-hist" onclick="clickDiv('#list-pat-list')">
                          View Patients
                        </a>
                      </p>
                    </div>
                  </div>
                </div>


                <div class="col-sm-4">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-paperclip fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">Appointment Details</h4>

                      <p class="cl-effect-1">
                        <a href="#app-hist" onclick="clickDiv('#list-app-list')">
                          View Appointments
                        </a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4" style="left: 13%;margin-top: 5%;">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-list-ul fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">Prescription List</h4>

                      <p class="cl-effect-1">
                        <a href="#list-pres" onclick="clickDiv('#list-pres-list')">
                          View Prescriptions
                        </a>
                      </p>
                    </div>
                  </div>
                </div>


                <div class="col-sm-4" style="left: 18%;margin-top: 5%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-plus fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">Manage Doctors</h4>

                      <p class="cl-effect-1">
                        <a href="#app-hist" onclick="clickDiv('#list-adoc-list')">Add Doctors</a>
                        &nbsp|
                        <a href="#app-hist" onclick="clickDiv('#list-ddoc-list')">
                          Delete Doctors
                        </a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>




            </div>
          </div>









          <div class="tab-pane fade" id="list-doc" role="tabpanel" aria-labelledby="list-home-list">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Doctor Name</th>
                  <th scope="col">Specialization</th>
                  <th scope="col">Email</th>
                  <th scope="col">Password</th>
                  <th scope="col">Fees</th>
                  <th scope="col">Registered On</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // $client = new \Google_Client();
                // $client->setApplicationName('Google Sheets API');
                // $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
                // $client->setAccessType('offline');
                // // credentials.json is the key file we downloaded while setting up our Google Sheets API
                // $path = 'credentials.json';
                // $client->setAuthConfig($path);

                // // configure the Sheets Service
                // $service = new \Google_Service_Sheets($client);

                // // the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
                // $spreadsheetId = '1VEVOIKqIWC7CTGTPGrdj27JuAiufwDUQl6Zht-cnvE0';
                // $spreadsheet = $service->spreadsheets->get($spreadsheetId);
                //var_dump($spreadsheet);
                // Fetch the rows
                $range = 'doctordb';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $rows = $response->getValues();
                // Remove the first one that contains headers
                for ($x = 1; $x < count($rows); $x++) {
                  $docname = $rows[$x][0];
                  $password = $rows[$x][2];
                  $email = $rows[$x][1];
                  $spec = $rows[$x][3];
                  $docFees = $rows[$x][4];
                  $registered_on = $rows[$x][5];

                  echo "<tr>
                        <td>$docname</td>
                        <td>$spec</td>
                        <td>$email</td>
                        <td>$password</td>
                        <td>$docFees</td>
                        <td>$registered_on</td>
                      </tr>";
                } ?>
              </tbody>
            </table>
            <br>
          </div>


          <div class="tab-pane fade" id="list-pat" role="tabpanel" aria-labelledby="list-pat-list">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Patient ID</th>
                  <th scope="col">First Name</th>
                  <th scope="col">Last Name</th>
                  <th scope="col">Contact</th>
                  <th scope="col">Gender</th>
                  <th scope="col">Email</th>
                  <th scope="col">Password</th>
                  <th scope="col">Registered On</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // $client = new \Google_Client();
                // $client->setApplicationName('Google Sheets API');
                // $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
                // $client->setAccessType('offline');
                // // credentials.json is the key file we downloaded while setting up our Google Sheets API
                // $path = 'credentials.json';
                // $client->setAuthConfig($path);

                // // configure the Sheets Service
                // $service = new \Google_Service_Sheets($client);

                // // the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
                // $spreadsheetId = '1VEVOIKqIWC7CTGTPGrdj27JuAiufwDUQl6Zht-cnvE0';
                // $spreadsheet = $service->spreadsheets->get($spreadsheetId);
                //var_dump($spreadsheet);
                // Fetch the rows
                $range = 'registrations';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $rows = $response->getValues();
                // Remove the first one that contains headers
                for ($x = 1; $x < count($rows); $x++) {
                  $pid = $rows[$x][0];
                  $fname = $rows[$x][1];
                  $lname = $rows[$x][2];
                  $contact = $rows[$x][3];
                  $gender = $rows[$x][4];
                  $email = $rows[$x][5];
                  $password = $rows[$x][6];
                  $registered_on = $rows[$x][8];

                  echo "<tr>
                        <td>$pid</td>
                        <td>$fname</td>
                        <td>$lname</td>
                        <td>$gender</td>
                        <td>$email</td>
                        <td>$contact</td>
                        <td>$password</td>
                        <td>$registered_on</td>
                      </tr>";
                } ?>
              </tbody>
            </table>
            <br>
          </div>


          <div class="tab-pane fade" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
            <div class="col-md-8">
              <div class="row">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Prescribed On</th>
                      <th scope="col">Appointment ID</th>
                      <th scope="col">Doctor</th>
                      <th scope="col">Patient ID</th>
                      <th scope="col">First Name</th>
                      <th scope="col">Last Name</th>
                      <th scope="col">Appointment Date</th>
                      <th scope="col">Appointment Time</th>
                      <th scope="col">Disease</th>
                      <th scope="col">Allergy</th>
                      <th scope="col">Prescription</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // $client = new \Google_Client();
                    // $client->setApplicationName('Google Sheets API');
                    // $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
                    // $client->setAccessType('offline');
                    // // credentials.json is the key file we downloaded while setting up our Google Sheets API
                    // $path = 'credentials.json';
                    // $client->setAuthConfig($path);

                    // // configure the Sheets Service
                    // $service = new \Google_Service_Sheets($client);

                    // // the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
                    // $spreadsheetId = '1VEVOIKqIWC7CTGTPGrdj27JuAiufwDUQl6Zht-cnvE0';
                    // $spreadsheet = $service->spreadsheets->get($spreadsheetId);
                    //var_dump($spreadsheet);
                    // Fetch the rows
                    $range = 'Prescriptions';
                    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                    $rows = $response->getValues();
                    for ($x = 1; $x < sizeof($rows); $x++) {
                    ?>
                      <tr>
                        <td><?php echo $rows[$x][0]; ?></td>
                        <td><?php echo $rows[$x][1]; ?></td>
                        <td><?php echo $rows[$x][2]; ?></td>
                        <td><?php echo $rows[$x][3]; ?></td>
                        <td><?php echo $rows[$x][4]; ?></td>
                        <td><?php echo $rows[$x][5]; ?></td>
                        <td><?php echo $rows[$x][6]; ?></td>
                        <td><?php echo $rows[$x][7]; ?></td>
                        <td><?php echo $rows[$x][8]; ?></td>
                        <td><?php echo $rows[$x][9]; ?></td>
                        <td><?php echo $rows[$x][10]; ?></td>
                      </tr>
                    <?php
                    } ?>
                  </tbody>
                </table>
                <br>
              </div>
            </div>
          </div>




          <div class="tab-pane fade" id="list-app" role="tabpanel" aria-labelledby="list-pat-list">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Appointment ID</th>
                  <th scope="col">Patient ID</th>
                  <th scope="col">First Name</th>
                  <th scope="col">Last Name</th>
                  <th scope="col">Gender</th>
                  <th scope="col">Email</th>
                  <th scope="col">Contact</th>
                  <th scope="col">Specialist</th>
                  <th scope="col">Doctor Name</th>
                  <th scope="col">Consultancy Fees</th>
                  <th scope="col">Appointment Date</th>
                  <th scope="col">Appointment Time</th>
                  <th scope="col">Appointment Status</th>
                </tr>
              </thead>
              <tbody>
                <?php

                // $client = new \Google_Client();
                // $client->setApplicationName('Google Sheets API');
                // $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
                // $client->setAccessType('offline');
                // // credentials.json is the key file we downloaded while setting up our Google Sheets API
                // $path = 'credentials.json';
                // $client->setAuthConfig($path);

                // // configure the Sheets Service
                // $service = new \Google_Service_Sheets($client);

                // // the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
                // $spreadsheetId = '1VEVOIKqIWC7CTGTPGrdj27JuAiufwDUQl6Zht-cnvE0';
                // $spreadsheet = $service->spreadsheets->get($spreadsheetId);
                //var_dump($spreadsheet);
                // Fetch the rows
                $range = 'Appointments';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $rows = $response->getValues();
                for ($x = 1; $x < sizeof($rows); $x++) {
                ?>
                  <tr>
                    <td><?php echo $rows[$x][1]; ?></td>
                    <td><?php echo $rows[$x][2]; ?></td>
                    <td><?php echo $rows[$x][3]; ?></td>
                    <td><?php echo $rows[$x][4]; ?></td>
                    <td><?php echo $rows[$x][6]; ?></td>
                    <td><?php echo $rows[$x][7]; ?></td>
                    <td><?php echo $rows[$x][5]; ?></td>
                    <td><?php echo $rows[$x][8]; ?></td>
                    <td><?php echo $rows[$x][9]; ?></td>
                    <td><?php echo $rows[$x][10]; ?></td>
                    <td><?php echo $rows[$x][11]; ?></td>
                    <td><?php echo $rows[$x][12]; ?></td>
                    <td><?php echo $rows[$x][13]; ?></td>
                  </tr>
                <?php
                } ?>
              </tbody>
            </table>
            <br>
          </div>

          <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">...</div>

          <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
            <form class="form-group" method="post" action="admin-panel1.php">
              <div class="row">
                <div class="col-md-4"><label>Doctor Name:</label></div>
                <div class="col-md-8"><input type="text" class="form-control" name="doctor" onkeydown="return alphaOnly(event);" required></div><br><br>
                <div class="col-md-4"><label>Specialization:</label></div>
                <div class="col-md-8">
                  <select name="special" class="form-control" id="special" required="required">
                    <option value="head" name="spec" disabled selected>Select Specialization</option>
                    <option value="General" name="spec">General</option>
                    <option value="Cardiologist" name="spec">Cardiologist</option>
                    <option value="Neurologist" name="spec">Neurologist</option>
                    <option value="Pediatrician" name="spec">Pediatrician</option>
                  </select>
                </div><br><br>
                <div class="col-md-4"><label>Email ID:</label></div>
                <div class="col-md-8"><input type="email" class="form-control" name="demail" required></div><br><br>
                <div class="col-md-4"><label>Password:</label></div>
                <div class="col-md-8"><input type="password" class="form-control" onkeyup='check();' name="dpassword" id="dpassword" required></div><br><br>
                <div class="col-md-4"><label>Confirm Password:</label></div>
                <div class="col-md-8" id='cpass'><input type="password" class="form-control" onkeyup='check();' name="cdpassword" id="cdpassword" required>&nbsp &nbsp<span id='message'></span> </div><br><br>


                <div class="col-md-4"><label>Consultancy Fees:</label></div>
                <div class="col-md-8"><input type="text" class="form-control" name="docFees" required></div><br><br>
              </div>
              <input type="submit" name="docsub" value="Add Doctor" class="btn btn-primary">
            </form>
          </div>

          <div class="tab-pane fade" id="list-settings1" role="tabpanel" aria-labelledby="list-settings1-list">
            <form class="form-group" method="post" action="admin-panel1.php">
              <div class="row">

                <div class="col-md-4"><label>Email ID:</label></div>
                <div class="col-md-8"><input type="email" class="form-control" name="demail" required></div><br><br>

              </div>
              <input type="submit" name="docsub1" value="Delete Doctor" class="btn btn-primary" onclick="confirm('do you really want to delete?')">
            </form>
          </div>


          <div class="tab-pane fade" id="list-attend" role="tabpanel" aria-labelledby="list-attend-list">...</div>

          <div class="tab-pane fade" id="list-mes" role="tabpanel" aria-labelledby="list-mes-list">

            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Registered On</th>
                  <th scope="col">User Name</th>
                  <th scope="col">Email</th>
                  <th scope="col">Contact</th>
                  <th scope="col">Message</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // $client = new \Google_Client();
                // $client->setApplicationName('Google Sheets API');
                // $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
                // $client->setAccessType('offline');
                // // credentials.json is the key file we downloaded while setting up our Google Sheets API
                // $path = 'credentials.json';
                // $client->setAuthConfig($path);

                // // configure the Sheets Service
                // $service = new \Google_Service_Sheets($client);

                // // the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
                // $spreadsheetId = '1VEVOIKqIWC7CTGTPGrdj27JuAiufwDUQl6Zht-cnvE0';
                // $spreadsheet = $service->spreadsheets->get($spreadsheetId);
                //var_dump($spreadsheet);
                // Fetch the rows
                $range = 'Queries';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $rows = $response->getValues();
                for ($i = 1; $i < sizeof($rows); $i++) {
                ?>
                  <tr>
                    <td><?php echo $rows[$i][0]; ?></td>
                    <td><?php echo $rows[$i][1]; ?></td>
                    <td><?php echo $rows[$i][2]; ?></td>
                    <td><?php echo $rows[$i][3]; ?></td>
                    <td><?php echo $rows[$i][4]; ?></td>
                  </tr>
                <?php
                } ?>


              </tbody>
            </table>
            <br>
          </div>



        </div>
      </div>
    </div>
  </div>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.10.1/sweetalert2.all.min.js"></script>
</body>

</html>