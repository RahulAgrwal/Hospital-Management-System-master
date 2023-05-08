<?php
// session_start();
require 'vendor/autoload.php';

// if(isset($_POST['submit'])){
//  $username=$_POST['username'];
//  $password=$_POST['password'];
//  $query="select * from logintb where username='$username' and password='$password';";
//  $result=mysqli_query($con,$query);
//  if(mysqli_num_rows($result)==1)
//  {
//   $_SESSION['username']=$username;
//   $_SESSION['pid']=
//   header("Location:admin-panel.php");
//  }
//  else
//   header("Location:error.php");
// }
// if (isset($_POST['update_data'])) {
//   $contact = $_POST['contact'];
//   $status = $_POST['status'];
//   $query = "update appointmenttb set payment='$status' where contact='$contact';";
//   $result = mysqli_query($con, $query);
//   if ($result)
//     header("Location:updated.php");
// }

// function display_docs()
// {
//  global $con;
//  $query="select * from doctb";
//  $result=mysqli_query($con,$query);
//  while($row=mysqli_fetch_array($result))
//  {
//   $username=$row['username'];
//   $price=$row['docFees'];
//   echo '<option value="' .$username. '" data-value="'.$price.'">'.$username.'</option>';
//  }
// }


function display_specs()
{
  //Google Client
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
  // Fetch the rows
  $range = 'doctordb';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  $arr = [];
  $idx = 0;
  for ($i=1; $i<sizeof($rows);$i++) {
    $arr[$idx++] = $rows[$i][3];
  }
  $arrunique = array_unique($arr);
  foreach ($arrunique as $spec) {
    echo '<option data-value="' . $spec . '">' . $spec . '</option>';
  }


  // global $con;
  // $query="select distinct(spec) from doctb";
  // $result=mysqli_query($con,$query);
  // while($row=mysqli_fetch_array($result))
  // {
  //   $spec=$row['spec'];
  //   echo '<option data-value="'.$spec.'">'.$spec.'</option>';
  // }
}

function display_docs()
{
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
  // Fetch the rows
  $range = 'doctordb';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  foreach ($rows as $row) {
    $username = $row[0];
    $price = $row[4];
    $spec = $row[3];
    echo '<option value="' . $username . '" data-value="' . $price . '" data-spec="' . $spec . '">' . $username . '</option>';
  }
  // global $con;
  // $query = "select * from doctb";
  // $result = mysqli_query($con, $query);
  // while ($row = mysqli_fetch_array($result)) {
  //   $username = $row['username'];
  //   $price = $row['docFees'];
  //   $spec = $row['spec'];
  //   echo '<option value="' . $username . '" data-value="' . $price . '" data-spec="' . $spec . '">' . $username . '</option>';
  // }
}

// function display_specs() {
//   global $con;
//   $query = "select distinct(spec) from doctb";
//   $result = mysqli_query($con,$query);
//   while($row = mysqli_fetch_array($result))
//   {
//     $spec = $row['spec'];
//     $username = $row['username'];
//     echo '<option value = "' .$spec. '">'.$spec.'</option>';
//   }
// }


if (isset($_POST['doc_sub'])) {
  $username = $_POST['username'];
  $query = "insert into doctb(username)values('$username')";
  $result = mysqli_query($con, $query);
  if ($result)
    header("Location:adddoc.php");
}
