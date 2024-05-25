
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
//var_dump($spreadsheet);

function generate_prescription()
{
    global $service;
    global $spreadsheetId;
    $range = 'Prescriptions';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $rows = $response->getValues();
    $appid = $_GET['ID'];
    $output = "";

    foreach ($rows as $row) {
        if ($row[1] == $appid) {
            $output .= <<<EOD
      <table border="1" cellpadding="8" cellspacing="2" font-size:40px; style="font-size:16px;">
      <tr style="font-size:20px;">
        <td width="600" align="center"><b>Prescription</b></td>        
       </tr>
       <tr style="background-color:#FF0000;color:#FFFF00;">
        <td width="40" align="center"><b>A</b></td>
        <td width="560" ><b>Appointment Details</b></td>
       </tr>
       <tr>
        <td width="40" align="center">1.</td>
        <td width="180">Appointment ID </td>
        <td width="380"> $row[1] </td>        
       </tr>
       <tr>
        <td width="40" align="center">2.</td>
        <td width="180" >Doctor Name </td>
        <td width="380"> $row[2] </td>
       </tr>
       <tr>
        <td width="40" align="center">3.</td>
        <td width="180" >Appointment Date </td>
        <td width="380"> $row[6] </td>
       </tr>
       <tr>
        <td width="40" align="center">4.</td>
        <td width="180" >Appointment Time </td>
        <td width="380"> $row[7] </td>
       </tr>

       <tr style="background-color:#FF0000;color:#FFFF00;">
        <td width="40" align="center"><b>B</b></td>
        <td width="560" ><b>Prescription Details</b></td>
       </tr>
       
       <tr>
        <td width="40" align="center">1.</td>
        <td width="180" >Prescription Date </td>
        <td width="380"> $row[0] </td>
       </tr>
       <tr>
        <td width="40" align="center">2.</td>
        <td width="180">Patient ID </td>
        <td width="380"> $row[3] </td>
       </tr>
       <tr>
        <td width="40" align="center">3.</td>
        <td width="180" >Patient Name </td>
        <td width="380"> $row[4] $row[5]</td>
       </tr>
       <tr>
        <td width="40" align="center">4.</td>
        <td width="180" >Diseases </td>
        <td width="380"> $row[8] </td>
       </tr>
       <tr>
        <td width="40" align="center">5.</td>
        <td width="180" >Allergies </td>
        <td width="380"> $row[9] </td>
       </tr>
       <tr>
        <td width="40" align="center">6.</td>
        <td width="180" >Prescription </td>
        <td width="380"> $row[10] </td>
       </tr>
      </table>
      
      EOD;
      
            break;
        }
    }
    // $output.='<img src=" https://engg.dypvp.edu.in/images/logoDpu1.png ">';
    return $output;
}

if (isset($_GET["generate_pres"])) {
    // Include the main TCPDF library (search for installation path).
    require_once('TCPDF/tcpdf.php');
    // class MYPDF extends TCPDF
    // {

    //     //Page header
    //     public function Header()
    //     {
    //         // Logo
    //         $image_file = "img\logo.jpg";
    //         $this->Image($image_file, 15, 10, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    //         // Set font
    //         $this->SetFont('helvetica', 'B', 20);
    //         // Title
    //         $this->Cell(0, 15, 'Dr. D.Y.Patil Institute of Technology', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    //         $this->Cell(0, 15, 'Patil Institute of Technol', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    //     }
    // }
    // $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->setCreator(PDF_CREATOR);
    $pdf->setAuthor('Nicola Asuni');
    $pdf->SetTitle($_GET['ID'] . " Prescription");
    $pdf->setSubject('TCPDF Tutorial');
    $pdf->setKeywords('TCPDF, PDF, example, test, guide');
    $img = "https://dpu.edu.in/img/logo.png";
    // set default header data
    $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Dr. D.Y.Patil Institute of Technology", "Sant Tukaram Nagar, Pimpri, Pune \nhttps://engg.dypvp.edu.in/");

    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', 12));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', 12));

    // set default monospaced font
    $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------

    // set font
    $pdf->setFont('helvetica', 'B', 20);
    // add a page
    $pdf->AddPage();

    // $pdf->Write(2, 'Prescription', '', 0, 'L', true, 0, false, false, 0);

    $pdf->setFont('helvetica', '', 8);

    // Table with rowspans and THEAD
    $tbl = generate_prescription();

    $pdf->writeHTML($tbl, true, false, false, false, '');
    // -----------------------------------------------------------------------------

    ob_end_clean();
    //Close and output PDF document
    $pdf->Output($_GET['ID'] . " Prescription.pdf", 'I');
}


?>