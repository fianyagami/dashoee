<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-dashplpteknik">
    <div class="x_content">
        <div class="body">

            <div class="x_title">
                <h2>Dashboard PLP Teknik</h2>
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

                                <div class="col-md-3">
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

                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button id="btnBrowseDashplpteknik" class="btn btn-cari">
                                        <i class="fa fa-search"></i>
                                        Browse
                                    </button>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div id="loadingDashboard"
                    style="display:none; margin:0 15px 15px 15px;"
                    class="alert alert-info text-center col-md-11">
                    <i class="fa fa-spinner fa-spin"></i>
                    Loading dashboard...
                </div>

                <!-- CHART 1 -->
                <div class="col-md-6">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Top-10 Jumlah PLP Teknik per Mesin</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div id="chartTopMesin" style="height:380px;"></div>
                        </div>
                    </div>
                </div>

                <!-- CHART 2 -->
                <div class="col-md-6">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Persentase Jenis Pekerjaan</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div id="chartJenisPekerjaan" style="height:380px;"></div>
                        </div>
                    </div>
                </div>

                <!-- CHART 3 -->
                <div class="col-md-6">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Tren Harian Jumlah PLP dalam Bulan</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div id="chartTrenHarian" style="height:380px;"></div>
                        </div>
                    </div>
                </div>

                <!-- CHART 4 -->
                <div class="col-md-6">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Top-10 Mesin Rata-rata Waktu Proses Terlama (Jam)</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div id="chartWaktuProses" style="height:380px;"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>