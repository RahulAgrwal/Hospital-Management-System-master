<?php
require 'vendor/autoload.php';
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);

if (isset($_POST['patsub1'])) {

  date_default_timezone_set('Asia/Kolkata');
  $pid = "P" . date('ymdHis');;
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $gender = $_POST['gender'];
  $email = $_POST['email'];
  $contact = $_POST['contact'];
  $password = $_POST['password'];
  $cpassword = $_POST['cpassword'];
  $registered_on = date('d-m-y h:i:sa');
  if ($password == $cpassword) {
    // configure the Google Client
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



    $newRow = [$pid, $fname, $lname, $contact, $gender, $email, $password, $cpassword, $registered_on];
    $rows = [$newRow]; // you can append several rows at once
    $valueRange = new \Google_Service_Sheets_ValueRange();
    $valueRange->setValues($rows);
    $range = 'registrations'; // the service will detect the last row of this sheet
    $options = ['valueInputOption' => 'USER_ENTERED'];
    $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);

    //Create New Sheet

    $body = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
      'requests' => array('addSheet' => array('properties' => array('title' => $pid)))
    ));

    $service->spreadsheets->batchUpdate($spreadsheetId, $body);


    echo "<script>alert('Registered successfully booked!! Mail Sent');</script>";

    header("Location:patient_login.php");
  } else {
    header("Location:error1.php");
  }

  try {
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
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
    $mail->Subject = "Registered Successfully ! Send email using Gmail SMTP and PHPMailer";
    $mail->Body = "
      <img src='https://dpu.edu.in/img/logo.png' style='height:50px;'>
      <h2>Wireless HealthCare System using IoMT with integration of Big Data</h2>
      <h3>----------------------------</h3>
      <h2>Patient Registration Credentials</h2>
      <h3>Patient ID : $pid </h3>
      <h3>Patient Name : $fname $lname </h3>
      <h3>Mobile No. : $contact  </h3>
      <h3>Gender : $gender  </h3>
      <h3>Email : $email </h3>
      <h3>Password : $password  </h3>
      <h3>----------------------------</h3>
      <p>Thankyou for Registration!! Have a Nice day.</p>
      <h3>Registered on : $registered_on </h3>";

    $mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';

    $mail->send();
  } catch (Exception $e) {
    //echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
  }
}





// function display_docs()
// {
// 	global $con;
// 	$query="select * from doctb";
// 	$result=mysqli_query($con,$query);
// 	while($row=mysqli_fetch_array($result))
// 	{
// 		$name=$row['name'];
// 		# echo'<option value="" disabled selected>Select Doctor</option>';
// 		echo '<option value="'.$name.'">'.$name.'</option>';
// 	}
// }


function display_admin_panel(){
	echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
      <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <a class="navbar-brand" href="#"><i class="fa fa-user-plus" aria-hidden="true"></i> Global Hospital</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
     <ul class="navbar-nav mr-auto">
       <li class="nav-item">
        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
      </li>
       <li class="nav-item">
        <a class="nav-link" href="#"></a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0" method="post" action="search.php">
      <input class="form-control mr-sm-2" type="text" placeholder="enter contact number" aria-label="Search" name="contact">
      <input type="submit" class="btn btn-outline-light my-2 my-sm-0 btn btn-outline-light" id="inputbtn" name="search_submit" value="Search">
    </form>
  </div>
</nav>
  </head>
  <style type="text/css">
    button:hover{cursor:pointer;}
    #inputbtn:hover{cursor:pointer;}
  </style>
  <body style="padding-top:50px;">
 <div class="jumbotron" id="ab1"></div>
   <div class="container-fluid" style="margin-top:50px;">
    <div class="row">
  <div class="col-md-4">
    <div class="list-group" id="list-tab" role="tablist">
      <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Appointment</a>
      <a class="list-group-item list-group-item-action" href="patientdetails.php" role="tab" aria-controls="home">Patient List</a>
      <a class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">Payment Status</a>
      <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list" href="#list-messages" role="tab" aria-controls="messages">Prescription</a>
      <a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list" href="#list-settings" role="tab" aria-controls="settings">Doctors Section</a>
       <a class="list-group-item list-group-item-action" id="list-attend-list" data-toggle="list" href="#list-attend" role="tab" aria-controls="settings">Attendance</a>
    </div><br>
  </div>

  





  <div class="col-md-8">
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
        <div class="container-fluid">
          <div class="card">
            <div class="card-body">
              <center><h4>Create an appointment</h4></center><br>
              <form class="form-group" method="post" action="appointment.php">
                <div class="row">
                  <div class="col-md-4"><label>First Name:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control" name="fname"></div><br><br>
                  <div class="col-md-4"><label>Last Name:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control"  name="lname"></div><br><br>
                  <div class="col-md-4"><label>Email id:</label></div>
                  <div class="col-md-8"><input type="text"  class="form-control" name="email"></div><br><br>
                  <div class="col-md-4"><label>Contact Number:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control"  name="contact"></div><br><br>
                  <div class="col-md-4"><label>Doctor:</label></div>
                  <div class="col-md-8">
                   <select name="doctor" class="form-control" >

                     <!-- <option value="" disabled selected>Select Doctor</option>
                     <option value="Dr. Punam Shaw">Dr. Punam Shaw</option>
                      <option value="Dr. Ashok Goyal">Dr. Ashok Goyal</option> -->
                      <?php display_docs();?>




                    </select>
                  </div><br><br>
                  <div class="col-md-4"><label>Payment:</label></div>
                  <div class="col-md-8">
                    <select name="payment" class="form-control" >
                      <option value="" disabled selected>Select Payment Status</option>
                      <option value="Paid">Paid</option>
                      <option value="Pay later">Pay later</option>
                    </select>
                  </div><br><br><br>
                  <div class="col-md-4">
                    <input type="submit" name="entry_submit" value="Create new entry" class="btn btn-primary" id="inputbtn">
                  </div>
                  <div class="col-md-8"></div>                  
                </div>
              </form>
            </div>
          </div>
        </div><br>
      </div>
      <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
        <div class="card">
          <div class="card-body">
            <form class="form-group" method="post" action="func.php">
              <input type="text" name="contact" class="form-control" placeholder="enter contact"><br>
              <select name="status" class="form-control">
               <option value="" disabled selected>Select Payment Status to update</option>
                <option value="paid">paid</option>
                <option value="pay later">pay later</option>
              </select><br><hr>
              <input type="submit" value="update" name="update_data" class="btn btn-primary">
            </form>
          </div>
        </div><br><br>
      </div>
      <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">...</div>
      <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
        <form class="form-group" method="post" action="func.php">
          <label>Doctors name: </label>
          <input type="text" name="name" placeholder="enter doctors name" class="form-control">
          <br>
          <input type="submit" name="doc_sub" value="Add Doctor" class="btn btn-primary">
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
    <!--Sweet alert js-->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.all.js"></script>
   <script type="text/javascript">
   $(document).ready(function(){
   	swal({
  title: "Welcome!",
  text: "Have a nice day!",
  imageUrl: "images/sweet.jpg",
  imageWidth: 400,
  imageHeight: 200,
  imageAlt: "Custom image",
  animation: false
})</script>
  </body>
</html>';
}
?>
