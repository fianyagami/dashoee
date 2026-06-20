<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url() ?>assets/images/pura_logo.ico" type="image/ico" />
    <title><?= $GLOBALS['project_head'] ?> - <?= isset($judul) ? $judul : ''; ?> </title>

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
            zoom: 90%;
        }

        .project-title {
            height: auto !important;
            white-space: normal !important;
            line-height: 25px;
            padding: 10px;
        }

        .project-title span {
            display: inline-block;
            white-space: normal !important;
            word-wrap: break-word;
            max-width: 160px;
            vertical-align: middle;
        }

        .x_title h2 {
            font-weight: bold;
            color: #2A3F54;
        }

        /*===( Tabel secara General )=== */
        .table thead tr:first-child th {
            /* .table thead th th.main-header th.group-header { */
            background: #2A3F54 !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;

            text-align: center !important;
            vertical-align: middle !important;
        }

        .table tbody tr.row-selected {
            background-color: #27db93 !important;
            font-weight: bold;
        }

        .table.dataTable tbody tr {
            cursor: pointer;
        }

        .table tbody td {
            padding: 6px 8px !important;
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

        /* == */
        #tblDetail {
            width: 100% !important;
        }

        #tblDetail th,
        #tblDetail td {
            white-space: nowrap;
            vertical-align: middle;
        }

        #tblDetail thead input {
            width: 100%;
            min-width: 70px;
            font-size: 11px;
            padding: 3px;
        }

        #tblProjek tbody tr.selected {
            background: #1ABB9C !important;
            color: white;
        }

        .filter-row input {
            width: 100%;
            min-width: 70px;
            font-size: 11px;
            padding: 3px;
        }

        /* Button Browse */
        .btn-cari {
            background: #26B99A;
            border: 1px solid #169F85;
            color: #FFF;
            width: 100%;
            font-weight: 600;
        }

        .btn-cari:hover {
            background: #169F85;
            border-color: #148F77;
            color: #FFF;
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