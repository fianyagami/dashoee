<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-kompkklhp">
    <div class="x_content">
        <div class="body">

            <div class="x_title">
                <h2>Komparasi KK dan LHP</h2>
                <div class="clearfix"></div>
            </div>

            <div id="loadingDetail"
                style="display:none; padding:15px; margin-bottom:10px;"
                class="alert alert-info text-center">

                <i class="fa fa-spinner fa-spin"></i>
                Loading Data....

            </div>

            <div class="row">

                <!-- PANEL KK -->
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2 style="color: blue;">Data KK (Kartu Kerja)</h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <label>Tahun KK</label>
                                <select id="thn_kk" class="form-control">
                                    <?php for ($i = 2021; $i <= 2027; $i++) { ?>
                                        <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Nomor KK</label>
                                <select id="select_kk" class="form-control" style="width:100%;"></select>
                            </div>

                            <div class="info-selected">
                                <h2 id="info_kk_barang">PRODUK: -</h2>
                                <h2 id="info_kk_customer">CUSTOMER: -</h2>
                            </div>

                            <div class="table-responsive">
                                <table id="tblkk" class="table table-bordered table-striped table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>URUT</th>
                                            <th>URUT FLOW</th>
                                            <th>NAMA PROSES</th>
                                            <th>NAMA MESIN</th>
                                            <th>WASTE PROSES</th>
                                            <th>TARGET</th>
                                            <th>SAT TARGET</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- PANEL LHP -->
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2 style="color: green;">Data LHP (Laporan Harian Produksi)</h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <div class="info-selected">
                                <h2 id="info_lhp_nomor">NOMOR KK: -</h2>
                                <h2 id="info_lhp_barang">PRODUK: -</h2>
                                <h2 id="info_lhp_status">STATUS: -</h2>
                            </div>

                            <div class="table-responsive">
                                <table id="tbllhp" class="table table-bordered table-striped table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>URUT PROSES</th>
                                            <th>PROSES</th>
                                            <th>TOT WAKTU</th>
                                            <th>TOT BAIK</th>
                                            <th>TOT RUSAK</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>