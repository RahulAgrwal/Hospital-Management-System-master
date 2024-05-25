<!DOCTYPE html>
<?php
include('generate_prescription.php');
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

$dash_doctors = getDoctors();
$dash_patients = getPatients();
$dash_appointments = getAppointments();
$dash_prescriptions = getPrescriptions();
$dash_queries = getQueries();

function getDoctors()
{
  global $service;
  global $spreadsheetId;
  $range = 'doctordb';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  if ($rows) return $rows;
  return [];
}
function getPatients()
{
  global $service;
  global $spreadsheetId;
  $range = 'registrations';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  if ($rows) return $rows;
  return [];
}
function getAppointments()
{
  global $service;
  global $spreadsheetId;
  $range = 'Appointments';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  if ($rows) return $rows;
  return [];
}
function getPrescriptions()
{
  global $service;
  global $spreadsheetId;
  $range = 'Prescriptions';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  if ($rows) return $rows;
  return [];
}

function getQueries()
{
  global $service;
  global $spreadsheetId;
  $range = "Queries";
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $rows = $response->getValues();
  if ($rows) return $rows;
  return [];
}

if (isset($_POST['docsub'])) {
  date_default_timezone_set('Asia/Kolkata');
  $doctor = $_POST['doctor'];
  $dpassword = $_POST['dpassword'];
  $demail = $_POST['demail'];
  $spec = $_POST['special'];
  $docFees = $_POST['docFees'];
  $doctor_add_on = date('m/d/Y H:i:s');
  $rows = [[$doctor, $demail, $dpassword, $spec, $docFees, $doctor_add_on]]; // you can append several rows at once
  $valueRange = new \Google_Service_Sheets_ValueRange();
  $valueRange->setValues($rows);
  $range = 'doctordb'; // the service will detect the last row of this sheet
  $options = ['valueInputOption' => 'USER_ENTERED'];
  $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
  echo "<script>alert('Doctor added successfully!');</script>";
}

