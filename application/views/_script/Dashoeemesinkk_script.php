<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    $(document).ready(function() {
        var page = $('#page-dashoeekk');
        if (page.length === 0) return;

        var tblDetailAR = null;
        var tblDetailQR = null;
        var tblDetailPR = null;

        // DROPDOWN MESIN
        $('#mesin').select2({
            placeholder: 'Pilih Mesin',
            ajax: {
                url: '<?= site_url("Dashoeemesinkk/getMesin") ?>',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return data;
                }
            },
            templateResult: formatMesin,
            templateSelection: function(data) {
                return data.text || data.mesin;
            }
        });

        function formatMesin(data) {
            if (!data.id) return data.text;

            return $(
                '<div class="select2-kk-row">' +
                '<div class="select2-kk-nomor">' + data.kdmesin + '</div>' +
                '<div class="select2-kk-produk">' + data.mesin + '</div>' +
                '</div>'
            );
        }

        //DROPDOWN NOMOR KK
        $('#nomor_kk').select2({
            placeholder: 'Pilih Nomor KK',
            ajax: {
                url: '<?= site_url("Dashoeemesinkk/getKK") ?>',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term,
                        thn_kk: $('#tahun_kk').val()
                    };
                },
                processResults: function(data) {
                    return data;
                }
            },
            templateResult: formatKK,
            templateSelection: function(data) {
                return data.nomor_kk || data.text;
            }
        });

        function formatKK(data) {
            if (!data.id) return data.text;

            return $(
                '<div class="select2-kk-row">' +
                '<div class="select2-kk-nomor">' + data.nomor_kk + '</div>' +
                '<div class="select2-kk-produk">' + data.nama_barang + '</div>' +
                '</div>'
            );
        }

        // RESET NOMOR KK SAAT TAHUN KK BERUBAH
        $('#tahun_kk').change(function() {
            $('#nomor_kk').val(null).trigger('change');
        });

        // TOMBOL CLEAR KK
        $('#btnClearKK').click(function() {
            $('#tahun_kk').val(<?= date('Y') ?>);
            $('#nomor_kk').val(null).trigger('change');
        });

        // KLIK BUTTON BROWSE
        $('#btnBrowse').click(function() {
            loadDashboard();
        });

        // KLIK TOMBOL DETAIL MODAL
        $(document).on('click', '.btn-detail-modal', function() {
            if ($(this).hasClass('disabled')) return;

            let type = $(this).data('type');
            let target = $(this).data('target');

            let mesinData = $('#mesin').select2('data')[0];
            if (!mesinData) {
                alert('Mesin wajib dipilih.');
                return;
            }

            let kkData = $('#nomor_kk').select2('data')[0] || null;

            $(target).modal('show');
            loadDetailModal(type, target, mesinData, kkData);
        });

        function loadDetailModal(type, target, mesinData, kkData) {
            $.ajax({
                url: '<?= site_url("Dashoeemesinkk/getDetailModal") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    type: type,
                    tahun: $('#tahun').val(),
                    bulan: $('#bulan').val(),
                    kdmesin: mesinData.kdmesin,
                    nomor_kk: kkData ? kkData.nomor_kk : '',
                    tanggal_kk: kkData ? kkData.tanggal_kk : ''
                },
                beforeSend: function() {
                    $(target).find('tbody').html(
                        '<tr><td colspan="20" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>'
                    );
                },
                success: function(res) {
                    if (type === 'AR') {
                        renderDetailAR(res.data);
                    } else if (type === 'QR') {
                        renderDetailQR(res.data);
                    } else if (type === 'PR') {
                        renderDetailPR(res.data);
                    }
                },
                error: function() {
                    $(target).find('tbody').html(
                        '<tr><td colspan="20" class="text-center">Gagal mengambil data.</td></tr>'
                    );
                }
            });
        }

        function renderDetailAR(data) {
            // Destroy dengan cara yang aman — cek via $.fn.DataTable.isDataTable
            if ($.fn.DataTable.isDataTable('#tblDetailAR')) {
                $('#tblDetailAR').DataTable().destroy();
            }
            $('#tblDetailAR tbody').empty();

            tblDetailAR = $('#tblDetailAR').DataTable({
                data: data,
                processing: true,
                searching: true,
                paging: true,
                ordering: true,
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
                        text: '<i class="fa fa-print"></i> &nbsp; Print',
                        extend: 'print',
                        className: 'btn btn-dark btn-sm'
                    }
                ],

                columns: [{
                        data: 'NOMOR_LHP',
                        className: 'text-center'
                    },
                    {
                        data: 'TANGGAL'
                    },
                    {
                        data: 'NO_URUT_DETAIL',
                        className: 'text-center'
                    },
                    {
                        data: 'NOMOR_KK',
                        className: 'text-center'
                    },
                    {
                        data: 'PRODUK'
                    },
                    {
                        data: 'SHIFT_',
                        className: 'text-center'
                    },
                    {
                        data: 'KEGIATAN'
                    },
                    {
                        data: 'KTG_LOSSTIME'
                    },
                    {
                        data: 'JAM1',
                        className: 'text-center',
                        render: function(data) {
                            return formatDateTimeDisplay(data);
                        }
                    },
                    {
                        data: 'JAM2',
                        className: 'text-center',
                        render: function(data) {
                            return formatDateTimeDisplay(data);
                        }
                    },
                    {
                        data: 'WAKTU_BLT_ASLI',
                        className: 'text-right',
                        render: function(data) {
                            return formatDecimal(data);
                        }
                    },
                    {
                        data: 'LIMITPLAN',
                        className: 'text-right',
                        render: function(data) {
                            return formatDecimal(data);
                        }
                    },
                    {
                        data: 'WAKTU_BLT',
                        className: 'text-right',
                        render: function(data) {
                            return formatDecimal(data);
                        }
                    },
                    {
                        data: 'PAR_LIMITPLAN',
                        className: 'text-center'
                    }
                ],
                columnDefs: [{
                        targets: 0, // NOMOR_LHP
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 1, // TANGGAL
                        width: "90px"
                    },
                    {
                        targets: 2, // NO_URUT_DETAIL
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 3, // NOMOR_KK
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 5, // SHIFT_
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 10, // WAKTU_ASLI
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold'
                                // 'color': '#1e21ef'
                            });
                        }
                    },
                    {
                        targets: 11, // LIMIT_PLAN
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'background-color': '#f1ddca'
                            });
                        }
                    },
                    {
                        targets: 12, //  WAKTU_BLT
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'background-color': '#7fdf96'
                            });
                        }
                    },
                ],
                // scrollX: true,
                // autoWidth: false,
                // pageLength: 25,
                language: {
                    emptyTable: 'Tidak ada data'
                },
                rowCallback: function(row, data) {
                    if (data.KEGIATAN && data.KEGIATAN.indexOf('OVER - ') === 0) {
                        $(row).css('color', 'red');
                    }
                },
                footerCallback: function(row, data, start, end, display) {

                    var api = this.api();

                    function parseNumber(i) {
                        return typeof i === 'string' ?
                            parseFloat(i.toString().replace(/,/g, '')) || 0 :
                            typeof i === 'number' ?
                            i :
                            0;
                    }

                    // WAKTU_BLT
                    var totalWaktu = api
                        .column(11, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);


                    $(api.column(12).footer()).html(formatNumber(totalWaktu));
                }
            });

            buildFilterRow('tblDetailAR', tblDetailAR);
        }

        function renderDetailQR(data) {
            // Destroy dengan cara yang aman — cek via $.fn.DataTable.isDataTable
            if ($.fn.DataTable.isDataTable('#tblDetailQR')) {
                $('#tblDetailQR').DataTable().destroy();
            }
            $('#tblDetailQR tbody').empty();

            tblDetailQR = $('#tblDetailQR').DataTable({
                data: data,
                processing: true,
                searching: true,
                paging: true,
                ordering: true,
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
                        text: '<i class="fa fa-print"></i> &nbsp; Print',
                        extend: 'print',
                        className: 'btn btn-dark btn-sm'
                    }
                ],

                columns: [{
                        data: 'NOMOR_LHP',
                        className: 'text-center'
                    },
                    {
                        data: 'TANGGAL'
                    },
                    {
                        data: 'NO_URUT_DETAIL',
                        className: 'text-center'
                    },
                    {
                        data: 'NOMOR_KK',
                        className: 'text-center'
                    },
                    {
                        data: 'PRODUK'
                    },
                    {
                        data: 'SHIFT_',
                        className: 'text-center'
                    },
                    {
                        data: 'KEGIATAN'
                    },
                    {
                        data: 'BAIK',
                        className: 'text-right',
                        render: function(data) {
                            return formatNumber(data);
                        }
                    },
                    {
                        data: 'SAT_HASIL_BAIK'
                    },
                    {
                        data: 'NAMA_WASTE'
                    },
                    {
                        data: 'RUSAK',
                        className: 'text-right',
                        render: function(data) {
                            return formatNumber(data);
                        }
                    },
                    {
                        data: 'SAT_HASIL_RUSAK'
                    },
                    {
                        data: 'OUTPUT',
                        className: 'text-right',
                        render: function(data) {
                            return formatNumber(data);
                        }
                    }
                ],
                columnDefs: [{
                        targets: 0, // NOMOR_LHP
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 1, // TANGGAL
                        width: "90px"
                    },
                    {
                        targets: 2, // NO_URUT_DETAIL
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 3, // NOMOR_KK
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 5, // SHIFT_
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 7, // BAIK
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold'
                                // 'color': '#1e21ef'
                            });
                        }
                    },
                    {
                        targets: 8, // SAT_HASIL_BAIK
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 9, // NAMA_WASTE
                        width: "210px"
                    },
                    {
                        targets: 10, // RUSAK
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold'
                                // 'color': '#1e21ef'
                            });
                        }
                    },
                    {
                        targets: 11, // SAT_HASIL_RUSAK
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 12, // OUTPUT
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold'
                                // 'color': '#1e21ef'
                            });
                        }
                    },
                ],
                // scrollX: true,
                // autoWidth: false,
                // pageLength: 25,
                language: {
                    emptyTable: 'Tidak ada data'
                },
                rowCallback: function(row, data) {
                    if (data.KEGIATAN && data.KEGIATAN.indexOf('PRODUKSI MURNI') === 0) {
                        $(row).css('color', 'green');
                    }
                },
                footerCallback: function(row, data, start, end, display) {

                    var api = this.api();

                    function parseNumber(i) {
                        return typeof i === 'string' ?
                            parseFloat(i.toString().replace(/,/g, '')) || 0 :
                            typeof i === 'number' ?
                            i :
                            0;
                    }

                    // BAIK
                    var totalBaik = api
                        .column(7, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    // RUSAK
                    var totalRusak = api
                        .column(10, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    // OUTPUT
                    var totalOutput = api
                        .column(12, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    $(api.column(7).footer()).html(formatNumber(totalBaik));
                    $(api.column(10).footer()).html(formatNumber(totalRusak));
                    $(api.column(12).footer()).html(formatNumber(totalOutput));
                }
            });

            buildFilterRow('tblDetailQR', tblDetailQR);
        }

        function renderDetailPR(data) {
            if ($.fn.DataTable.isDataTable('#tblDetailPR')) {
                $('#tblDetailPR').DataTable().destroy();
            }
            $('#tblDetailPR tbody').empty();

            tblDetailPR = $('#tblDetailPR').DataTable({
                data: data,
                processing: true,
                searching: true,
                paging: true,
                ordering: true,
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
                        text: '<i class="fa fa-print"></i> &nbsp; Print',
                        extend: 'print',
                        className: 'btn btn-dark btn-sm'
                    }
                ],

                columns: [{
                        data: 'TANGGAL',
                        className: 'text-center'
                    },
                    {
                        data: 'SHIFT_',
                        className: 'text-center'
                    },
                    {
                        data: 'NOMOR_KK',
                        className: 'text-center'
                    },
                    {
                        data: 'PRODUK'
                    },
                    {
                        data: 'PROSES'
                    },
                    {
                        data: 'TOTAL_OUTPUT',
                        className: 'text-right',
                        render: function(data) {
                            return formatNumber(data);
                        }
                    },
                    {
                        data: 'WAKTU_PRODUKSI',
                        className: 'text-right',
                        render: function(data) {
                            return formatDecimal(data);
                        }
                    },
                    {
                        data: 'AVG_TARGET',
                        className: 'text-right',
                        render: function(data) {
                            return formatDecimal(data);
                        }
                    },
                    {
                        data: 'PR',
                        className: 'text-right',
                        render: function(data) {
                            if (data === null || data === undefined) return '-';
                            var val = parseFloat(data);
                            var color = val >= 85 ? 'green' : (val >= 60 ? 'orange' : 'red');
                            return '<span style="font-weight:bold; color:' + color + ';">' +
                                formatDecimal(data) + '%</span>';
                        }
                    }
                ],

                columnDefs: [{
                        targets: 0,
                        width: "90px"
                    }, // TANGGAL
                    {
                        targets: 1,
                        width: "50px"
                    }, // SHIFT
                    {
                        targets: 2,
                        width: "80px"
                    }, // NOMOR_KK
                    {
                        targets: 3,
                        width: "150px"
                    }, // PRODUK
                    {
                        targets: 4,
                        width: "150px"
                    }, // PROSES
                    {
                        targets: 5,
                        width: "90px", // TOTAL_OUTPUT
                        createdCell: function(td) {
                            $(td).css('font-weight', 'bold');
                        }
                    },
                    {
                        targets: 6,
                        width: "90px", // WAKTU_PRODUKSI
                        createdCell: function(td) {
                            $(td).css('font-weight', 'bold');
                        }
                    },
                    {
                        targets: 7,
                        width: "90px"
                    }, // AVG_TARGET
                    {
                        targets: 8,
                        width: "90px"
                    } // PR
                ],

                language: {
                    emptyTable: 'Tidak ada data'
                },

                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    function parseNumber(i) {
                        return typeof i === 'string' ?
                            parseFloat(i.toString().replace(/,/g, '')) || 0 :
                            typeof i === 'number' ? i : 0;
                    }

                    // Total Output
                    var totalOutput = api.column(5, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    // Total Waktu Produksi
                    var totalWaktu = api.column(6, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    // AVG Rata-rata Target KK (semua data, bukan hanya page aktif)
                    var allTarget = api.column(7, {
                        search: 'applied'
                    }).data();
                    var validTarget = [];
                    allTarget.each(function(val) {
                        var n = parseNumber(val);
                        if (n > 0) validTarget.push(n);
                    });
                    var avgTarget = validTarget.length > 0 ?
                        validTarget.reduce(function(a, b) {
                            return a + b;
                        }, 0) / validTarget.length :
                        0;

                    $(api.column(5).footer()).html(formatNumber(totalOutput));
                    $(api.column(6).footer()).html(formatDecimal(totalWaktu));
                    $(api.column(7).footer()).html(
                        '<span title="Rata-rata dari semua baris">' + formatDecimal(avgTarget) + '</span>'
                    );
                }
            });

            buildFilterRow('tblDetailPR', tblDetailPR);
        }

        function buildFilterRow(tableId, dtInstance) {
            // Dengan scrollX, DataTables membuat clone thead di div.dataTables_scrollHead
            // Kita harus append filter row ke clone thead tersebut, bukan thead asli

            var wrapper = $('#' + tableId).closest('.dataTables_wrapper');
            var scrollHeadTable = wrapper.find('.dataTables_scrollHead table thead');

            // Hapus filter row lama kalau ada
            scrollHeadTable.find('tr.filter-row').remove();

            // Buat filter row
            var filterRow = $('<tr class="filter-row"></tr>');

            dtInstance.columns().every(function(colIdx) {
                var column = this;
                $('<td>')
                    .appendTo(filterRow)
                    .append(
                        $('<input>', {
                            type: 'text',
                            placeholder: 'Cari...',
                            class: 'form-control input-sm',
                            'data-col': colIdx
                        }).on('input', function() {
                            column.search(this.value).draw(false);
                        })
                    );
            });

            scrollHeadTable.append(filterRow);

            // Setiap draw, re-append ke scrollHead (bukan thead asli)
            dtInstance.on('draw.filterRow', function() {
                var scrollHead = wrapper.find('.dataTables_scrollHead table thead');
                if (scrollHead.find('tr.filter-row').length === 0) {
                    // Restore nilai search yang sudah ada sebelumnya
                    filterRow.find('input').each(function() {
                        var colIdx = $(this).data('col');
                        $(this).val(dtInstance.column(colIdx).search());
                    });
                    scrollHead.append(filterRow);
                }
            });
        }

        function loadDashboard() {
            let mesinData = $('#mesin').select2('data')[0];
            let kkData = $('#nomor_kk').select2('data')[0] || null;

            // if (!mesinData || !kkData) {
            //     alert('Mesin dan Nomor KK wajib dipilih.');
            //     return;
            // }
            if (!mesinData) {
                alert('Mesin wajib dipilih.');
                return;
            }

            console.log('tahun:', $('#tahun').val());
            console.log('bulan:', $('#bulan').val());
            console.log('kdmesin:', mesinData.kdmesin);
            // console.log('nomor_kk:', kkData.nomor_kk);
            // console.log('tanggal_kk:', kkData.tanggal_kk);

            $.ajax({
                url: '<?= site_url("Dashoeemesinkk/getDashboard") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    tahun: $('#tahun').val(),
                    bulan: $('#bulan').val(),
                    kdmesin: mesinData.kdmesin,
                    nomor_kk: kkData ? kkData.nomor_kk : '',
                    tanggal_kk: kkData ? kkData.tanggal_kk : ''
                },
                beforeSend: function() {
                    page.find('#loadingDashboardKK').show();

                    page.find('#btnBrowse').prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Loading...'
                    );

                    // tblDetail.clear().draw();
                },
                success: function(res) {
                    let s = res.summary;

                    let subTitle = kkData ?
                        kkData.nomor_kk + ' - ' + kkData.nama_barang :
                        'Semua KK';

                    $('#dashboardTitle').html(
                        '<div class="dashboard-title-main">Dashboard OEE - ' + mesinData.mesin + '</div>' +
                        '<div class="dashboard-title-sub">' + subTitle + '</div>'
                    );

                    renderGauge('chartAR', 'Availability', parseFloat(s.AR), parseFloat(s.TARGET_AR));
                    renderGauge('chartPR', 'Performance', parseFloat(s.PR), parseFloat(s.TARGET_PR));
                    renderGauge('chartQR', 'Quality', parseFloat(s.QR), parseFloat(s.TARGET_QR));

                    // renderBarHorizontal('chartDowntime', 'Top 5 Unplanned Downtime', res.downtime, 'KEGIATAN', 'PERSEN', '%');
                    // renderBarHorizontal('chartDefect', 'Top 5 Defect', res.defect, 'KEGIATAN', 'JUMLAH', '');
                    renderBarHorizontal('chartDowntime', 'Top 5 Unplanned Downtime', res.downtime, 'KEGIATAN', 'PERSEN', '%', [{
                            label: 'Jumlah Downtime (kali)',
                            field: 'FREQ_DOWNTIME'
                        },
                        {
                            label: 'Total Jam Downtime (Jam)',
                            field: 'WAKTU_DOWNTIME'
                        }
                    ]);
                    renderBarHorizontal('chartDefect', 'Top 5 Defect', res.defect, 'KEGIATAN', 'PERSEN', '%', [{
                        label: 'Jumlah Defect (Satuan dalam Proses)',
                        field: 'JUMLAH'
                    }]);
                    renderActualTarget(res.actual_target);

                    let ar = parseFloat(s.AR || 0);
                    let pr = parseFloat(s.PR || 0);
                    let qr = parseFloat(s.QR || 0);
                    let oee = parseFloat(s.OEE || 0);

                    console.log('RESPONSE:', res);
                    console.log('SUMMARY:', res.summary);
                    console.log('DOWNTIME:', res.downtime);
                    console.log('DEFECT:', res.defect);
                    console.log('ACTUAL TARGET:', res.actual_target);

                    $('#oeeScore').text(oee.toFixed(2) + '%');
                    $('#oeeStatus').text(getOeeStatus(oee));

                    $('#oeeFormula').html(
                        'AR x PR x QR = ' +
                        ar.toFixed(2) + '% x ' +
                        pr.toFixed(2) + '% x ' +
                        qr.toFixed(2) + '% '
                    );

                    setOeeProgress(ar, pr, qr);
                },
                error: function() {
                    alert('Gagal mengambil data dashboard.');
                },
                complete: function() {
                    page.find('#loadingDashboardKK').hide();

                    page.find('#btnBrowse').prop('disabled', false).html(
                        '<i class="fa fa-search"></i> Browse'
                    );
                }
            });
        }

        function getRandomColors(total) {
            let colors = [
                '#E74C3C', '#3498DB', '#2ECC71', '#F39C12', '#9B59B6',
                '#1ABC9C', '#34495E', '#D35400', '#7F8C8D', '#C0392B'
            ];

            let result = [];

            for (let i = 0; i < total; i++) {
                result.push(colors[i % colors.length]);
            }

            return result;
        }

        function renderGauge(id, title, value, target) {
            // let chart = echarts.init(document.getElementById(id));
            let dom = document.getElementById(id);
            let chart = echarts.getInstanceByDom(dom);
            if (chart) chart.dispose();
            chart = echarts.init(dom);

            let gaugeColor = value >= target ? '#008000' : '#ff0000';

            chart.setOption({
                title: {
                    text: title,
                    left: 'center',
                    top: 5
                },
                series: [{
                    type: 'gauge',
                    min: 0,
                    max: 100,
                    progress: {
                        show: true,
                        width: 18,
                        itemStyle: {
                            color: gaugeColor
                        }
                    },
                    axisLine: {
                        lineStyle: {
                            width: 18
                        }
                    },
                    pointer: {
                        show: true
                    },
                    detail: {
                        valueAnimation: true,
                        formatter: '{value}%',
                        fontSize: 24,
                        offsetCenter: [0, '60%']
                    },
                    title: {
                        fontSize: 14,
                        fontWeight: 'bold',
                        color: '#555',
                        offsetCenter: [0, '90%']
                    },
                    data: [{
                        value: value || 0,
                        name: 'Target ' + target + '%'
                    }]
                }]
            });

            // window.addEventListener('resize', function() {
            //     chart.resize();
            // });
        }

        function renderBarHorizontal(id, title, data, labelField, valueField, suffix, extraFields) {
            let dom = document.getElementById(id);
            let chart = echarts.getInstanceByDom(dom);
            if (chart) chart.dispose();
            chart = echarts.init(dom);

            let labels = [];
            let values = [];
            let rawRows = [];

            data.forEach(function(row) {
                labels.push(row[labelField]);
                values.push(parseFloat(row[valueField] || 0));
                rawRows.push(row);
            });

            labels = labels.reverse();
            values = values.reverse();
            rawRows = rawRows.reverse();

            let colors = getRandomColors(values.length);

            chart.setOption({
                title: {
                    text: title,
                    left: 'center',
                    top: 5
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    },
                    formatter: function(params) {
                        let idx = params[0].dataIndex;
                        let row = rawRows[idx];

                        let html = '<b>' + labels[idx] + '</b><br/>';
                        html += params[0].seriesName + ': ' + values[idx] + suffix + '<br/>';

                        if (extraFields) {
                            extraFields.forEach(function(f) {
                                let val = row[f.field];
                                html += f.label + ': ' + (val !== undefined && val !== null ? val : '-') + (f.suffix || '') + '<br/>';
                            });
                        }

                        return html;
                    }
                },
                grid: {
                    left: '35%',
                    right: '10%',
                    bottom: '10%',
                    top: '20%'
                },
                xAxis: {
                    type: 'value'
                },
                yAxis: {
                    type: 'category',
                    data: labels,
                    axisLabel: {
                        fontSize: 11,
                        width: 120,
                        overflow: 'break',
                        lineOverflow: 'truncate',
                        formatter: function(value) {
                            var maxLen = 18;
                            if (value.length <= maxLen) return value;
                            var words = value.split(' ');
                            var lines = [];
                            var current = '';
                            words.forEach(function(word) {
                                if ((current + ' ' + word).trim().length > maxLen) {
                                    if (current) lines.push(current.trim());
                                    current = word;
                                } else {
                                    current = (current + ' ' + word).trim();
                                }
                            });
                            if (current) lines.push(current.trim());
                            return lines.join('\n');
                        }
                    }
                },
                series: [{
                    type: 'bar',
                    name: title,
                    data: values.map(function(value, index) {
                        return {
                            value: value,
                            itemStyle: {
                                color: colors[index]
                            }
                        };
                    }),
                    label: {
                        show: true,
                        position: 'right',
                        formatter: '{c}' + suffix
                    }
                }]
            });

            setTimeout(function() {
                chart.resize();
            }, 50);
        }

        function renderActualTarget(data) {
            let dom = document.getElementById('chartActualTarget');
            let chart = echarts.getInstanceByDom(dom);
            if (chart) chart.dispose();
            chart = echarts.init(dom);

            let actualVal = parseFloat(data.ACTUAL_OUTPUT || 0);
            let targetVal = parseFloat(data.TARGET_OUTPUT || 0);
            let totalOutput = parseFloat(data.TOTAL_OUTPUT || 0);
            let totalWaktu = parseFloat(data.TOTAL_WAKTU_PRODUKSI || 0);

            chart.setOption({
                title: {
                    text: 'Actual / Target',
                    left: 'center',
                    top: 5
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    },
                    formatter: function(params) {
                        let p = params[0];
                        let html = '<b>' + p.name + '</b><br/>';

                        if (p.name === 'Actual') {
                            html += 'Actual Output/Jam : <b>' + formatDecimal(actualVal) + '</b><br/>';
                            html += '<span style="color:#888; font-size:11px;">';
                            html += 'Total Output : ' + formatNumber(totalOutput) + '<br/>';
                            html += 'Waktu Produksi : ' + formatDecimal(totalWaktu) + ' Jam<br/>';
                            html += '= ' + formatNumber(totalOutput) + ' / ' + formatDecimal(totalWaktu) + ' Jam';
                            html += '</span>';
                        } else {
                            html += 'Rata-rata Target KK : <b>' + formatDecimal(targetVal) + '</b><br/>';
                            html += '<span style="color:#888; font-size:11px;">AVG(TARGET) dari data terpilih</span>';
                        }

                        return html;
                    }
                },
                xAxis: {
                    type: 'category',
                    data: ['Actual', 'Target']
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    type: 'bar',
                    data: [{
                            value: actualVal,
                            itemStyle: {
                                color: '#2ECC71'
                            }
                        },
                        {
                            value: targetVal,
                            itemStyle: {
                                color: '#E67E22'
                            }
                        }
                    ],
                    label: {
                        show: true,
                        position: 'inside'
                    }
                }]
            });
        }

        function getOeeStatus(oee) {
            if (oee >= 100) return 'Perfect';
            if (oee >= 85) return 'World Class';
            if (oee >= 60) return 'Standard';
            return 'Low';
        }

        function setOeeProgress(ar, pr, qr) {
            ar = parseFloat(ar) || 0;
            pr = parseFloat(pr) || 0;
            qr = parseFloat(qr) || 0;

            $('#progressARText').text(ar.toFixed(2) + '%');
            $('#progressPRText').text(pr.toFixed(2) + '%');
            $('#progressQRText').text(qr.toFixed(2) + '%');

            $('#progressAR').css('width', Math.min(ar, 100) + '%');
            $('#progressPR').css('width', Math.min(pr, 100) + '%');
            $('#progressQR').css('width', Math.min(qr, 100) + '%');
        }

        function formatDateTimeMinute(value) {
            if (!value) return '';

            let dateStr = value;

            if (typeof value === 'object' && value.date) {
                dateStr = value.date;
            }

            dateStr = String(dateStr).replace('T', ' ').substring(0, 16);

            const parts = dateStr.split(' ');
            if (parts.length < 2) return value;

            const datePart = parts[0];
            const timePart = parts[1];

            const d = datePart.split('-');
            if (d.length !== 3) return value;

            return d[2] + '/' + d[1] + '/' + d[0] + ' ' + timePart;
        }

        function formatDecimal(value) {
            if (value === null || value === undefined || value === '') {
                return '0';
            }

            return parseFloat(value).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        function formatNumber(value) {
            if (value === null || value === undefined || value === '') {
                return '0';
            }

            return parseFloat(value).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        function formatDateDisplay(value) {

            if (!value) {
                return '';
            }

            let dateStr = value;

            if (typeof value === 'object' && value.date) {
                dateStr = value.date;
            }

            dateStr = String(dateStr).substring(0, 10);

            const parts = dateStr.split('-');

            if (parts.length !== 3) {
                return value;
            }

            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }

        function formatDateTimeDisplay(value) {
            if (!value) return '';

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

        // RESIZE HANDLER — cukup 1x, handle semua chart sekaligus
        window.addEventListener('resize', function() {
            ['chartAR', 'chartPR', 'chartQR', 'chartDowntime', 'chartDefect', 'chartActualTarget'].forEach(function(id) {
                let instance = echarts.getInstanceByDom(document.getElementById(id));
                if (instance) instance.resize();
            });
        });

    });
</script>