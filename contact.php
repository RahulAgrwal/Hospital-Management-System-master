<?php
require 'vendor/autoload.php';
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

if (isset($_POST['btnSubmit'])) {

	date_default_timezone_set('Asia/Kolkata');
	$query_date = date('d-m-y h:i:sa');
	$name = $_POST['txtName'];
	$email = $_POST['txtEmail'];
	$contact = $_POST['txtPhone'];
	$message = $_POST['txtMsg'];

	$newRow = [$query_date, $name, $email, $contact, $message];
	$rows = [$newRow]; // you can append several rows at once
	$valueRange = new \Google_Service_Sheets_ValueRange();
	$valueRange->setValues($rows);
	$range = 'Queries'; // the service will detect the last row of this sheet
	$options = ['valueInputOption' => 'USER_ENTERED'];
	$result = $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);

	if ($result) {
		echo '<script type="text/javascript">';
		echo 'alert("Message sent successfully!");';
		echo 'window.location.href = "contact.html";';
		echo '</script>';
	}
}
