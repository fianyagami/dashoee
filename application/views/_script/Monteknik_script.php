<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    $(function() {

        var page = $('#page-monteknik');
        if (page.length === 0) return;

        var selectedDept = '';
        var selectedMesin = '';

        var tblDetail = null;

        // ============================
        // FORMAT HELPERS
        // ============================
        function formatDateTimeDisplay(value) {
            if (!value) return '-';

            let dateStr = value;

            if (typeof value === 'object' && value.date) {
                dateStr = value.date;
            }

            dateStr = String(dateStr).replace('T', ' ').substring(0, 19);

            let parts = dateStr.split(' ');
            if (parts.length < 2) return value;

            let datePart = parts[0];
            let timePart = parts[1];

            let d = datePart.split('-');
            if (d.length !== 3) return value;

            return d[2] + '/' + d[1] + '/' + d[0] + ' ' + timePart;
        }

        function renderStatus(status) {
            if (!status) return '-';

            var cls = 'badge-status-default';
            var up = status.toUpperCase();

            if (up.indexOf('BELUM') !== -1) {
                cls = 'badge-status-belum';
            } else if (up.indexOf('SELESAI') !== -1 || up.indexOf('TERSELESAIKAN') !== -1) {
                cls = 'badge-status-selesai';
            } else if (up.indexOf('DIPERBAIKI') !== -1) {
                cls = 'badge-status-proses';
            }

            return '<span class="badge-status ' + cls + '">' + status + '</span>';
        }

        function renderKonfirmasi(val) {
            var isTrue = (val === true || val === 'TRUE' || val === 'true' || val === 1 || val === '1');
            var cls = isTrue ? 'badge-konfirmasi-true' : 'badge-konfirmasi-false';
            var txt = isTrue ? 'SUDAH' : 'BELUM';

            return '<span class="badge-status ' + cls + '">' + txt + '</span>';
        }

        // ============================
        // SELECT2 - DEPARTEMEN & MESIN
        // ============================
        function initSelect2() {
            page.find('#dept').select2({
                width: '100%',
                placeholder: 'Pilih Departemen',
                allowClear: true,
                ajax: {
                    url: base_url + 'Monteknik/get_departemen',
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
                        '<div class="select2-mesin-kode">' + data.kode + '</div>' +
                        '<div class="select2-mesin-nama">' + data.nama + '</div>' +
                        '</div>'
                    );
                },
                templateSelection: function(data) {
                    if (!data.id) return data.text;
                    return data.nama || data.text;
                }
            });

            page.find('#mesin').select2({
                width: '100%',
                placeholder: 'Pilih Mesin',
                allowClear: true,
                ajax: {
                    url: base_url + 'Monteknik/get_mesin',
                    type: 'GET',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            dept: page.find('#dept').val() || ''
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
                        '<div class="select2-mesin-kode">' + data.kode + '</div>' +
                        '<div class="select2-mesin-nama">' + data.nama + '</div>' +
                        '</div>'
                    );
                },
                templateSelection: function(data) {
                    if (!data.id) return data.text;
                    return data.nama || data.text;
                }
            });

            page.find('#dept').on('change', function() {
                selectedDept = $(this).val() || '';

                // reset mesin setiap kali dept berubah
                page.find('#mesin').val(null).trigger('change');
                selectedMesin = '';

                if (selectedDept) {
                    page.find('#mesin').prop('disabled', false);
                } else {
                    page.find('#mesin').prop('disabled', true);
                }
            });

            page.find('#mesin').on('change', function() {
                selectedMesin = $(this).val() || '';
            });
        }

        // ============================
        // DATATABLE
        // ============================
        function initDataTable() {
            page.find('#tblDetail tfoot th').each(function() {
                var title = $(this).text();

                $(this).html(
                    '<input type="text" class="form-control input-sm" placeholder="Search ' + title + '" />'
                );
            });

            page.find('#tblDetail tfoot tr').appendTo('#tblDetail thead');

            tblDetail = page.find('#tblDetail').DataTable({
                processing: true,
                searching: true,
                paging: true,
                ordering: true,
                order: [
                    [0, 'desc']
                ],
                autoWidth: false,
                responsive: false,
                scrollX: true,
                scrollCollapse: true,
                pageLength: 10,

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
                            browseDetail();
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
                        data: 'TANGGAL_PLP',
                        render: function(d) {
                            return formatDateTimeDisplay(d);
                        }
                    },
                    {
                        data: 'KODE_PLP'
                    },
                    {
                        data: 'NAMA_DEPARTEMEN',
                        className: 'request-wrap'
                    },
                    {
                        data: 'NAMA_MESIN',
                        className: 'request-wrap'
                    },
                    {
                        data: 'PELAPOR',
                        className: 'request-wrap'
                    },
                    {
                        data: 'JENIS_PEKERJAAN'
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
                    },
                    {
                        data: 'STATUS',
                        className: 'text-center',
                        render: function(d) {
                            return renderStatus(d);
                        }
                    },
                    {
                        data: 'KONFIRMASI',
                        className: 'text-center',
                        render: function(d) {
                            return renderKonfirmasi(d);
                        }
                    }
                ],

                columnDefs: [{
                        targets: 1, // Kode PLP
                        width: "60px",
                        className: "text-center"
                    },
                    {
                        targets: 2, // Departemen
                        width: "80px"
                    },
                    {
                        targets: 3, // Mesin
                        width: "100px"
                    },
                    {
                        targets: 4, // Pelapor
                        width: "60px"
                    }
                ]
            });

            tblDetail.columns().every(function() {
                var that = this;

                $('input', this.header()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }

        // ============================
        // LOAD DATA
        // ============================
        function loadDetail(thn, bln, dept, mesin) {
            $.ajax({
                url: base_url + 'Monteknik/get_detail',
                type: 'POST',
                dataType: 'json',
                data: {
                    thn: thn,
                    bln: bln,
                    dept: dept,
                    mesin: mesin
                },
                beforeSend: function() {
                    page.find('#loadingDetail').show();

                    page.find('#btnBrowseMonteknik').prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Loading...'
                    );

                    tblDetail.clear().draw();
                },
                success: function(response) {
                    tblDetail.clear();
                    tblDetail.rows.add(response);
                    tblDetail.draw();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: 'Terjadi kesalahan saat mengambil data PLP Teknik.'
                    });
                    console.log(xhr.responseText);
                },
                complete: function() {
                    page.find('#loadingDetail').hide();

                    page.find('#btnBrowseMonteknik').prop('disabled', false).html(
                        '<i class="fa fa-search"></i> Browse'
                    );
                }
            });
        }

        function browseDetail() {
            var thn = page.find('#thn').val();
            var bln = page.find('#bln').val();
            var dept = page.find('#dept').val();
            var mesin = page.find('#mesin').val();

            if (!thn || !bln) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Tahun dan Bulan wajib dipilih.'
                });
                return;
            }

            selectedDept = dept || '';
            selectedMesin = mesin || '';

            var deptText = dept ? page.find('#dept').select2('data')[0].nama : '-';
            var mesinText = mesin ? page.find('#mesin').select2('data')[0].nama : '-';

            page.find('#infoMesin').html(
                'Departemen : <b>' + deptText + '</b> | Mesin : <b>' + mesinText + '</b>'
            );

            loadDetail(thn, bln, dept, mesin);
        }

        page.find('#btnBrowseMonteknik').on('click', function() {
            browseDetail();
        });

        initSelect2();
        initDataTable();

    });
</script>