if (isset($_POST['deletedoc'])) {
  $demail = $_POST['demail'];
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
        'sheetId'   => '891902770', // <======= This mean the very first sheet on worksheet
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
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

<!-- GOOGLE MAP API SCRIPT -->
<script type="module" crossorigin>
  (function() {
    const n = document.createElement("link").relList;
    if (n && n.supports && n.supports("modulepreload")) return;
    for (const t of document.querySelectorAll('link[rel="modulepreload"]'))
      s(t);
    new MutationObserver((t) => {
      for (const o of t)
        if (o.type === "childList")
          for (const i of o.addedNodes)
            i.tagName === "LINK" && i.rel === "modulepreload" && s(i);
    }).observe(document, {
      childList: !0,
      subtree: !0
    });

    function r(t) {
      const o = {};
      return (
        t.integrity && (o.integrity = t.integrity),
        t.referrerpolicy && (o.referrerPolicy = t.referrerpolicy),
        t.crossorigin === "use-credentials" ?
        (o.credentials = "include") :
        t.crossorigin === "anonymous" ?
        (o.credentials = "omit") :
        (o.credentials = "same-origin"),
        o
      );
    }

    function s(t) {
      if (t.ep) return;
      t.ep = !0;
      const o = r(t);
      fetch(t.href, o);
    }
  })();
  /**
   * @license
   * Copyright 2019 Google LLC. All Rights Reserved.
   * SPDX-License-Identifier: Apache-2.0
   */
  let l,
    u,
    p,
    c = [],
    a;
  const I = {
      country: "us"
    },
    f =
    "https://developers.google.com/maps/documentation/javascript/images/marker_green",
    E = new RegExp("^https?://.+?/"),
    d = {
      au: {
        center: {
          lat: -25.3,
          lng: 133.8
        },
        zoom: 4
      },
      br: {
        center: {
          lat: -14.2,
          lng: -51.9
        },
        zoom: 3
      },
      ca: {
        center: {
          lat: 62,
          lng: -110
        },
        zoom: 3
      },
      fr: {
        center: {
          lat: 46.2,
          lng: 2.2
        },
        zoom: 5
      },
      de: {
        center: {
          lat: 51.2,
          lng: 10.4
        },
        zoom: 5
      },
      mx: {
        center: {
          lat: 23.6,
          lng: -102.5
        },
        zoom: 4
      },
      nz: {
        center: {
          lat: -40.9,
          lng: 174.9
        },
        zoom: 5
      },
      it: {
        center: {
          lat: 41.9,
          lng: 12.6
        },
        zoom: 5
      },
      za: {
        center: {
          lat: -30.6,
          lng: 22.9
        },
        zoom: 5
      },
      es: {
        center: {
          lat: 40.5,
          lng: -3.7
        },
        zoom: 5
      },
      pt: {
        center: {
          lat: 39.4,
          lng: -8.2
        },
        zoom: 6
      },
      us: {
        center: {
          lat: 37.1,
          lng: -95.7
        },
        zoom: 3
      },
      uk: {
        center: {
          lat: 54.8,
          lng: -4.6
        },
        zoom: 5
      },
    };

  function C() {
    (l = new google.maps.Map(document.getElementById("map"), {
      zoom: d.us.zoom,
      center: d.us.center,
      mapTypeControl: !1,
      panControl: !1,
      zoomControl: !1,
      streetViewControl: !1,
    })),
    (p = new google.maps.InfoWindow({
      content: document.getElementById("info-content"),
    })),
    (a = new google.maps.places.Autocomplete(
      document.getElementById("autocomplete"), {
        types: ["(cities)"],
        componentRestrictions: I,
        fields: ["geometry"],
      }
    )),
    (u = new google.maps.places.PlacesService(l)),
    a.addListener("place_changed", b),
      document.getElementById("country").addEventListener("change", z);
  }

  function b() {
    const e = a.getPlace();
    e.geometry && e.geometry.location ?
      (l.panTo(e.geometry.location), l.setZoom(15), B()) :
      (document.getElementById("autocomplete").placeholder =
        "Enter a city");
  }

  function B() {
    const e = {
      bounds: l.getBounds(),
      types: ["doctors"]
    };
    u.nearbySearch(e, (n, r, s) => {
      if (r === google.maps.places.PlacesServiceStatus.OK && n) {
        h(), y();
        for (let t = 0; t < n.length; t++) {
          const o = String.fromCharCode("A".charCodeAt(0) + (t % 26)),
            i = f + o + ".png";
          (c[t] = new google.maps.Marker({
            position: n[t].geometry.location,
            animation: google.maps.Animation.DROP,
            icon: i,
          })),
          (c[t].placeResult = n[t]),
          google.maps.event.addListener(c[t], "click", L),
            setTimeout(k(t), t * 100),
            v(n[t], t);
        }
      }
    });
  }

  function y() {
    for (let e = 0; e < c.length; e++) c[e] && c[e].setMap(null);
    c = [];
  }

  function z() {
    const e = document.getElementById("country").value;
    e == "all" ?
      (a.setComponentRestrictions({
          country: []
        }),
        l.setCenter({
          lat: 15,
          lng: 0
        }),
        l.setZoom(2)) :
      (a.setComponentRestrictions({
          country: e
        }),
        l.setCenter(d[e].center),
        l.setZoom(d[e].zoom)),
      h(),
      y();
  }

  function k(e) {
    return function() {
      c[e].setMap(l);
    };
  }

  function v(e, n) {
    const r = document.getElementById("results"),
      s = String.fromCharCode("A".charCodeAt(0) + (n % 26)),
      t = f + s + ".png",
      o = document.createElement("tr");
    (o.style.backgroundColor = n % 2 === 0 ? "#F0F0F0" : "#FFFFFF"),
    (o.onclick = function() {
      google.maps.event.trigger(c[n], "click");
    });
    const i = document.createElement("td"),
      g = document.createElement("td"),
      m = document.createElement("img");
    (m.src = t),
    m.setAttribute("class", "placeIcon"),
      m.setAttribute("className", "placeIcon");
    const w = document.createTextNode(e.name);
    i.appendChild(m),
      g.appendChild(w),
      o.appendChild(i),
      o.appendChild(g),
      r.appendChild(o);
  }

  function h() {
    const e = document.getElementById("results");
    for (; e.childNodes[0];) e.removeChild(e.childNodes[0]);
  }

  function L() {
    const e = this;
    u.getDetails({
      placeId: e.placeResult.place_id
    }, (n, r) => {
      r === google.maps.places.PlacesServiceStatus.OK &&
        (p.open(l, e), M(n));
    });
  }

  function M(e) {
    if (
      ((document.getElementById("iw-icon").innerHTML =
          '<img class="hotelIcon" src="' + e.icon + '"/>'),
        (document.getElementById("iw-url").innerHTML =
          '<b><a href="' + e.url + '">' + e.name + "</a></b>"),
        (document.getElementById("iw-address").textContent = e.vicinity),
        e.formatted_phone_number ?
        ((document.getElementById("iw-phone-row").style.display = ""),
          (document.getElementById("iw-phone").textContent =
            e.formatted_phone_number)) :
        (document.getElementById("iw-phone-row").style.display = "none"),
        e.rating)
    ) {
      let n = "";
      for (let r = 0; r < 5; r++)
        e.rating < r + 0.5 ? (n += "&#10025;") : (n += "&#10029;"),
        (document.getElementById("iw-rating-row").style.display = ""),
        (document.getElementById("iw-rating").innerHTML = n);
    } else document.getElementById("iw-rating-row").style.display = "none";
    if (e.website) {
      e.website;
      let n = String(E.exec(e.website));
      n || (n = "http://" + e.website + "/"),
        (document.getElementById("iw-website-row").style.display = ""),
        (document.getElementById("iw-website").textContent = n);
    } else document.getElementById("iw-website-row").style.display = "none";
  }
  window.initMap = C;
</script>
<!-- GOOGLE MAP API SCRIPT /-->
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
          <span id="lblTitle" class="hidden-xs" style="color:White;font-size:X-Large;">Hospital Management System | Admin Portal</span>
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
            <a href="#" class="d-block" style="color:white">Admin</a>
          </div>
        </div>
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <i class="nav-icon fas fa-id-card-alt"></i>
          </div>
          <div class="info">
            <a href="#" class="d-block" style="color:white">Admin Access</a>
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
              <a href="#list-doc" id="list-doc-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fa fa-user-md"></i>
                <p>
                  Doctor List
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_doctors) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-pat" id="list-pat-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fas fa-book-medical"></i>
                <p>
                  Patient List
                  <i class="right fas fa-angle-left"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_patients) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-app" id="list-app-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fas fa-notes-medical"></i>
                <p>
                  Appointment Details
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_appointments) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-pres" id="list-pres-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fas fa-file-prescription"></i>
                <p>
                  Prescription List
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_prescriptions) ?></span>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#add-doc" id="list-adoc-list" data-toggle="list" role="tab" aria-controls="home" class="nav-link">
                <i class="nav-icon fa fa-plus"></i>
                <p>
                  Add Doctor
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#delete-doc" id="list-ddoc-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fa fa-trash"></i>
                <p>
                  Delete Doctor
                  <i class="fas fa-angle-left right"></i>

                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#list-mes" id="list-mes-list" role="tab" data-toggle="list" aria-controls="home" class="nav-link">
                <i class="nav-icon fa fa-question-circle"></i>
                <p>
                  Queries
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right"><?php echo sizeof($dash_queries) ?></span>
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
                        echo sizeof($dash_doctors);
                        ?></h3>

                      <p>Doctors</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-user-md"></i>
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
                      <h3>
                        <?php


                        echo sizeof($dash_patients);
                        ?>
                      </h3>

                      <p>Patients</p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-book-medical"></i>
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
                <div class="col-12 col-sm-6 col-md-3 ">
                  <!-- small card -->
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>
                        <?php
                        echo sizeof($dash_prescriptions);
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

                <div class="col-12 col-sm-6 col-md-3 ">
                  <!-- small card -->
                  <div class="small-box bg-secondary">
                    <div class="inner">
                      <h3>
                        <?php
                        echo sizeof($dash_queries);
                        ?></h3>

                      <p>Queries</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-question-circle"></i>
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

      <!-- DOCTOR LIST -->
      <div class="tab-pane fade" id="list-doc" role="tabpanel" aria-labelledby="list-doc-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Doctor List</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Doctor List</li>
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
                      <h3 class="card-title">Doctor List</h3>

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
                            $rows = $dash_doctors;
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
                            }
                            ?>
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
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->

        </div>


      </div>
      <!-- /DOCTORS LIST -->

      <!-- PATIENT LIST -->
      <div class="tab-pane fade" id="list-pat" role="tabpanel" aria-labelledby="list-pat-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Patient List</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Patient List</li>
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
                      <h3 class="card-title">Patient List</h3>

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
                              <th scope="col">Contact</th>
                              <th scope="col">Gender</th>
                              <th scope="col">Email</th>
                              <th scope="col">Password</th>
                              <th scope="col">Registered On</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $rows = $dash_patients;
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
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /PATIENT LIST -->

      <!-- APPOINTMENT DETAILS -->
      <div class="tab-pane fade" id="list-app" role="tabpanel" aria-labelledby="list-app-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Appointment Details</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Appointment Details</li>
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
                      <h3 class="card-title">Appointment Details</h3>

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
                            $rows = $dash_appointments;
                            for ($x = 1; $x < sizeof($rows); $x++) {
                            ?>
                              <tr>
                                <td>
                                  <?php echo $rows[$x][1]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][2]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][3]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][4]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][6]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][7]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][5]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][8]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][9]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][10]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][11]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][12]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][13]; ?>
                                </td>
                              </tr>
                            <?php
                            } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.row -->
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /APPOINTMENT DETAILS -->

      <!-- Prescription LIST -->
      <div class="tab-pane fade" id="list-pres" role="tabpanel" aria-labelledby="list-pres-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Prescription List</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Prescription List</li>
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
                      <h3 class="card-title">Prescription List</h3>

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
                            $rows = $dash_prescriptions;
                            for ($x = 1; $x < sizeof($rows); $x++) {
                            ?>
                              <tr>
                                <td>
                                  <?php echo $rows[$x][0]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][1]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][2]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][3]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][4]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][5]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][6]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][7]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][8]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][9]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$x][10]; ?>
                                </td>
                                <td>
                                  <form method="get">
                                    <a href="generate_prescription.php?ID=<?php echo $rows[$x][1] ?>" target="_blank">
                                      <input type="hidden" name="ID" value="<?php echo $rows[$x][1] ?>" />
                                      <input type="submit" name="generate_pres" class="btn btn-success" value="Download" />
                                    </a>
                                  </form>
                                </td>
                              </tr>
                            <?php
                            } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.row -->
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /Prescription LIST -->


      <!-- ADD DOC -->
      <div class="tab-pane fade" id="add-doc" role="tabpanel" aria-labelledby="add-doctor">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Add Doctor</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Add Doctor</li>
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
                      <h3 class="card-title text-center">Add Doctor</h3>

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
                  </div>
                </div>
              </div>
            </div>
            <!--/. container-fluid -->
          </section>
          <!-- /.content -->
        </div>
      </div>
      <!-- /ADD DOCTOR -->

      <!--DELETE DOCTOR -->
      <div class="tab-pane fade" id="delete-doc" role="tabpanel" aria-labelledby="delete-doctor">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Delete Doctor</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Delete Doctor</li>
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
                      <h3 class="card-title text-center">Delete Doctor</h3>

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
                      <form class="form-group" method="post" action="admin-panel1.php">
                        <div class="row">

                          <div class="col-md-4"><label>Email ID:</label></div>
                          <div class="col-md-8"><input type="email" class="form-control" name="demail" required></div><br><br>

                        </div>
                        <input type="submit" name="deletedoc" value="Delete Doctor" class="btn btn-primary" onclick="confirm('Do you really want to delete?')">
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
      <!-- /DELETE DOCTOR -->




      <!-- QUERIES -->
      <div class="tab-pane fade" id="list-mes" role="tabpanel" aria-labelledby="list-mes-list">
        <div class="content-wrapper">
          <!-- Content Header (Page header) -->
          <div class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1 class="m-0">Queries</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Queries</li>
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
                      <h3 class="card-title">Queries</h3>

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
                              <th scope="col">Registered On</th>
                              <th scope="col">User Name</th>
                              <th scope="col">Email</th>
                              <th scope="col">Contact</th>
                              <th scope="col">Message</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $rows = $dash_queries;
                            for ($i = 1; $i < sizeof($rows); $i++) {
                            ?>
                              <tr>
                                <td>
                                  <?php echo $rows[$i][0]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$i][1]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$i][2]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$i][3]; ?>
                                </td>
                                <td>
                                  <?php echo $rows[$i][4]; ?>
                                </td>
                              </tr>
                            <?php
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
      <!-- /QUERIES -->





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

  <script>
    // HEART RATE DOUGHNUT CHART
    const heartrate_doughnut = document.getElementById('heartrate_doughnut')
    var heartrate_donutData = {
      labels: [
        'Low',
        'Normal',
        'High',
      ],
      datasets: [{
        data: [<?php echo $heart_low ?>, <?php echo $heart_normal ?>, <?php echo $heart_high ?>],
        backgroundColor: ['yellow', 'green', 'red'],
      }]
    }
    var heartrate_donutOptions = {
      maintainAspectRatio: false,
      responsive: true,
    }
    new Chart(heartrate_doughnut, {
      type: 'doughnut',
      data: heartrate_donutData,
      options: heartrate_donutOptions
    })


    // Body TEMPERATURE DOUGHNUT CHART
    const bodyTemp_doughnut = document.getElementById('bodyTemp_doughnut')
    var bodyTemp_donutData = {
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
        data: <?php echo $bodytemp_results ?>,
        backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', 'black'],
      }]
    }
    var bodyTemp_donutOptions = {
      maintainAspectRatio: false,
      responsive: true,
    }
    new Chart(bodyTemp_doughnut, {
      type: 'doughnut',
      data: heartrate_donutData,
      options: heartrate_donutOptions
    })
  </script>
</body>

</html>