<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-kompbapobkk">
    <div class="x_content">
        <div class="body">

            <div class="x_title">
                <h2>Komparasi BAPOB dan KK</h2>
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
                            <h2>Data KK (Kartu Kerja)</h2>
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
                                <h2 id="info_kk_bapob">NO BAPOB: -</h2>
                                <h2 id="info_kk_barang">PRODUK: -</h2>
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

                <!-- PANEL BAPOB -->
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Data BAPOB</h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <label>Tahun BAPOB</label>
                                <select id="thn_bapob" class="form-control">
                                    <?php for ($i = 2021; $i <= 2027; $i++) { ?>
                                        <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>No BAPOB</label>
                                <select id="select_bapob" class="form-control" style="width:100%;"></select>
                            </div>

                            <div class="info-selected">
                                <h2 id="info_bapob_produk">PRODUK: -</h2>
                                <h2 id="info_bapob_dibuat">PLATFORM: -</h2>
                            </div>

                            <div class="table-responsive">
                                <table id="tblbapob" class="table table-bordered table-striped table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>URUT SUB</th>
                                            <th>NAMA SUB</th>
                                            <th>URUTAN PROSES</th>
                                            <th>NAMA PROSES</th>
                                            <th>NAMA MESIN</th>
                                            <th>TARGET SPEED</th>
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