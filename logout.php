<?php
session_start();
require 'vendor/autoload.php';
//include('func.php');

$pid = $_SESSION['pid'];

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
$range = 'LoginDetails'; // the service will detect the last row of this sheet
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$rows = $response->getValues();
$options = ['valueInputOption' => 'USER_ENTERED'];
for ($i = sizeof($rows) - 1; $i > 0; $i--) {
  if ($rows[$i][0] == $pid) {
    date_default_timezone_set('Asia/Kolkata');
    $logouttime = date('d-m-y h:i:sa');
    $body = new \Google_Service_Sheets_ValueRange(['values' => [[$logouttime, "Logged Out"]]]);

    $params = ['valueInputOption' => 'USER_ENTERED'];
    $service->spreadsheets_values->update($spreadsheetId, $range . '!I' . ($i + 1), $body, $params);
    break;
  }
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

  <style>
    .btn-outline-light:hover {
      color: #0076d4;
      background-color: #f8f9fa;
      border-color: #f8f9fa;
    }
  </style>
</head>

<body style="background: -webkit-linear-gradient(left, #3931af, #00c6ff);color:white;padding-top:100px;text-align:center;">
  <h3>You have logged out.</h3><br><br>
  <a href="patient_login.php" class="btn btn-outline-light">Back to Login Page</a>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>

</html>