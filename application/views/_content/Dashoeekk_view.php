<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-dashoeekk">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Dashboard OEE per KK</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>

                <div class="x_panel">
                    <div class="x_content">

                        <div class="row filter-box">
                            <div class="col-md-2 col-sm-6">
                                <label>Tahun</label>
                                <select id="tahun" name="tahun" class="form-control">
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

                            <div class="col-md-2 col-sm-6">
                                <label>Bulan</label>
                                <?php $bulan = date('n'); // 'n' = bulan tanpa leading zero (1-12) 
                                ?>
                                <select id="bulan" name="bulan"
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

                            <div class="col-md-2 col-sm-6">
                                <label>Mesin</label>
                                <select id="mesin" class="form-control"></select>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <label>Tahun KK</label>
                                <select id="tahun_kk" class="form-control">
                                    <?php for ($i = date('Y'); $i >= 2022; $i--) { ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <label>Nomor KK</label>
                                <select id="nomor_kk" class="form-control"></select>
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <label>&nbsp;</label>
                                <button type="button" id="btnBrowse" class="btn btn-success btn-block">
                                    <i class="fa fa-search"></i> Browse
                                </button>
                            </div>
                        </div>

                        <!-- <div class="row filter-box">
                            
                        </div> -->

                        <div id="loadingDashboardKK"
                            style="display:none; padding:15px; margin-bottom:10px;"
                            class="alert alert-info text-center">

                            <i class="fa fa-spinner fa-spin"></i>
                            Loading Dashboard OEE KK...

                        </div>

                        <h3 id="dashboardTitle" class="dashboard-title">
                            <div class="dashboard-title-main">Dashboard OEE</div>
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