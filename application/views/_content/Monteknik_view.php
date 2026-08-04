<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-monteknik">
    <div class="x_content">
        <div class="body">

            <div class="x_title">
                <h2>Monitoring PLP Teknik</h2>
                <div class="clearfix"></div>
            </div>

            <div class="row">

                <!-- FILTER -->
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">

                            <div class="row">

                                <div class="col-md-2">
                                    <label>Tahun <span class="text-danger">*</span></label>
                                    <select id="thn" class="form-control">
                                        <?php
                                        $tahun_sekarang = date('Y');
                                        for ($i = 2022; $i <= 2027; $i++) {
                                            $selected = ($i == $tahun_sekarang) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $i ?>" <?= $selected ?>>
                                                <?= $i ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label>Bulan <span class="text-danger">*</span></label>
                                    <?php $bulan = date('n'); ?>
                                    <select id="bln" class="form-control select2_single">
                                        <option value="1" <?= $bulan == 1 ? 'selected' : '' ?>>JANUARI</option>
                                        <option value="2" <?= $bulan == 2 ? 'selected' : '' ?>>FEBRUARI</option>
                                        <option value="3" <?= $bulan == 3 ? 'selected' : '' ?>>MARET</option>
                                        <option value="4" <?= $bulan == 4 ? 'selected' : '' ?>>APRIL</option>
                                        <option value="5" <?= $bulan == 5 ? 'selected' : '' ?>>MEI</option>
                                        <option value="6" <?= $bulan == 6 ? 'selected' : '' ?>>JUNI</option>
                                        <option value="7" <?= $bulan == 7 ? 'selected' : '' ?>>JULI</option>
                                        <option value="8" <?= $bulan == 8 ? 'selected' : '' ?>>AGUSTUS</option>
                                        <option value="9" <?= $bulan == 9 ? 'selected' : '' ?>>SEPTEMBER</option>
                                        <option value="10" <?= $bulan == 10 ? 'selected' : '' ?>>OKTOBER</option>
                                        <option value="11" <?= $bulan == 11 ? 'selected' : '' ?>>NOVEMBER</option>
                                        <option value="12" <?= $bulan == 12 ? 'selected' : '' ?>>DESEMBER</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Departemen</label>
                                    <select id="dept" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Mesin</label>
                                    <select id="mesin" class="form-control" disabled>
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button id="btnBrowseMonteknik" class="btn btn-cari">
                                        <i class="fa fa-search"></i>
                                        Browse
                                    </button>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <!-- DETAIL -->
                <div class="col-md-12">
                    <div class="x_panel">

                        <div class="x_title">
                            <h2>Detail PLP Teknik</h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">
                            <div style="overflow-x:auto; width:100%;">
                                <h2 id="infoMesin" class="text-left" style="font-size:16px; margin-bottom:15px;">
                                    Departemen : - | Mesin : -
                                </h2>
                                <div id="loadingDetail"
                                    style="display:none; padding:15px; margin-bottom:10px;"
                                    class="alert alert-info text-center">

                                    <i class="fa fa-spinner fa-spin"></i>
                                    Loading detail PLP Teknik...

                                </div>
                                <table id="tblDetail"
                                    class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="group-header">
                                            <th colspan="7">PLP</th>
                                            <th colspan="5">Waktu</th>
                                            <th colspan="2">Status</th>
                                        </tr>
                                        <tr class="main-header">
                                            <th>Tanggal PLP</th>
                                            <th>Kode PLP</th>
                                            <th>Departemen</th>
                                            <th>Mesin</th>
                                            <th>Pelapor</th>
                                            <th>Jenis Pekerjaan</th>
                                            <th>Request</th>

                                            <th>Waktu Request</th>
                                            <th>Waktu Perencanaan</th>
                                            <th>Waktu Start</th>
                                            <th>Waktu Finish</th>
                                            <th>Waktu Proses</th>

                                            <th>Status</th>
                                            <th>Konfirmasi</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="filter-row">
                                            <th>Tanggal PLP</th>
                                            <th>Kode PLP</th>
                                            <th>Departemen</th>
                                            <th>Mesin</th>
                                            <th>Pelapor</th>
                                            <th>Jenis Pekerjaan</th>
                                            <th>Request</th>

                                            <th>Waktu Request</th>
                                            <th>Waktu Perencanaan</th>
                                            <th>Waktu Start</th>
                                            <th>Waktu Finish</th>
                                            <th>Waktu Proses</th>

                                            <th>Status</th>
                                            <th>Konfirmasi</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>