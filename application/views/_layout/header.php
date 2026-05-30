<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url() ?>assets/images/pura_logo.ico" type="image/ico" />
    <title><?= $GLOBALS['nama_project'] ?> </title>

    <!-- Bootstrap -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet" />
    <!-- bootstrap-daterangepicker -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Select2 -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">

    <!-- Datatables -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?= $GLOBALS['assets'] ?>vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="<?= $GLOBALS['assets'] ?>vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="<?= $GLOBALS['assets'] ?>vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="<?= $GLOBALS['assets'] ?>vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?= $GLOBALS['assets'] ?>css/custom.min.css" rel="stylesheet">

    <style>
        body {
            zoom: 80%;
        }

        /* HEADER DATATABLE */
        table.dataTable thead th {
            font-size: 14px;
            font-weight: bold !important;
            color: #000000 !important;

            text-align: center !important;
            vertical-align: middle !important;
        }

        /* FILTER ROW */
        table.dataTable thead input {
            font-size: 11px;
            font-weight: normal;
        }
    </style>

    <?php
    if (!empty($css)) {
        if (is_file(APPPATH . 'views/_style/' . $css . '.php')) {
            include_once APPPATH . 'views/_style/' . $css . '.php';
        }
    }
    ?>

    <!-- Style Tiap Form -->
    <?php // include_once 'header/h_monprod.php'; 
    ?>
    <?php // include_once 'header/h_dashoeekk.php'; 
    ?>

</head>