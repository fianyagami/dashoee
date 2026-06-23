<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-daftarkk">
    <div class="x_content">
        <div class="body">

            <div class="x_title">
                <h2>Daftar Kartu Kerja</h2>
                <div class="clearfix"></div>
            </div>

            <div class="row">
                <!-- FILTER -->
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">

                            <div class="row">

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>Tahun</label>
                                    <select id="filter_thn" class="form-control">
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

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>&nbsp;</label>

                                    <button id="btnBrowseDaftarkk"
                                        class="btn btn-cari">
                                        <i class="fa fa-search"></i>
                                        Browse

                                    </button>
                                </div>

                            </div>
                            <div id="panel_loading" class="alert alert-info" align="center" style="display:none;">
                                <i class="fa fa-spinner fa-spin"></i>
                                Mohon bersabar, data sedang diproses...
                                <i class="fa fa-coffee"></i>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- HEADER KK -->
                <div class="col-md-12">
                    <div class="x_panel">

                        <div class="x_title">
                            <h2>Daftar KK</h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">
                            <div style="overflow-x:auto; width:100%;"></div>
                            <table id="tblKKhead" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>NO KK</th>
                                        <th>NO BAPOB</th>
                                        <th>PO CUST</th>
                                        <th>STATUS KK</th>
                                        <th>TGL KK</th>
                                        <th>CUSTOMER</th>
                                        <th>PRODUK</th>
                                        <th>KATEGORI</th>
                                        <th>OPLAGH PO</th>
                                        <th>SATUAN</th>
                                        <th>ARSIP KK</th>
                                        <th>PEMBUAT</th>
                                        <th>MENGETAHUI</th>
                                    </tr>
                                    <tr class="filter-row">
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                        <th><input type="text" class="form-control input-sm" placeholder="Cari"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- DETAIL KK -->
                <div class="col-md-12">
                    <div class="x_panel">

                        <div class="x_title">
                            <h2>Detail KK</h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">
                            <div style="overflow-x:auto; width:100%;"></div>
                            <h2 id="infoKK" class="text-left" style="font-size:16px; margin-bottom:15px;">
                                KK : - / -
                            </h2>
                            <table id="tblKKdetail" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr class="group-header">
                                        <th colspan="6">Proses</th>
                                        <th colspan="3">Bahan</th>
                                        <th colspan="1">Hasil</th>
                                    </tr>
                                    <tr>
                                        <!-- <th>NO</th> -->
                                        <th>URUTAN FLOW</th>
                                        <th>PROSES</th>
                                        <th>MESIN</th>
                                        <th>WASTE (%)</th>
                                        <th>TARGET</th>
                                        <th>SATUAN</th>
                                        <th>JENIS</th>
                                        <th>BAHAN</th>
                                        <th>UKURAN</th>
                                        <th>HASIL</th>
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