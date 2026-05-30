<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    $(function() {

        var page = $('#page-monprod');
        if (page.length === 0) return;

        var tblDetail = null;
        var selectedMesin = '';

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
                    }
                ],

                columnDefs: [{
                    targets: 3,
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

        initSelect2();
        initDataTable();

    });
</script>