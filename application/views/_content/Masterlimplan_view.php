<?php defined('_HOME') or exit('No direct script access allowed'); ?>

<div id="page-masterlimplan">
    <div class="x_content">
        <div class="body">

            <div class="x_title">
                <h2>Master Limit Planned</h2>
                <div class="clearfix"></div>
            </div>

            <div class="row">

                <!-- FILTER -->
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <div class="row">

                                <div class="col-md-5">
                                    <label>Mesin</label>
                                    <select id="filterMesin" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button id="btnBrowseLimplan" class="btn btn-cari">
                                        <i class="fa fa-search"></i> Browse
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABEL UTAMA -->
                <div class="col-md-12">
                    <div class="x_panel">

                        <div class="x_title">
                            <h2>Data Limit Planned</h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <!-- Tombol Tambah -->
                            <div style="margin-bottom: 10px;">
                                <button id="btnTambahLimplan" class="btn btn-warning">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>

                            <div id="loadingLimplan"
                                style="display:none; padding:15px; margin-bottom:10px;"
                                class="alert alert-info text-center">
                                <i class="fa fa-spinner fa-spin"></i>
                                Loading data...
                            </div>

                            <h2 id="infoMesinLimplan" class="text-left"
                                style="font-size:16px; margin-bottom:15px;">
                                Mesin : -
                            </h2>

                            <div style="overflow-x:auto; width:100%;">
                                <table id="tblLimplan"
                                    class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr class="main-header">
                                            <th>No</th>
                                            <th>Kegiatan</th>
                                            <th>Limit Plan (Jam)</th>
                                            <th>Limit Plan (Menit)</th>
                                            <th>Parameter</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="filter-row">
                                            <th></th>
                                            <th>Kegiatan</th>
                                            <th>Limit Plan (Jam)</th>
                                            <th>Limit Plan (Menit)</th>
                                            <th>Parameter</th>
                                            <th></th>
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

<!-- MODAL FORM INPUT / EDIT -->
<div class="modal fade" id="modalFormLimplan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 600px; max-width: 98%;">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h4 class="modal-title" id="modalLimplanTitle">
                    <i class="fa fa-plus-circle"></i> Tambah Master Limit Planned
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="fldIdLimitplan" value="">

                <!-- Row 1: Mesin -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Mesin <span class="text-danger">*</span></label>
                            <select id="fldMesin" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Kegiatan -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Kegiatan <span class="text-danger">*</span></label>
                            <select id="fldKegiatan" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Limit Plan + Parameter -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Limit Plan (Jam) <span class="text-danger">*</span></label>
                            <input type="number" id="fldLimitplan" class="form-control"
                                step="0.01" min="0" placeholder="Contoh: 0.50">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Parameter <span class="text-danger">*</span></label>
                            <select id="fldParameter" class="form-control">
                                <option value=""></option>
                                <option value="DAY">DAY</option>
                                <option value="SHIFT">SHIFT</option>
                                <option value="PRODUK">PRODUK</option>
                                <option value="BAHAN">BAHAN</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Catatan -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info" style="font-size:12px; padding:10px 14px; margin-bottom:0;">
                            <b><i class="fa fa-info-circle"></i> Nilai Limit Plan dalam Jam</b><br>
                            &bull; 1 &nbsp;&nbsp; = 60 menit<br>
                            &bull; 0,75 = 45 menit<br>
                            &bull; 0,5 &nbsp;= 30 menit<br>
                            &bull; 0,25 = 15 menit<br>
                            &bull; 0,17 = 10 menit<br>
                            &bull; dll
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" id="btnSaveLimplan" class="btn btn-success">
                    <i class="fa fa-save"></i> <span id="lblBtnSave">Save</span>
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
            </div>

        </div>
    </div>
</div>
<!-- END MODAL -->