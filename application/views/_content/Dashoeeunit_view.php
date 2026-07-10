<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-dashoeeunit">
    <div class="x_content">
        <div class="body">

            <div class="x_title">
                <h2>Dashboard OEE per Unit</h2>
                <div class="clearfix"></div>
            </div>

            <div class="row">

                <!-- FILTER -->
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">

                            <div class="row filter-box">
                                <div class="col-md-3 col-sm-6">
                                    <label>Tahun</label>
                                    <select id="tahun" name="tahun" class="form-control">
                                        <?php
                                        $tahun_sekarang = (int) $tahun;
                                        for ($i = 2022; $i <= 2027; $i++) {
                                            $selected = ($i == $tahun_sekarang) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $i ?>" <?= $selected ?>>
                                                <?= $i ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <label>Minggu Ke-</label>
                                    <select id="minggu" name="minggu" class="form-control"></select>
                                </div>

                                <div class="col-md-2 col-sm-12">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btnBrowse" class="btn btn-cari">
                                        <i class="fa fa-search"></i> Browse
                                    </button>
                                </div>
                            </div>

                            <div id="loadingDashboardUnit"
                                style="display:none; padding:15px; margin-bottom:10px;"
                                class="alert alert-info text-center">

                                <i class="fa fa-spinner fa-spin"></i>
                                &nbsp; &nbsp; &nbsp; &nbsp;
                                Loading Dashboard OEE Unit...
                                &nbsp; &nbsp; &nbsp; &nbsp;
                                <i class="fa fa-coffee"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">

                            <h3 id="dashboardTitle" class="dashboard-title">
                                <div class="dashboard-title-main">Dashboard OEE Pura TSS-01</div>
                                <div class="dashboard-title-sub">-</div>
                            </h3>

                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="oee-panel">
                                        <div class="oee-panel-title" style="color:blue;">Availability Rate</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="chartAR" class="chart-box"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="chartDowntime" class="chart-box"></div>
                                            </div>
                                        </div>
                                        <div class="oee-panel-detail-link">
                                            <a href="javascript:void(0);" class="btn-detail-modal" data-type="AR" data-target="#modalDetailAR">
                                                <i class="fa fa-table"></i> Detail Data
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="oee-panel">
                                        <div class="oee-panel-title" style="color:green;">Quality Rate</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="chartQR" class="chart-box"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="chartDefect" class="chart-box"></div>
                                            </div>
                                        </div>
                                        <div class="oee-panel-detail-link">
                                            <a href="javascript:void(0);" class="btn-detail-modal" data-type="QR" data-target="#modalDetailQR">
                                                <i class="fa fa-table"></i> Detail Data
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="oee-panel">
                                        <div class="oee-panel-title" style="color:orange;">Performance Rate</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="chartPR" class="chart-box"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="chartActualTarget" class="chart-box"></div>
                                            </div>
                                        </div>
                                        <div class="oee-panel-detail-link">
                                            <a href="javascript:void(0);" class="btn-detail-modal" data-type="PR" data-target="#modalDetailPR">
                                                <i class="fa fa-table"></i> Detail Data
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="oee-panel">
                                        <div class="oee-panel-title" style="color:#ff00ff;">
                                            OEE (Overall Equipment Effectiveness)
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="oee-score" id="oeeScore">0%</div>
                                                <div class="oee-status" id="oeeStatus">-</div>
                                                <div class="oee-formula" id="oeeFormula">AR x PR x QR</div>
                                                <div class="oee-progress-wrap">

                                                    <div class="oee-progress-item">
                                                        <div class="oee-progress-label">
                                                            <span>Availability</span>
                                                            <span id="progressARText">0%</span>
                                                        </div>
                                                        <div class="progress">
                                                            <div id="progressAR" class="progress-bar progress-bar-info" style="width:0%"></div>
                                                        </div>
                                                    </div>

                                                    <div class="oee-progress-item">
                                                        <div class="oee-progress-label">
                                                            <span>Quality</span>
                                                            <span id="progressQRText">0%</span>
                                                        </div>
                                                        <div class="progress">
                                                            <div id="progressQR" class="progress-bar progress-bar-success" style="width:0%"></div>
                                                        </div>
                                                    </div>

                                                    <div class="oee-progress-item">
                                                        <div class="oee-progress-label">
                                                            <span>Performance</span>
                                                            <span id="progressPRText">0%</span>
                                                        </div>
                                                        <div class="progress">
                                                            <div id="progressPR" class="progress-bar progress-bar-warning" style="width:0%"></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>100%</th>
                                                        <td>Perfect</td>
                                                    </tr>
                                                    <tr>
                                                        <th>85% - 99%</th>
                                                        <td>World Class</td>
                                                    </tr>
                                                    <tr>
                                                        <th>60% - 84%</th>
                                                        <td>Standard</td>
                                                    </tr>
                                                    <tr>
                                                        <th>&lt; 60%</th>
                                                        <td>Low</td>
                                                    </tr>
                                                </table>
                                                <div class="target-unit-box">
                                                    <div class="target-unit-label">TARGET OEE</div>
                                                    <div class="target-unit-value">70%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL DETAIL AR -->
    <div class="modal fade modal-dashoeekk" id="modalDetailAR" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="width: 95%;">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Detail Data - Availability Rate</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tblDetailAR" class="table table-bordered table-striped table-dashoeekk" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No LHP</th>
                                    <th>Tanggal</th>
                                    <th>Urut</th>
                                    <th>Nama Mesin</th>
                                    <th>Nomor KK</th>
                                    <th>Proses</th>
                                    <th>Produk</th>
                                    <th>Shift</th>
                                    <th>Kegiatan</th>
                                    <th>Kategori Losstime</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Waktu Asli (Jam)</th>
                                    <th>Limit Plan (Jam)</th>
                                    <th>Waktu Fix (Jam)</th>
                                    <th>Par Limit Plan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="13" style="text-align:right">Total Jam Kerja :</th>
                                    <th></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL QR -->
    <div class="modal fade modal-dashoeekk" id="modalDetailQR" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="width: 95%;">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title">Detail Data - Quality Rate</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tblDetailQR" class="table table-bordered table-striped table-dashoeekk" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No LHP</th>
                                    <th>Tanggal</th>
                                    <th>Urut</th>
                                    <th>Nama Mesin</th>
                                    <th>Nomor KK</th>
                                    <th>Proses</th>
                                    <th>Produk</th>
                                    <th>Shift</th>
                                    <th>Kegiatan</th>
                                    <th>Baik</th>
                                    <th>Satuan Baik</th>
                                    <th>Nama Waste</th>
                                    <th>Rusak</th>
                                    <th>Satuan Rusak</th>
                                    <th>Output</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="9" style="text-align:right">Total Hasil :</th>
                                    <th></th>
                                    <th colspan="2"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL PR -->
    <div class="modal fade modal-dashoeekk" id="modalDetailPR" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="width: 95%;">
            <div class="modal-content">

                <div class="modal-header bg-warning text-white">
                    <h4 class="modal-title">Detail Data - Performance Rate</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="tblDetailPR" class="table table-bordered table-striped table-dashoeekk" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Tanggal LHP</th>
                                    <th>Shift</th>
                                    <th>Nama Mesin</th>
                                    <th>Nomor KK</th>
                                    <th>Produk</th>
                                    <th>Proses</th>
                                    <th>Total Output (Baik+Rusak)</th>
                                    <th>Waktu Produksi Murni (Jam)</th>
                                    <th>Rata-rata Target KK</th>
                                    <th>Performance Rate (%)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right">Total :</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align:right; font-size:11px; color:#888;">AVG :</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</div>