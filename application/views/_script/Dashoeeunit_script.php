<script>
    var base_url = '<?= base_url(); ?>';
    var defaultTahun = <?= (int) $tahun ?>;
    var defaultMinggu = <?= (int) $minggu ?>;
</script>

<script>
    $(document).ready(function() {
        var page = $('#page-dashoeeunit');
        if (page.length === 0) return;

        var tblDetailAR = null;
        var tblDetailQR = null;
        var tblDetailPR = null;

        var namaBulan = {
            1: 'Januari',
            2: 'Februari',
            3: 'Maret',
            4: 'April',
            5: 'Mei',
            6: 'Juni',
            7: 'Juli',
            8: 'Agustus',
            9: 'September',
            10: 'Oktober',
            11: 'November',
            12: 'Desember'
        };

        // ================= ISO WEEK HELPERS =================
        function isLeapYear(y) {
            return (y % 4 === 0 && y % 100 !== 0) || (y % 400 === 0);
        }

        function isoWeeksInYear(y) {
            var p = function(year) {
                var d = new Date(Date.UTC(year, 0, 1));
                return d.getUTCDay();
            };
            var jan1Day = p(y);
            return (jan1Day === 4 || (jan1Day === 3 && isLeapYear(y))) ? 53 : 52;
        }

        function getISOWeekRange(year, week) {
            var jan4 = new Date(Date.UTC(year, 0, 4));
            var jan4Day = jan4.getUTCDay() || 7; // Senin=1 ... Minggu=7
            var week1Monday = new Date(jan4);
            week1Monday.setUTCDate(jan4.getUTCDate() - jan4Day + 1);

            var monday = new Date(week1Monday);
            monday.setUTCDate(week1Monday.getUTCDate() + (week - 1) * 7);

            var sunday = new Date(monday);
            sunday.setUTCDate(monday.getUTCDate() + 6);

            return {
                start: monday,
                end: sunday
            };
        }

        function formatShortDate(d) {
            var bulanSingkat = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return String(d.getUTCDate()).padStart(2, '0') + ' ' + bulanSingkat[d.getUTCMonth()];
        }

        function populateMingguDropdown(selectedWeek) {
            var tahun = parseInt($('#tahun').val());
            var totalWeek = isoWeeksInYear(tahun);
            var $minggu = $('#minggu');

            $minggu.empty();

            for (var w = 1; w <= totalWeek; w++) {
                var range = getISOWeekRange(tahun, w);
                var label = 'Minggu ' + w + ' (' + formatShortDate(range.start) + ' - ' + formatShortDate(range.end) + ')';
                $minggu.append('<option value="' + w + '">' + label + '</option>');
            }

            if (selectedWeek && selectedWeek <= totalWeek) {
                $minggu.val(selectedWeek);
            }
        }

        // Inisialisasi awal
        populateMingguDropdown(defaultMinggu);

        // Kalau Tahun berubah, susun ulang daftar Minggu (default ke minggu 1)
        $('#tahun').change(function() {
            populateMingguDropdown(1);
        });

        // ================= END ISO WEEK HELPERS =================


        // KLIK BUTTON BROWSE
        $('#btnBrowse').click(function() {
            loadDashboard();
        });

        // KLIK TOMBOL DETAIL MODAL
        $(document).on('click', '.btn-detail-modal', function() {
            if ($(this).hasClass('disabled')) return;

            let type = $(this).data('type');
            let target = $(this).data('target');

            $(target).modal('show');
            loadDetailModal(type, target);
        });

        function loadDetailModal(type, target) {
            $.ajax({
                url: '<?= site_url("Dashoeeunit/getDetailModal") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    type: type,
                    tahun: $('#tahun').val(),
                    minggu: $('#minggu').val()
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
                        data: 'MESIN',
                        className: 'text-center'
                    },
                    {
                        data: 'NOMOR_KK',
                        className: 'text-center'
                    },
                    {
                        data: 'PROSES'
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
                        targets: 3, // MESIN
                        width: "110px",
                        className: "text-center"
                    },
                    {
                        targets: 4, // NOMOR_KK
                        width: "80px",
                        className: "text-center"
                    },
                    {
                        targets: 5, // PROSES
                        width: "130px"
                    },
                    {
                        targets: 7, // SHIFT_
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 12, // WAKTU_ASLI
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold'
                            });
                        }
                    },
                    {
                        targets: 13, // LIMIT_PLAN
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'background-color': '#f1ddca'
                            });
                        }
                    },
                    {
                        targets: 14, //  WAKTU_BLT
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'background-color': '#7fdf96'
                            });
                        }
                    },
                ],
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

                    var totalWaktu = api
                        .column(13, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    $(api.column(14).footer()).html(formatNumber(totalWaktu));
                }
            });

            buildFilterRow('tblDetailAR', tblDetailAR);
        }

        function renderDetailQR(data) {
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
                        data: 'MESIN',
                        className: 'text-center'
                    },
                    {
                        data: 'NOMOR_KK',
                        className: 'text-center'
                    },
                    {
                        data: 'PROSES'
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
                        targets: 0,
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 1,
                        width: "90px"
                    },
                    {
                        targets: 2,
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 3, // MESIN
                        width: "110px",
                        className: "text-center"
                    },
                    {
                        targets: 4, // NOMOR_KK
                        width: "80px",
                        className: "text-center"
                    },
                    {
                        targets: 5, // PROSES
                        width: "130px"
                    },
                    {
                        targets: 7, // SHIFT_
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 9, // BAIK
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold'
                            });
                        }
                    },
                    {
                        targets: 10, // SAT_HASIL_BAIK
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 11, // NAMA_WASTE
                        width: "210px"
                    },
                    {
                        targets: 12, // RUSAK
                        width: "70px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold'
                            });
                        }
                    },
                    {
                        targets: 13, // SAT_HASIL_RUSAK
                        width: "70px",
                        className: "text-center"
                    },
                    {
                        targets: 14, // OUTPUT
                        width: "80px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold'
                            });
                        }
                    }
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

                    var totalBaik = api.column(9, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    var totalRusak = api.column(12, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    var totalOutput = api.column(14, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    $(api.column(9).footer()).html(formatNumber(totalBaik));
                    $(api.column(12).footer()).html(formatNumber(totalRusak));
                    $(api.column(14).footer()).html(formatNumber(totalOutput));
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
                        data: 'MESIN',
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
                    },
                    {
                        targets: 1,
                        width: "50px"
                    },
                    {
                        targets: 2, // MESIN
                        width: "110px"
                    },
                    {
                        targets: 3, // NOMOR_KK
                        width: "80px"
                    },
                    {
                        targets: 4,
                        width: "150px"
                    },
                    {
                        targets: 5,
                        width: "130px"
                    },
                    {
                        targets: 6,
                        width: "90px",
                        createdCell: function(td) {
                            $(td).css('font-weight', 'bold');
                        }
                    },
                    {
                        targets: 7,
                        width: "90px",
                        createdCell: function(td) {
                            $(td).css('font-weight', 'bold');
                        }
                    },
                    {
                        targets: 8,
                        width: "90px"
                    },
                    {
                        targets: 9,
                        width: "90px"
                    }
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

                    var totalOutput = api.column(6, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    var totalWaktu = api.column(7, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

                    var allTarget = api.column(8, {
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

                    $(api.column(6).footer()).html(formatNumber(totalOutput));
                    $(api.column(7).footer()).html(formatDecimal(totalWaktu));
                    $(api.column(8).footer()).html(
                        '<span title="Rata-rata dari semua baris">' + formatDecimal(avgTarget) + '</span>'
                    );
                }
            });

            buildFilterRow('tblDetailPR', tblDetailPR);
        }

        function buildFilterRow(tableId, dtInstance) {
            var wrapper = $('#' + tableId).closest('.dataTables_wrapper');
            var scrollHeadTable = wrapper.find('.dataTables_scrollHead table thead');

            scrollHeadTable.find('tr.filter-row').remove();

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

            dtInstance.on('draw.filterRow', function() {
                var scrollHead = wrapper.find('.dataTables_scrollHead table thead');
                if (scrollHead.find('tr.filter-row').length === 0) {
                    filterRow.find('input').each(function() {
                        var colIdx = $(this).data('col');
                        $(this).val(dtInstance.column(colIdx).search());
                    });
                    scrollHead.append(filterRow);
                }
            });
        }

        function loadDashboard() {
            let tahun = $('#tahun').val();
            let minggu = $('#minggu').val();

            $.ajax({
                url: '<?= site_url("Dashoeeunit/getDashboard") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    tahun: tahun,
                    minggu: minggu
                },
                beforeSend: function() {
                    page.find('#loadingDashboardUnit').show();

                    page.find('#btnBrowse').prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Loading...'
                    );
                },
                success: function(res) {
                    let s = res.summary;
                    let r = res.range; // { awal: 'YYYY-MM-DD', akhir: 'YYYY-MM-DD' } dari server (otoritatif)

                    function toDisplayDate(iso) {
                        let p = iso.split('-');
                        return p[2] + '/' + p[1] + '/' + p[0];
                    }

                    $('#dashboardTitle').html(
                        '<div class="dashboard-title-main">Dashboard OEE Pura TSS-01</div>' +
                        '<div class="dashboard-title-sub">Tahun ' + tahun + ' - Minggu ' + minggu +
                        ' (' + toDisplayDate(r.awal) + ' - ' + toDisplayDate(r.akhir) + ')</div>'
                    );

                    renderGauge('chartAR', 'Availability', parseFloat(s.AR), parseFloat(s.TARGET_AR));
                    renderGauge('chartPR', 'Performance', parseFloat(s.PR), parseFloat(s.TARGET_PR));
                    renderGauge('chartQR', 'Quality', parseFloat(s.QR), parseFloat(s.TARGET_QR));

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
                    page.find('#loadingDashboardUnit').hide();

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

        // RESIZE HANDLER
        window.addEventListener('resize', function() {
            ['chartAR', 'chartPR', 'chartQR', 'chartDowntime', 'chartDefect', 'chartActualTarget'].forEach(function(id) {
                let instance = echarts.getInstanceByDom(document.getElementById(id));
                if (instance) instance.resize();
            });
        });

    });
</script>