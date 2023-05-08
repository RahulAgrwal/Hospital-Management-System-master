<!DOCTYPE html>
<?php
require 'vendor/autoload.php';
include('func1.php');
$doctor = $_SESSION['dname'];
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


  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" type="text/css" href="font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="style.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <a class="navbar-brand" href="#">
      <h2><img src="https://dpu.edu.in/img/logo.png" style="height:50px;"></h2>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <style>
      .btn-outline-light:hover {
        color: #25bef7;
        background-color: #f8f9fa;
        border-color: #f8f9fa;
      }
    </style>

    <style>
      .bg-primary {
        background: -webkit-linear-gradient(left, #3931af, #00c6ff);
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
    <h3 style="margin-left: 40%; padding-bottom: 20px;font-family:'IBM Plex Sans', sans-serif;"> Welcome &nbsp<?php echo $_SESSION['dname'] ?> </h3>
    <div class="row">
      <div class="col-md-4" style="max-width:18%;margin-top: 3%;">
        <div class="list-group" id="list-tab" role="tablist">
          <a class="list-group-item list-group-item-action active" href="#list-dash" role="tab" aria-controls="home" data-toggle="list">Dashboard</a>
          <a class="list-group-item list-group-item-action" href="#list-app" id="list-app-list" role="tab" data-toggle="list" aria-controls="home">Appointments</a>
          <a class="list-group-item list-group-item-action" href="#list-pres" id="list-pres-list" role="tab" data-toggle="list" aria-controls="home"> Prescription List</a>

        </div><br>
      </div>
      <div class="col-md-8" style="margin-top: 3%;">
        <div class="tab-content" id="nav-tabContent" style="width: 950px;">
          <div class="tab-pane fade show active" id="list-dash" role="tabpanel" aria-labelledby="list-dash-list">

            <div class="container-fluid container-fullw bg-white">
              <div class="row">

                <div class="col-sm-4" style="left: 10%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-list fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;"> View Appointments</h4>
                      <script>
                        function clickDiv(id) {
                          document.querySelector(id).click();
                        }
                      </script>
                      <p class="links cl-effect-1">
                        <a href="#list-app" onclick="clickDiv('#list-app-list')">
                          Appointment List
                        </a>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="col-sm-4" style="left: 15%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-list-ul fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;"> Prescriptions</h4>

                      <p class="links cl-effect-1">
                        <a href="#list-pres" onclick="clickDiv('#list-pres-list')">
                          Prescription List
                        </a>
                      </p>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>


          <div class="tab-pane fade" id="list-app" role="tabpanel" aria-labelledby="list-home-list">

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
                $range = 'Appointments';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $rows = $response->getValues();

                $array = [];
                foreach ($rows as $row) {
                  if ($row[9] == $doctor) {
                ?>
                    <tr>
                      <td><?php echo $row[1]; ?></td>
                      <td><?php echo $row[2]; ?></td>
                      <td><?php echo $row[3]; ?></td>
                      <td><?php echo $row[4]; ?></td>
                      <td><?php echo $row[6]; ?></td>
                      <td><?php echo $row[7]; ?></td>
                      <td><?php echo $row[5]; ?></td>
                      <td><?php echo $row[11]; ?></td>
                      <td><?php echo $row[12]; ?></td>
                      <td><?php echo $row[13]; ?></td>

                      <td>
                        <?php if (($row[13] == "Active")) { ?>


                          <a href="doctor-panel.php?ID=<?php echo $row[1] ?>&cancel=update" onClick="return confirm('Are you sure you want to cancel this appointment ?')" title="Cancel Appointment" tooltip-placement="top" tooltip="Remove"><button class="btn btn-danger">Cancel</button></a>
                        <?php } else {

                          echo "Cancelled";
                        } ?>

                      </td>

                      <td>

                        <?php if (($row[13] == "Active")) { ?>

                          <a href="prescribe.php?pid=<?php echo $row[2] ?>&ID=<?php echo $row[1] ?>&fname=<?php echo $row[3] ?>&lname=<?php echo $row[4] ?>&appdate=<?php echo $row[11] ?>&apptime=<?php echo $row[12] ?>" tooltip-placement="top" tooltip="Remove" title="prescribe">
                            <button class="btn btn-success">Prescibe</button></a>
                        <?php } else {
                          echo "-";
                        } ?>

                      </td>
                    </tr>
                <?php }
                } ?>
                <!--  -->
              </tbody>
            </table>
            <br>
          </div>



          <div class="tab-pane fade" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Prescribed On</th>
                  <th scope="col">Appointment ID</th>
                  <th scope="col">Patient ID</th>
                  <th scope="col">First Name</th>
                  <th scope="col">Last Name</th>
                  <th scope="col">Appointment Date</th>
                  <th scope="col">Appointment Time</th>
                  <th scope="col">Disease</th>
                  <th scope="col">Allergy</th>
                  <th scope="col">Prescribe</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Fetch the rows
                $range = 'Prescriptions';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $rows = $response->getValues();

                $array = [];
                foreach ($rows as $row) {
                  if ($row[2] == $doctor) {
                ?>
                    <tr>
                      <td><?php echo $row[0]; ?></td>
                      <td><?php echo $row[1]; ?></td>
                      <td><?php echo $row[2]; ?></td>
                      <td><?php echo $row[3]; ?></td>
                      <td><?php echo $row[4]; ?></td>
                      <td><?php echo $row[5]; ?></td>
                      <td><?php echo $row[6]; ?></td>
                      <td><?php echo $row[7]; ?></td>
                      <td><?php echo $row[8]; ?></td>
                      <td><?php echo $row[9]; ?></td>
                      <td><?php echo $row[10]; ?></td>
                    </tr>
                <?php }
                } ?>

              </tbody>
            </table>
          </div>

          <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">...</div>
          <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
            <form class="form-group" method="post" action="admin-panel1.php">
              <div class="row">
                <div class="col-md-4"><label>Doctor Name:</label></div>
                <div class="col-md-8"><input type="text" class="form-control" name="doctor" required></div><br><br>
                <div class="col-md-4"><label>Password:</label></div>
                <div class="col-md-8"><input type="password" class="form-control" name="dpassword" required></div><br><br>
                <div class="col-md-4"><label>Email ID:</label></div>
                <div class="col-md-8"><input type="email" class="form-control" name="demail" required></div><br><br>
                <div class="col-md-4"><label>Consultancy Fees:</label></div>
                <div class="col-md-8"><input type="text" class="form-control" name="docFees" required></div><br><br>
              </div>
              <input type="submit" name="docsub" value="Add Doctor" class="btn btn-primary">
            </form>
          </div>
          <div class="tab-pane fade" id="list-attend" role="tabpanel" aria-labelledby="list-attend-list">...</div>
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