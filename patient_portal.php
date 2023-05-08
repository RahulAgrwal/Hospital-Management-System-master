<!DOCTYPE html>
<?php

include('func.php');
include('newfunc.php');

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

$pid = $_SESSION['pid'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$fname = $_SESSION['fname'];
$gender = $_SESSION['gender'];
$lname = $_SESSION['lname'];
$contact = $_SESSION['contact'];
$arr_chart_label = [];
$arr_chart_datasets = [];
$chart_idx = 0;

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
  $booking = date('d-m-y h:i:sa');
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
      $mail->Password = 'jstdavrdgelmjlkk'; // YOUR gmail password
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





function generate_prescription()
{
  //$con = mysqli_connect("localhost", "root", "", "myhmsdb");
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

  $range = 'Prescriptions';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $pid = $_SESSION['pid'];
  $appid = $_GET['ID'];
  $output = '';

  foreach ($rows as $row) {
    if ($row[3] == $pid && $row[1] == $appid) {
      $output .= '
      <label> Appointment ID : </label>' . $row[1] . '<br/><br/>
      <label> Date : </label>' . $row[0] . '<br/><br/>
      <label> Patient ID : </label>' . $row[3] . '<br/><br/>
      <label> Patient Name : </label>' . $row[4] . ' ' . $row[5] . '<br/><br/>
      <label> Doctor Name : </label>' . $row[2] . '<br/><br/>
      <label> Appointment Date : </label>' . $row[6] . '<br/><br/>
      <label> Appointment Time : </label>' . $row[7] . '<br/><br/>
      <label> Disease : </label>' . $row[8] . '<br/><br/>
      <label> Allergies : </label>' . $row[9] . '<br/><br/>
      <label> Prescription : </label>' . $row[10] . '<br/><br/>
    
    ';
      break;
    }
  }
  return $output;
}


if (isset($_GET["generate_pres"])) {
  require_once("TCPDF/tcpdf.php");
  $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $obj_pdf->SetCreator(PDF_CREATOR);
  $obj_pdf->SetTitle($_GET['ID'] . " Prescription");
  $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
  $obj_pdf->SetHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $obj_pdf->SetFooterFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $obj_pdf->SetDefaultMonospacedFont('helvetica');
  $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
  $obj_pdf->SetPrintHeader(false);
  $obj_pdf->SetPrintFooter(false);
  $obj_pdf->SetAutoPageBreak(TRUE, 10);
  $obj_pdf->SetFont('helvetica', '', 12);
  $obj_pdf->AddPage();

  $content = '';

  $content .= '
      <br/>
      <h2 align ="center"> DPU Hospitals</h2></br>
  ';
  $content .= generate_prescription();
  $obj_pdf->writeHTML($content);
  ob_end_clean();
  $obj_pdf->Output($_GET['ID'] . " Prescription.pdf", 'I');
}

if (isset($_POST["detect_heart_rate"])) {
  date_default_timezone_set('Asia/Kolkata');
  $date_time = date('d-m-y h:i:sa');
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

      .btn-primary {
        background-color: #3c50c1;
        border-color: #3c50c1;
      }
    </style>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
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
    <h3 style="margin-left: 40%;  padding-bottom: 20px; font-family: 'IBM Plex Sans', sans-serif;"> Welcome &nbsp<?php echo $username ?> (Patient Id : <?php echo $pid ?>)
    </h3>
    <div class="row">
      <div class="col-md-4" style="max-width:25%; margin-top: 3%">
        <div class="list-group" id="list-tab" role="tablist">

          <a class="list-group-item list-group-item-action active" id="list-dash-list" data-toggle="list" href="#list-dash" role="tab" aria-controls="home">Dashboard</a>
          <a class="list-group-item list-group-item-action" href="#make-medical-checkup" id="list-make-medical-checkup" role="tab" data-toggle="list" aria-controls="home">Medical Check UP</a>
          <a class="list-group-item list-group-item-action" href="#list-heart-rate" id="list-heart-rate-list" role="tab" data-toggle="list" aria-controls="home">Heart Rate Report</a>
          <a class="list-group-item list-group-item-action" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Book Appointment</a>
          <a class="list-group-item list-group-item-action" href="#app-hist" id="list-pat-list" role="tab" data-toggle="list" aria-controls="home">Appointment History</a>
          <a class="list-group-item list-group-item-action" href="#list-pres" id="list-pres-list" role="tab" data-toggle="list" aria-controls="home">Prescriptions</a>

        </div><br>
      </div>
      <div class="col-md-8" style="margin-top: 5%;">

        <div class="tab-content" id="nav-tabContent" style="width: 950px;">

          <div class="tab-pane fade  show active" id="list-dash" role="tabpanel" aria-labelledby="list-dash-list">
            <div class="container-fluid container-fullw bg-white">

              <div class="row">
                <div class="col-sm-4" style="left: 15%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i><i class="fa fa-stethoscope fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">Medical Check Up</h2>

                        <p class="cl-effect-1">
                          <a href="#make-medical-checkup" onclick="clickDiv('#list-make-medical-checkup')">
                            Make Medical CheckUp
                          </a>
                        </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4" style="left: 20%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-terminal fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;"> Heart Rate Report</h4>
                      <script>
                        function clickDiv(id) {
                          document.querySelector(id).click();
                        }
                      </script>
                      <p class="links cl-effect-1">
                        <a href="#list-heart-rate" onclick="clickDiv('#list-heart-rate-list');">
                          View Heart Rate History
                        </a>
                      </p>
                    </div>
                  </div>
                </div>


              </div>
              <div class="row">
                <div class="col-sm-4" style="left: 0%;margin-top:5%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-terminal fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;"> Book My Appointment</h4>
                      <script>
                        function clickDiv(id) {
                          document.querySelector(id).click();
                        }
                      </script>
                      <p class="links cl-effect-1">
                        <a href="#list-home" onclick="clickDiv('#list-home-list')">
                          Book Appointment
                        </a>
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4" style="left: 3%;margin-top:5%">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-paperclip fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">My Appointments</h2>

                        <p class="cl-effect-1">
                          <a href="#app-hist" onclick="clickDiv('#list-pat-list')">
                            View Appointment History
                          </a>
                        </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4" style="left: 6%;margin-top:5%;">
                  <div class="panel panel-white no-radius text-center">
                    <div class="panel-body">
                      <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-list-ul fa-stack-1x fa-inverse"></i> </span>
                      <h4 class="StepTitle" style="margin-top: 5%;">Prescriptions</h2>

                        <p class="cl-effect-1">
                          <a href="#list-pres" onclick="clickDiv('#list-pres-list')">
                            View Prescription List
                          </a>
                        </p>
                    </div>
                  </div>
                </div>


              </div>


            </div>
          </div>



          <div class="tab-pane fade" id="make-medical-checkup" role="tabpanel" aria-labelledby="list-make-medical-checkup">
            <div class="container-fluid">
              <div class="card">
                <div class="card-body">
                  <center>
                    <h4>Make Medical Check UP</h4>
                  </center><br>
                  <form class="form-group" method="post" action="patient_portal.php">
                    <div class="row">
                      <div class="col-md-4">
                        <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-heartbeat fa-stack-1x fa-inverse"></i> </span>
                        <input type="submit" onclick="alert('Ready to Detect Heart Rate!! Click OK to Detect');" name="detect_heart_rate" value="Detect Heart Rate" class="btn btn-primary" id="inputbtn">
                      </div>
                      <div class="col-md-8"></div>
                    </div>
                  </form>
                </div>
              </div>
            </div><br>
          </div>
          <div class="tab-pane fade" id="list-heart-rate" role="tabpanel" aria-labelledby="list-heart-rate-list">
            <div>
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th scope="col">Date</th>
                    <th scope="col">HeartRate</th>
                    <th scope="col">Result</th>
                  </tr>
                </thead>
                <tbody>
                  <?php

                  $range = $_SESSION['pid'];
                  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                  $rows = $response->getValues();

                  foreach ($rows as $row) {
                    if ($row[7] == "HeartRate" && $row[8] != "Ready") {
                      // date('d/M/Y h:i:sa', strtotime($row[0]))
                      $arr_chart_label[$chart_idx] = date('d/M/Y h:i:sa', strtotime($row[0]));
                      $arr_chart_datasets[$chart_idx] = (float)($row[8]);
                      $chart_idx++;
                  ?>
                      <td><?php echo date('d/M/Y h:i:sa', strtotime($row[0])); ?></td>
                      <td><?php echo (float)($row[8]); ?> BPM </td>
                      <td>
                        <?php if (((float)$row[8]) <= 60) {
                          echo "Low"; ?>
                        <?php } else if (((float)$row[8]) <= 100) {

                          echo "Normal";
                        ?>
                        <?php } else {
                          echo "High";
                        } ?>
                        </tr>
                    <?php }
                  } ?>
                </tbody>
              </table>
              <br>
              <br>
            </div>
            <div>
              <canvas id="myChart"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
              const ctx = document.getElementById('myChart');
              let delayed;
              new Chart(ctx, {
                type: 'line',
                data: {
                  labels: <?php echo json_encode($arr_chart_label) ?>,
                  datasets: [{
                    label: 'HeartRate',
                    data: <?php echo json_encode($arr_chart_datasets) ?>,
                    borderWidth: 2
                  }]
                },
                options: {
                  hoverRadius:10,
                  animations: {
                    onProgress: function(animation) {
                      progress.value = animation.currentStep / animation.numSteps;
                    }
                  },
                  scales: {
                    y: {
                      beginAtZero: true
                    }
                  }
                }
              });
            </script>

            <!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript">
              google.charts.load('current', {
                'packages': ['line', 'corechart']
              });
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {

                var chartDiv = document.getElementById('chart_div');

                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Date & Time');
                data.addColumn('number', "Heart Rate");

                data.addRows(<?php echo json_encode($arr_chart) ?>);

                var materialOptions = {
                  chart: {
                    title: 'Heart Rate Measurement',
                    color: '#1a237e',
                    fontSize: 24,
                    bold: true
                  },
                  width: 900,
                  height: 500,
                  series: {
                    // Gives each series an axis name that matches the Y-axis below.
                    0: {
                      axis: 'HeartRate'
                    }
                  },
                  axes: {
                    // Adds labels to each axis; they don't have to match the axis names.
                    y: {
                      HeartRate: {
                        label: 'HeartRate (BPM)',
                        color: '#1a237e',
                        fontSize: 24,
                        bold: true
                      }
                    }
                  },
                  colors: ['#a52714'],
                  backgroundColor: "#1a237e"
                };

                function drawMaterialChart() {
                  var materialChart = new google.charts.Line(chartDiv);
                  materialChart.draw(data, materialOptions);
                }
                drawMaterialChart();
              }
            </script> -->

          </div>
          <!-- <div id="chart_div"></div> -->


          <div class="tab-pane fade" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
            <div class="container-fluid">
              <div class="card">
                <div class="card-body">
                  <center>
                    <h4>Create an appointment</h4>
                  </center><br>
                  <form class="form-group" method="post" action="patient_portal.php">
                    <div class="row">

                      <div class="col-md-4">
                        <label for="spec">Specialization:</label>
                      </div>
                      <div class="col-md-8">
                        <select name="spec" class="form-control" id="spec">
                          <option value="" disabled selected>Select Specialization</option>
                          <?php
                          display_specs();
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

                          <?php display_docs(); ?>
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
            </div><br>
          </div>

          <div class="tab-pane fade" id="app-hist" role="tabpanel" aria-labelledby="list-pat-list">
            <table class="table table-hover">
              <thead>
                <tr>

                  <th scope="col">Appointment ID</th>
                  <th scope="col">Specialization</th>
                  <th scope="col">Doctor Name</th>
                  <th scope="col">Consultancy Fees</th>
                  <th scope="col">Appointment Date</th>
                  <th scope="col">Appointment Time</th>
                  <th scope="col">Current Status</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php

                //var_dump($spreadsheet);
                // Fetch the rows
                $range = 'Appointments';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $rows = $response->getValues();

                $array = [];
                foreach ($rows as $row) {
                  if ($row[2] == $pid) {
                ?>
                    <td><?php echo $row[1]; ?></td>
                    <td><?php echo $row[8]; ?></td>
                    <td><?php echo $row[9]; ?></td>
                    <td><?php echo $row[10]; ?></td>
                    <td><?php echo $row[11]; ?></td>
                    <td><?php echo $row[12]; ?></td>
                    <td><?php echo $row[13]; ?></td>

                    <td>
                      <?php if ($row[13] == "Active") { ?>
                        <a href="patient_portal.php?ID=<?php echo $row[1] ?>&cancel=update" onClick="return confirm('Are you sure you want to cancel this appointment ?')" title="Cancel Appointment" tooltip-placement="top" tooltip="Remove"><button class="btn btn-danger">Cancel</button></a>
                      <?php } else {

                        echo "Cancelled";
                      } ?>

                    </td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
            <br>
          </div>



          <div class="tab-pane fade" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Prescribed On</th>
                  <th scope="col">Doctor Name</th>
                  <th scope="col">Appointment ID</th>
                  <th scope="col">Appointment Date</th>
                  <th scope="col">Appointment Time</th>
                  <th scope="col">Diseases</th>
                  <th scope="col">Allergies</th>
                  <th scope="col">Prescriptions</th>
                  <th scope="col">Download</th>
                </tr>
              </thead>
              <tbody>
                <?php

                $range = 'Prescriptions';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $rows = $response->getValues();

                foreach ($rows as $row) {
                  if ($row[3] == $pid) {
                ?>
                    <td><?php echo $row[0]; ?></td>
                    <td><?php echo $row[2]; ?></td>
                    <td><?php echo $row[1]; ?></td>
                    <td><?php echo $row[6]; ?></td>
                    <td><?php echo $row[7]; ?></td>
                    <td><?php echo $row[8]; ?></td>
                    <td><?php echo $row[9]; ?></td>
                    <td><?php echo $row[10]; ?></td>
                    <td>
                      <form method="get">
                        <a href="patient_portal.php?ID=<?php echo $row[1] ?>" target="_blank">
                          <input type="hidden" name="ID" value="<?php echo $row[1] ?>" />
                          <input type="submit" onclick="alert('Downloading');" name="generate_pres" class="btn btn-success" value="Download" />
                        </a>
                    </td>
                    </form>


                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
            <br>
          </div>

          <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
            <form class="form-group" method="post" action="func.php">
              <label>Doctors name: </label>
              <input type="text" name="name" placeholder="Enter doctors name" class="form-control">
              <br>
              <input type="submit" name="doc_sub" value="Add Doctor" class="btn btn-primary">
            </form>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.10.1/sweetalert2.all.min.js">
  </script>



</body>

</html>