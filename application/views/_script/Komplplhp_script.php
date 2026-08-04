<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    $(function() {

        var page = $('#page-komplplhp');
        if (page.length === 0) return;

        var tblPlp = null;
        var tblLhp = null;

        // ============================
        // FORMAT HELPER
        // ============================
        function formatDateTimeDisplay(value) {
            if (!value) return '-';

            let dateStr = String(value).replace('T', ' ').substring(0, 19);
            let parts = dateStr.split(' ');
            if (parts.length < 2) return value;

            let d = parts[0].split('-');
            if (d.length !== 3) return value;

            return d[2] + '/' + d[1] + '/' + d[0] + ' ' + parts[1];
        }

        // ============================
        // SELECT2 - NAMA MESIN (PLP & LHP), independen, tanpa dependensi Departemen
        // ============================
        function initSelect2() {
            page.find('#mesinPlp').select2({
                width: '100%',
                placeholder: 'Pilih Nama Mesin (PLP)',
                allowClear: true,
                ajax: {
                    url: base_url + 'Komplplhp/get_mesin_plp',
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
                templateResult: formatMesinResult,
                templateSelection: formatMesinSelection
            });

            page.find('#mesinLhp').select2({
                width: '100%',
                placeholder: 'Pilih Nama Mesin (LHP)',
                allowClear: true,
                ajax: {
                    url: base_url + 'Komplplhp/get_mesin_lhp',
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
                templateResult: formatMesinResult,
                templateSelection: formatMesinSelection
            });
        }

        function formatMesinResult(data) {
            if (!data.id) return data.text;

            return $(
                '<div class="select2-mesin-row">' +
                '<div class="select2-mesin-kode">' + data.kode + '</div>' +
                '<div class="select2-mesin-nama">' + data.nama + '</div>' +
                '</div>'
            );
        }

        function formatMesinSelection(data) {
            if (!data.id) return data.text;
            return data.nama || data.text;
        }

        // ============================
        // DATATABLE INIT
        // ============================
        function initTablePlp() {
            page.find('#tblPlp tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control input-sm" placeholder="Search ' + title + '" />');
            });

            page.find('#tblPlp tfoot tr').appendTo('#tblPlp thead');

            tblPlp = page.find('#tblPlp').DataTable({
                processing: true,
                autoWidth: false,
                scrollX: true,
                pageLength: 10,
                data: [],
                columns: [{
                        data: 'TANGGAL_PLP',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'KODE_PLP'
                    },
                    {
                        data: 'REQUEST',
                        className: 'request-wrap'
                    },
                    {
                        data: 'WAKTU_REQ',
                        className: 'text-center',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'WAKTU_PERENCANAAN',
                        className: 'text-center',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'WAKTU_START',
                        className: 'text-center',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'WAKTU_FINISH',
                        className: 'text-center',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'WAKTU_PROSES',
                        className: 'text-center',
                        render: function(d) {
                            return d ? d : '-';
                        }
                    }
                ],
                language: {
                    emptyTable: "Pilih Nama Mesin (PLP) untuk menampilkan data",
                    zeroRecords: "Data tidak ditemukan"
                }
            });

            tblPlp.columns().every(function() {
                var that = this;
                $('input', this.header()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });

            // Row focus saat diklik
            page.find('#tblPlp tbody').on('click', 'tr', function() {
                page.find('#tblPlp tbody tr').removeClass('row-selected');
                $(this).addClass('row-selected');
            });
        }

        function initTableLhp() {
            page.find('#tblLhp tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control input-sm" placeholder="Search ' + title + '" />');
            });

            page.find('#tblLhp tfoot tr').appendTo('#tblLhp thead');

            tblLhp = page.find('#tblLhp').DataTable({
                processing: true,
                autoWidth: false,
                scrollX: true,
                ordering: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                data: [],
                columns: [{
                        data: 'TANGGAL',
                        className: 'text-center',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'NOMOR_KK'
                    },
                    {
                        data: 'PRODUK',
                        className: 'produk-wrap'
                    },
                    {
                        data: 'SHIFT_',
                        className: 'text-center'
                    },
                    {
                        data: 'KATEGORI'
                    },
                    {
                        data: 'KEGIATAN'
                    },
                    {
                        data: 'JAM1',
                        className: 'text-center',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'JAM2',
                        className: 'text-center',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'WAKTU_BLT',
                        className: 'text-right',
                        render: function(d) {
                            return d !== null ? parseFloat(d).toFixed(2) : '-';
                        }
                    }
                ],
                language: {
                    emptyTable: "Pilih Nama Mesin (LHP) untuk menampilkan data",
                    zeroRecords: "Data tidak ditemukan"
                }
            });

            tblLhp.columns().every(function() {
                var that = this;
                $('input', this.header()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });

            // Row focus saat diklik
            page.find('#tblLhp tbody').on('click', 'tr', function() {
                page.find('#tblLhp tbody tr').removeClass('row-selected');
                $(this).addClass('row-selected');
            });
        }

        // ============================
        // LOAD DATA PER PANEL
        // ============================
        function loadDataPlp() {
            var thn = page.find('#thn').val();
            var bln = page.find('#bln').val();
            var mesin = page.find('#mesinPlp').val();

            if (!mesin) {
                tblPlp.clear().draw();
                return;
            }

            if (!thn || !bln) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Tahun dan Bulan wajib dipilih.'
                });
                return;
            }

            $.ajax({
                url: base_url + 'Komplplhp/get_data_plp',
                type: 'POST',
                dataType: 'json',
                data: {
                    thn: thn,
                    bln: bln,
                    mesin: mesin
                },
                beforeSend: function() {
                    page.find('#loadingPlp').show();
                },
                success: function(res) {
                    tblPlp.clear();
                    tblPlp.rows.add(res);
                    tblPlp.draw();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data PLP',
                        text: 'Terjadi kesalahan saat mengambil data PLP.'
                    });
                    console.log(xhr.responseText);
                },
                complete: function() {
                    page.find('#loadingPlp').hide();
                }
            });
        }

        function loadDataLhp() {
            var thn = page.find('#thn').val();
            var bln = page.find('#bln').val();
            var mesin = page.find('#mesinLhp').val();

            if (!mesin) {
                tblLhp.clear().draw();
                return;
            }

            if (!thn || !bln) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Tahun dan Bulan wajib dipilih.'
                });
                return;
            }

            $.ajax({
                url: base_url + 'Komplplhp/get_data_lhp',
                type: 'POST',
                dataType: 'json',
                data: {
                    thn: thn,
                    bln: bln,
                    mesin: mesin
                },
                beforeSend: function() {
                    page.find('#loadingLhp').show();
                },
                success: function(res) {
                    tblLhp.clear();
                    tblLhp.rows.add(res);
                    tblLhp.draw();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data LHP',
                        text: 'Terjadi kesalahan saat mengambil data LHP.'
                    });
                    console.log(xhr.responseText);
                },
                complete: function() {
                    page.find('#loadingLhp').hide();
                }
            });
        }

        // ============================
        // EVENT BINDING
        // ============================
        page.find('#mesinPlp').on('change', function() {
            loadDataPlp();
        });

        page.find('#mesinLhp').on('change', function() {
            loadDataLhp();
        });

        // Kalau Tahun/Bulan diganti, reload panel yang mesin-nya sudah terpilih
        page.find('#thn, #bln').on('change', function() {
            if (page.find('#mesinPlp').val()) {
                loadDataPlp();
            }
            if (page.find('#mesinLhp').val()) {
                loadDataLhp();
            }
        });

        initSelect2();
        initTablePlp();
        initTableLhp();

    });
</script>