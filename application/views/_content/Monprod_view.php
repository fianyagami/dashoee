<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div class="x_content">
    <div class="body">
        <!-- <div role="main"> -->

        <div class="x_title">
            <h2>Monitoring Produksi Mesin</h2>
            <div class="clearfix"></div>
        </div>

        <div class="row">

            <!-- FILTER -->
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">

                        <div class="row">

                            <div class="col-md-2">
                                <label>Tahun</label>
                                <select id="thn" class="form-control">
                                    <?php
                                    $tahun_sekarang = date('Y'); // Mendapatkan tahun saat ini (contoh: 2026)
                                    for ($i = 2022; $i <= 2027; $i++) {
                                        // Periksa apakah tahun di loop sama dengan tahun sekarang
                                        $selected = ($i == $tahun_sekarang) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $i ?>" <?= $selected ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Bulan</label>
                                <?php $bulan = date('n'); // 'n' = bulan tanpa leading zero (1-12) 
                                ?>
                                <select id="bln"
                                    class="form-control select2_single">
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

                            <div class="col-md-4">

                                <label>Mesin</label>

                                <select id="mesin"
                                    class="form-control select2_single">

                                    <option value="">- Pilih Mesin -</option>

                                </select>

                            </div>

                            <div class="col-md-3">
                                <label>&nbsp;</label>

                                <button id="btnBrowseMonprod"
                                    class="btn btn-primary form-control">

                                    <i class="fa fa-search"></i>
                                    Browse

                                </button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- TABEL MESIN -->
            <!-- <div class="col-md-3">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Departemen & Mesin</h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">

                        <table id="tblMesin"
                            class="table table-bordered table-striped table-hover">

                            <thead>
                                <tr>
                                    <th>Departemen</th>
                                    <th>Mesin</th>
                                </tr>
                            </thead>

                        </table>

                    </div>
                </div>
            </div> -->

            <!-- DETAIL -->
            <div class="col-md-12">
                <div class="x_panel">

                    <div class="x_title">
                        <h2>Detail Produksi</h2>
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
                                Loading detail produksi...

                            </div>
                            <table id="tblDetail"
                                class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Shift</th>
                                        <th>No KK</th>
                                        <th>Produk</th>
                                        <th>Proses</th>
                                        <th>Target</th>
                                        <th>Sat</th>

                                        <th>Waktu Prod</th>
                                        <th>Waktu Non Prod</th>
                                        <th>Total</th>

                                        <th>Baik</th>
                                        <th>Rusak</th>
                                        <th>Output</th>
                                        <th>Sat</th>

                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Shift</th>
                                        <th>No KK</th>
                                        <th>Produk</th>
                                        <th>Proses</th>
                                        <th>Target</th>
                                        <th>Sat</th>

                                        <th>Waktu Prod</th>
                                        <th>Waktu Non Prod</th>
                                        <th>Total</th>

                                        <th>Baik</th>
                                        <th>Rusak</th>
                                        <th>Output</th>
                                        <th>Sat</th>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- </div> -->


    </div>
</div>