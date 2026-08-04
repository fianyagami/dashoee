<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-komplplhp">
    <div class="x_content">
        <div class="body">

            <div class="x_title">
                <h2>Komparasi PLP vs LHP</h2>
                <div class="clearfix"></div>
            </div>

            <div class="row">

                <!-- FILTER PERIODE -->
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

                            </div>

                        </div>
                    </div>
                </div>

                <!-- PANEL KIRI: PLP TEKNIK -->
                <div class="col-md-6">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>PLP Teknik</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <div class="row" style="margin-bottom:15px;">
                                <div class="col-md-12">
                                    <label>Nama Mesin (PLP)</label>
                                    <select id="mesinPlp" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                            <div id="loadingPlp"
                                style="display:none; padding:10px; margin-bottom:10px;"
                                class="alert alert-info text-center">
                                <i class="fa fa-spinner fa-spin"></i>
                                Loading data PLP...
                            </div>

                            <div style="overflow-x:auto; width:100%;">
                                <table id="tblPlp" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal PLP</th>
                                            <th>Kode PLP</th>
                                            <th>Request</th>
                                            <th>Waktu Req</th>
                                            <th>Waktu Perencanaan</th>
                                            <th>Waktu Start</th>
                                            <th>Waktu Finish</th>
                                            <th>Waktu Proses</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="filter-row">
                                            <th>Tanggal PLP</th>
                                            <th>Kode PLP</th>
                                            <th>Request</th>
                                            <th>Waktu Req</th>
                                            <th>Waktu Perencanaan</th>
                                            <th>Waktu Start</th>
                                            <th>Waktu Finish</th>
                                            <th>Waktu Proses</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- PANEL KANAN: LHP PRODUKSI -->
                <div class="col-md-6">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>LHP Produksi (Kategori: Lain-lain / Teknisi)</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <div class="row" style="margin-bottom:15px;">
                                <div class="col-md-12">
                                    <label>Nama Mesin (LHP)</label>
                                    <select id="mesinLhp" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                            <div id="loadingLhp"
                                style="display:none; padding:10px; margin-bottom:10px;"
                                class="alert alert-info text-center">
                                <i class="fa fa-spinner fa-spin"></i>
                                Loading data LHP...
                            </div>

                            <div style="overflow-x:auto; width:100%;">
                                <table id="tblLhp" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal LHP</th>
                                            <th>Nomor KK</th>
                                            <th>Produk</th>
                                            <th>Shift</th>
                                            <th>Kategori</th>
                                            <th>Kegiatan</th>
                                            <th>Jam Mulai</th>
                                            <th>Jam Selesai</th>
                                            <th>Rentang Waktu</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="filter-row">
                                            <th>Tanggal LHP</th>
                                            <th>Nomor KK</th>
                                            <th>Produk</th>
                                            <th>Shift</th>
                                            <th>Kategori</th>
                                            <th>Kegiatan</th>
                                            <th>Jam Mulai</th>
                                            <th>Jam Selesai</th>
                                            <th>Rentang Waktu</th>
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