<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    $(function() {

        var page = $('#page-masterlimplan');
        if (page.length === 0) return;


        // ============================================================
        // FIX SELECT2 + BOOTSTRAP 3 MODAL
        // ============================================================
        $(document).on('focusin.modal', function(e) {
            if (
                $(e.target).closest('.select2-dropdown, .select2-container, .select2-search').length
            ) {
                e.stopImmediatePropagation();
            }
        });
        // ============================================================


        var tblLimplan = null;
        var selectedKdmesin = '';
        var modeForm = 'insert'; // 'insert' | 'edit'

        // ============================================================
        // FORMAT HELPERS
        // ============================================================
        function formatNumber(data) {
            if (data === null || data === undefined || data === '') return '0';
            var num = parseFloat(data);
            if (isNaN(num)) return data;
            return num.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // ============================================================
        // SELECT2 FILTER UTAMA (MESIN dari VOEE_LIMITPLAN)
        // ============================================================

        function initSelect2Filter() {
            $('#filterMesin').select2({
                width: '100%',
                placeholder: 'Pilih Mesin',
                allowClear: true,
                ajax: {
                    url: base_url + 'Masterlimplan/get_mesin',
                    type: 'GET',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return data;
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (!data.id) return data.text;
                    return $(
                        '<div class="select2-mesin-row">' +
                        '<div class="select2-mesin-kode">' + data.kdmesin + '</div>' +
                        '<div class="select2-mesin-nama">' + data.mesin + '</div>' +
                        '</div>'
                    );
                },
                templateSelection: function(data) {
                    if (!data.id) return data.text;
                    return data.mesin || data.text;
                }
            });

            $('#filterMesin').on('change', function() {
                selectedKdmesin = $(this).val() || '';
            });
        }

        // ============================================================
        // SELECT2 MODAL — MESIN AKTIF
        // ============================================================
        function initSelect2MesinForm() {
            $('#fldMesin').select2({
                dropdownParent: $('#modalFormLimplan'),
                width: '100%',
                placeholder: 'Pilih Mesin',
                allowClear: true,
                ajax: {
                    url: base_url + 'Masterlimplan/get_mesin_aktif',
                    type: 'GET',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return data;
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (!data.id) return data.text;
                    return $(
                        '<div class="select2-mesin-row">' +
                        '<div class="select2-mesin-kode">' + data.kdmesin + '</div>' +
                        '<div class="select2-mesin-nama">' + data.mesin + '</div>' +
                        '</div>'
                    );
                },
                templateSelection: function(data) {
                    if (!data.id) return data.text;
                    return data.mesin || data.text;
                }
            });
        }

        // ============================================================
        // SELECT2 MODAL — KEGIATAN
        // ============================================================
        function initSelect2KegiatanForm() {
            $('#fldKegiatan').select2({
                dropdownParent: $('#modalFormLimplan'),
                width: '100%',
                placeholder: 'Pilih Kegiatan',
                allowClear: true,
                ajax: {
                    url: base_url + 'Masterlimplan/get_kegiatan',
                    type: 'GET',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return data;
                    },
                    cache: true
                }
            });
        }

        // SELECT2 MODAL — PARAMETER (static)
        function initSelect2Parameter() {
            $('#fldParameter').select2({
                dropdownParent: $('#modalFormLimplan'),
                width: '100%',
                placeholder: 'Pilih Parameter',
                allowClear: true
            });
        }

        // ============================================================
        // DATATABLE UTAMA
        // ============================================================
        function initDataTable() {
            // Pindahkan tfoot ke thead untuk column search
            page.find('#tblLimplan tfoot th').each(function() {
                var title = $(this).text();
                if (title) {
                    $(this).html(
                        '<input type="text" class="form-control input-sm" placeholder="Search ' + title + '" />'
                    );
                }
            });
            page.find('#tblLimplan tfoot tr').appendTo('#tblLimplan thead');

            tblLimplan = page.find('#tblLimplan').DataTable({
                processing: true,
                searching: true,
                paging: true,
                ordering: true,
                autoWidth: false,
                responsive: false,
                scrollX: true,
                scrollCollapse: true,
                pageLength: 25,

                dom: "<'row'<'col-sm-6'l><'col-sm-6 text-right'B>>" +
                    "rt" +
                    "<'row'<'col-sm-6'i><'col-sm-6'p>>",

                buttons: [{
                        text: '<i class="fa fa-file-excel-o"></i> &nbsp; Excel',
                        extend: 'excelHtml5',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        text: '<i class="fa fa-file-pdf-o"></i> &nbsp; PDF',
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        text: '<i class="fa fa-refresh"></i> Refresh',
                        className: 'btn btn-primary btn-sm',
                        action: function() {
                            browseLimplan();
                        }
                    },
                    {
                        text: '<i class="fa fa-print"></i> &nbsp; Print',
                        extend: 'print',
                        className: 'btn btn-dark btn-sm'
                    }
                ],

                data: [],

                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'KEGIATAN'
                    },
                    {
                        data: 'LIMITPLAN',
                        className: 'text-right angka-limitplan',
                        render: formatNumber
                    },
                    {
                        data: 'LIMITPLAN_MENIT',
                        className: 'text-right angka-limitplan',
                        render: formatNumber
                    },
                    {
                        data: 'PAR_LIMITPLAN',
                        className: 'text-center'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<button type="button" class="btn btn-xs btn-warning btn-edit-limplan" title="Edit">' +
                                '<i class="fa fa-pencil"></i> Edit' +
                                '</button>';
                        }
                    }
                ],

                columnDefs: [{
                        targets: 0,
                        width: '40px'
                    },
                    {
                        targets: 5,
                        width: '80px'
                    }
                ]
            });

            // Column search
            tblLimplan.columns().every(function() {
                var that = this;
                $('input', this.header()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }

        // ============================================================
        // LOAD DATA
        // ============================================================
        function loadData(kdmesin) {
            $.ajax({
                url: base_url + 'Masterlimplan/get_data',
                type: 'POST',
                dataType: 'json',
                data: {
                    kdmesin: kdmesin
                },
                beforeSend: function() {
                    page.find('#loadingLimplan').show();
                    page.find('#btnBrowseLimplan')
                        .prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                    tblLimplan.clear().draw();
                },
                success: function(response) {
                    tblLimplan.clear();
                    tblLimplan.rows.add(response);
                    tblLimplan.draw();
                },
                error: function(xhr) {
                    alert('Gagal mengambil data.');
                    console.log(xhr.responseText);
                },
                complete: function() {
                    page.find('#loadingLimplan').hide();
                    page.find('#btnBrowseLimplan')
                        .prop('disabled', false)
                        .html('<i class="fa fa-search"></i> Browse');
                }
            });
        }

        function browseLimplan() {
            var kdmesin = page.find('#filterMesin').val();
            if (!kdmesin) {
                alert('Pilih mesin terlebih dahulu.');
                return;
            }
            selectedKdmesin = kdmesin;
            var mesinText = page.find('#filterMesin option:selected').text();

            page.find('#infoMesinLimplan').html('Mesin : <b>' + mesinText + '</b>');
            loadData(kdmesin);
        }

        // ============================================================
        // MODAL — RESET FORM
        // ============================================================
        function resetForm() {
            $('#fldIdLimitplan').val('');
            $('#fldMesin').val(null).trigger('change');
            $('#fldKegiatan').val(null).trigger('change');
            $('#fldLimitplan').val('');
            $('#fldParameter').val(null).trigger('change');
        }

        // Helper: set Select2 dengan data option baru
        function setSelect2Value(selector, id, text) {
            var $el = $(selector);
            if ($el.find("option[value='" + id + "']").length === 0) {
                $el.append(new Option(text, id, true, true));
            }
            $el.val(id).trigger('change');
        }

        // ============================================================
        // TOMBOL TAMBAH
        // ============================================================
        $('#btnTambahLimplan').on('click', function() {
            modeForm = 'insert';
            resetForm();
            $('#modalLimplanTitle').html('<i class="fa fa-plus-circle"></i> Tambah Master Limit Planned');
            $('#lblBtnSave').text('Save');
            $('#modalFormLimplan').modal('show');
        });

        // ============================================================
        // TOMBOL EDIT (dari DataTable)
        // ============================================================
        page.find('#tblLimplan').on('click', '.btn-edit-limplan', function() {
            var row = tblLimplan.row($(this).closest('tr')).data();
            if (!row) return;

            modeForm = 'edit';
            resetForm();

            $('#modalLimplanTitle').html('<i class="fa fa-pencil"></i> Edit Master Limit Planned');
            $('#lblBtnSave').text('Update');

            $('#fldIdLimitplan').val(row.ID_LIMITPLAN);
            setSelect2Value('#fldMesin', row.KDMESIN, row.KDMESIN);
            setSelect2Value('#fldKegiatan', row.K_HIS, row.KEGIATAN);
            $('#fldLimitplan').val(parseFloat(row.LIMITPLAN).toFixed(2));
            setSelect2Value('#fldParameter', row.PAR_LIMITPLAN, row.PAR_LIMITPLAN);

            $('#modalFormLimplan').modal('show');
        });

        // ============================================================
        // SIMPAN / UPDATE
        // ============================================================
        $('#btnSaveLimplan').on('click', function() {
            var id_limitplan = $('#fldIdLimitplan').val();
            var kdmesin = $('#fldMesin').val();
            var k_his = $('#fldKegiatan').val();
            var limitplan = $('#fldLimitplan').val();
            var par_limitplan = $('#fldParameter').val();

            // Validasi
            if (!kdmesin) {
                alert('Pilih Mesin terlebih dahulu.');
                return;
            }
            if (!k_his) {
                alert('Pilih Kegiatan terlebih dahulu.');
                return;
            }
            if (limitplan === '' || isNaN(parseFloat(limitplan))) {
                alert('Isi nilai Limit Plan dengan angka.');
                return;
            }
            if (!par_limitplan) {
                alert('Pilih Parameter terlebih dahulu.');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: base_url + 'Masterlimplan/save',
                type: 'POST',
                dataType: 'json',
                data: {
                    id_limitplan: id_limitplan,
                    kdmesin: kdmesin,
                    k_his: k_his,
                    limitplan: limitplan,
                    par_limitplan: par_limitplan
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#modalFormLimplan').modal('hide');

                        // Reload tabel sesuai filter mesin yang aktif
                        if (selectedKdmesin) {
                            loadData(selectedKdmesin);
                        }

                        // Jika mode insert dan mesin form sama dengan filter, info tetap
                        // (sudah tertangani oleh loadData di atas)
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi error, coba lagi.');
                    console.log(xhr.responseText);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fa fa-save"></i> <span id="lblBtnSave">' + (modeForm === 'insert' ? 'Save' : 'Update') + '</span>');
                }
            });
        });

        // ============================================================
        // BUTTON BROWSE
        // ============================================================
        page.find('#btnBrowseLimplan').on('click', function() {
            browseLimplan();
        });

        // ============================================================
        // INIT
        // ============================================================
        initSelect2Filter();
        initSelect2MesinForm();
        initSelect2KegiatanForm();
        initSelect2Parameter();
        initDataTable();

    });
</script>