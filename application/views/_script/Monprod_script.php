<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    $(function() {

        var page = $('#page-monprod');
        if (page.length === 0) return;

        var tblDetail = null;
        var selectedMesin = '';

        let tblDetailWaktu = null;
        let selectedDetailParam = {};

        function formatNumberOEE(data) {
            if (data === null || data === undefined || data === '') return '0';

            var num = parseFloat(data);
            if (isNaN(num)) return data;

            return num.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        function initSelect2() {
            page.find('#bln').select2({
                width: '100%'
            });

            page.find('#mesin').select2({
                width: '100%',
                placeholder: 'Pilih Mesin',
                allowClear: true,
                ajax: {
                    url: base_url + 'Monprod/get_mesin',
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

            page.find('#mesin').on('change', function() {
                selectedMesin = $(this).val() || '';
            });
        }

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
                        data: 'TANGGAL'
                    },
                    {
                        data: 'TANGGAL_PARAM',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'SHIFT_'
                    },
                    {
                        data: 'NOMOR_KK'
                    },
                    {
                        data: 'PRODUK'
                    },
                    {
                        data: 'PROSES'
                    },
                    {
                        data: 'TARGET',
                        className: 'text-right angka-target',
                        render: formatNumberOEE
                    },
                    {
                        data: 'SAT_TARGET',
                        className: 'text-center angka-target'
                    },
                    {
                        data: 'WAKTU_PROD',
                        className: 'text-right angka-waktu',
                        render: formatNumberOEE
                    },
                    {
                        data: 'WAKTU_NON_PROD',
                        className: 'text-right angka-waktu',
                        render: formatNumberOEE
                    },
                    {
                        data: 'WAKTU_TOTAL',
                        className: 'text-right angka-waktu',
                        render: formatNumberOEE
                    },
                    {
                        data: 'BAIK',
                        className: 'text-right angka-hasil',
                        render: formatNumberOEE
                    },
                    {
                        data: 'RUSAK',
                        className: 'text-right angka-hasil',
                        render: formatNumberOEE
                    },
                    {
                        data: 'OUTPUT',
                        className: 'text-right angka-hasil',
                        render: formatNumberOEE
                    },
                    {
                        data: 'SAT_HASIL_OUTPUT',
                        className: 'text-center angka-hasil'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <button type="button" class="btn btn-xs btn-warning btn-detail-waktu">
                                <i class="fa fa-file-text"></i> Detail
                            </button>
                        `;
                        }
                    }
                ],

                columnDefs: [{
                    targets: 2, // Shift
                    width: "30px",
                    className: "text-center"
                }, {
                    targets: 3, // No KK
                    width: "40px"
                }, {
                    targets: 4,
                    className: 'produk-wrap'
                }]
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

        function loadDetailProduksi(thn, bln, mesin) {
            $.ajax({
                url: base_url + 'Monprod/get_detail',
                type: 'POST',
                dataType: 'json',
                data: {
                    thn: thn,
                    bln: bln,
                    mesin: mesin
                },
                beforeSend: function() {
                    page.find('#loadingDetail').show();

                    page.find('#btnBrowseMonprod').prop('disabled', true).html(
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
                    alert('Gagal mengambil detail produksi.');
                    console.log(xhr.responseText);
                },
                complete: function() {
                    page.find('#loadingDetail').hide();

                    page.find('#btnBrowseMonprod').prop('disabled', false).html(
                        '<i class="fa fa-search"></i> Browse'
                    );
                }
            });
        }

        function browseDetail() {
            var thn = page.find('#thn').val();
            var bln = page.find('#bln').val();
            var mesin = page.find('#mesin').val();

            if (!mesin) {
                alert('Pilih mesin terlebih dahulu');
                return;
            }

            selectedMesin = mesin;

            var mesinText = page.find('#mesin option:selected').text();

            page.find('#infoMesin').html(
                'Mesin : <b>' + mesinText + '</b>'
            );

            loadDetailProduksi(thn, bln, mesin);
        }

        page.find('#btnBrowseMonprod').on('click', function() {
            browseDetail();
        });

        $('#tblDetail').on('click', '.btn-detail-waktu', function() {
            const row = tblDetail.row($(this).closest('tr')).data();

            if (!row) {
                return;
            }

            selectedDetailParam = {
                // mesin: $('#select_mesin').val() || row.MESIN,
                mesin: selectedMesin,
                tanggal: row.TANGGAL_PARAM,
                tanggal_display: row.TANGGAL,
                shift: row.SHIFT_,
                nomor_kk: row.NOMOR_KK,
                proses: row.PROSES,
                produk: row.PRODUK
            };

            // $('#modalDetailInfo').html(`
            //     Mesin: <b>${selectedDetailParam.mesin}</b> |
            //     Tanggal: <b>${formatDateDisplay(selectedDetailParam.tanggal)}</b> |
            //     Shift: <b>${selectedDetailParam.shift}</b> |
            //     KK: <b>${selectedDetailParam.nomor_kk}</b> |
            //     Proses: <b>${selectedDetailParam.proses}</b>
            // `);

            var mesinText = page.find('#mesin option:selected').text();

            page.find('#infoDetailLHP').html(`
                Mesin: <span class="text-primary"><b>${mesinText}</b></span> &nbsp; |
                Tanggal:  <span class="text-success"><b>${formatDateDisplay(selectedDetailParam.tanggal)}</b></span> &nbsp; |
                Shift: <span class="text-info"><b>${selectedDetailParam.shift}</b></span> &nbsp; |                
                Proses: <span class="text-warning"><b>${selectedDetailParam.proses}</b></span> &nbsp; | <br>
                KK: <span class="text-danger"><b>${selectedDetailParam.nomor_kk}</b></span> &nbsp;
                ( <span class="text-danger"><b>${selectedDetailParam.produk}</b></span> )
            `);

            $('#modalDetailWaktu').modal('show');

            loadDetailWaktu();
        });

        function loadDetailWaktu() {
            if (tblDetailWaktu !== null) {
                tblDetailWaktu.ajax.reload(null, false);
                return;
            }

            tblDetailWaktu = $('#tblDetailWaktu').DataTable({
                processing: true,
                searching: true,
                paging: true,
                ordering: true,
                autoWidth: false,
                responsive: false,
                scrollX: false,
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
                            browseDetail();
                        }
                    },
                    {
                        text: '<i class="fa fa-print"></i> &nbsp; Print',
                        extend: 'print',
                        className: 'btn btn-dark btn-sm'
                    }
                ],

                // data: [],
                ajax: {
                    url: "<?= base_url('Monprod/get_detail_waktu') ?>",
                    type: "POST",
                    data: function(d) {
                        d.mesin = selectedDetailParam.mesin;
                        d.tanggal = selectedDetailParam.tanggal;
                        d.shift = selectedDetailParam.shift;
                        d.nomor_kk = selectedDetailParam.nomor_kk;
                        d.proses = selectedDetailParam.proses;
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'NOMOR_LHP'
                    },
                    {
                        data: 'NO_URUT_DETAIL',
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
                        data: 'WAKTU_BLT',
                        className: 'text-right',
                        render: function(data) {
                            return formatDecimal(data);
                        }
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
                    },
                    {
                        data: 'OPERATOR'
                    },
                    {
                        data: 'PENGAWAS'
                    }
                ],
                columnDefs: [{
                        targets: 0, // NOMOR_LHP
                        width: "40px",
                        className: "text-center"
                    },
                    {
                        targets: 1, // NO_URUT_DETAIL
                        width: "40px",
                        className: "text-center"
                    },
                    {
                        targets: 6, // WAKTU
                        width: "50px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#1e21ef'
                            });
                        }
                    },
                    {
                        targets: 7, // BAIK
                        width: "80px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#069144'
                            });
                        }
                    },
                    {
                        targets: 8, // SAT BAIK
                        width: "50px"
                    },
                    {
                        targets: 10, // RUSAK
                        width: "50px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#ed1c1c'
                            });
                        }
                    },
                    {
                        targets: 11, // SAT RUSAK
                        width: "50px"
                    },
                    {
                        targets: 12, // OUTPUT
                        width: "80px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#b507af'
                            });
                        }
                    }
                ],
                footerCallback: function(row, data, start, end, display) {

                    var api = this.api();

                    function parseNumber(i) {
                        return typeof i === 'string' ?
                            parseFloat(i.toString().replace(/,/g, '')) || 0 :
                            typeof i === 'number' ?
                            i :
                            0;
                    }

                    // WAKTU
                    var totalWaktu = api
                        .column(6, {
                            search: 'applied'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return parseNumber(a) + parseNumber(b);
                        }, 0);

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

                    $(api.column(6).footer()).html(formatNumber(totalWaktu));
                    $(api.column(7).footer()).html(formatNumber(totalBaik));
                    $(api.column(10).footer()).html(formatNumber(totalRusak));
                    $(api.column(12).footer()).html(formatNumber(totalOutput));
                }
            });
        }

        initSelect2();
        initDataTable();

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

    });
</script>