<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    var tblDetail;
    var tblMesin;

    var selectedDept = '';
    var selectedMesin = '';

    function loadDetailProduksi(thn, bln, dept, mesin) {
        $.ajax({

            url: base_url + 'Monprod/get_detail',
            type: 'POST',

            data: {
                thn: thn,
                bln: bln,
                dept: dept,
                mesin: mesin
            },

            dataType: 'JSON',

            beforeSend: function() {

                $('#loadingDetail').show();

                tblDetail.clear();
                tblDetail.draw();

            },

            success: function(response) {

                tblDetail.clear();
                tblDetail.rows.add(response);
                tblDetail.draw();

            },

            complete: function() {

                $('#loadingDetail').hide();

            },

            error: function(xhr) {

                $('#loadingDetail').hide();

                alert('Gagal mengambil detail produksi.');
                console.log(xhr.responseText);

            }

        });
    }

    $(document).ready(function() {

        // SELECT2
        $('.select2_single').select2({
            width: '100%'
        });

        // TABEL MESIN
        tblMesin = $('#tblMesin').DataTable({

            processing: true,

            paging: false,
            info: false,

            searching: false,
            ordering: true,

            scrollY: '550px',
            scrollCollapse: true,

            fixedHeader: true,
            scrollY: '550px',
            scrollCollapse: true,
            fixedHeader: true,

            data: [],

            columns: [{
                    data: 'NAMA_DEPARTEMEN'
                },
                {
                    data: 'MESIN'
                }
            ]

        });

        // KLIK TOMBOL BROWSE
        $('#btnBrowse').on('click', function() {

            var thn = $('#thn').val();
            var bln = $('#bln').val();

            $.ajax({

                url: "<?= base_url('Monprod/get_mesin') ?>",
                type: "POST",

                data: {
                    thn: thn,
                    bln: bln
                },

                dataType: "JSON",

                beforeSend: function() {

                    $('#btnBrowse').html(
                        '<i class="fa fa-spinner fa-spin"></i> Loading...'
                    );

                },

                success: function(response) {

                    tblMesin.clear();
                    tblMesin.rows.add(response);
                    tblMesin.draw();

                },

                complete: function() {

                    $('#btnBrowse').html(
                        '<i class="fa fa-search"></i> Browse'
                    );

                }

            });

        });

        // DETAIL TABLE
        tblDetail = $('#tblDetail').DataTable({

            processing: true,

            searching: true,
            paging: true,
            ordering: true,

            autoWidth: false,
            responsive: false,

            // scrollX: true,
            scrollCollapse: true,

            pageLength: 10,
            // dom: 'Bfrtip',
            dom: "<'row'<'col-sm-6'l><'col-sm-6 text-right'B>>" +
                "rt" +
                "<'row'<'col-sm-6'i><'col-sm-6'p>>",
            buttons: [

                {
                    text: '<i class="fa fa-file-excel-o"></i> &nbsp; Excel',
                    extend: 'excelHtml5',
                    className: 'btn btn-success btn-sm',
                },

                {
                    text: '<i class="fa fa-file-pdf-o"></i> &nbsp; PDF',
                    extend: 'pdfHtml5',
                    className: 'btn btn-danger btn-sm',
                },

                {
                    text: '<i class="fa fa-refresh"></i> Refresh',
                    className: 'btn btn-primary btn-sm',

                    action: function() {

                        var thn = $('#thn').val();
                        var bln = $('#bln').val();

                        if (selectedMesin != '') {

                            loadDetailProduksi(
                                thn,
                                bln,
                                selectedDept,
                                selectedMesin
                            );

                        }

                    }
                },

                // {
                //     extend: 'copy',
                //     className: 'btn btn-dark btn-sm'
                // },

                {
                    text: '<i class="fa fa-print"></i> &nbsp; Print',
                    extend: 'print',
                    className: 'btn btn-dark btn-sm'
                }

            ],

            columns: [

                {
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
                    className: 'text-right angka-target'
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

            ]

        });

        // FILTER TABLE DETAIL
        $('#tblDetail tfoot th').each(function() {

            var title = $(this).text();

            $(this).html(
                '<input type="text" ' +
                'class="form-control input-sm" ' +
                'placeholder="Search ' + title + '" />'
            );

        });

        // FILTER TABLE DETAIL
        $('#tblDetail tfoot').insertAfter($('#tblDetail thead'));

        tblDetail.columns().every(function() {

            var that = this;

            $('input', this.footer()).on('keyup change clear', function() {

                if (that.search() !== this.value) {

                    that.search(this.value).draw();

                }

            });

        });

        // KLIK ROW MESIN
        $('#tblDetail tfoot tr').appendTo('#tblDetail thead');

        // KLIK ROW MESIN UNTUK MENAMPILKAN DETAIL
        $('#tblMesin tbody').on('click', 'tr', function() {

            $('#tblMesin tbody tr').removeClass('selected');
            $(this).addClass('selected');

            var data = tblMesin.row(this).data();

            var thn = $('#thn').val();
            var bln = $('#bln').val();

            var dept = data.NAMA_DEPARTEMEN;
            var mesin = data.MESIN;

            // simpan global
            selectedDept = dept;
            selectedMesin = mesin;

            $('#infoMesin').html(
                'Departemen : <b>' + dept + '</b> &nbsp; | &nbsp; Mesin : <b>' + mesin + '</b>'
            );

            loadDetailProduksi(thn, bln, dept, mesin);

        });

        // FILTER TABLE DETAIL
        tblDetail.columns().every(function() {

            var that = this;

            $('input', this.footer()).on('keyup change clear', function() {

                if (that.search() !== this.value) {

                    that.search(this.value).draw();

                }

            });

        });

        // KLIK ROW MESIN
        $('#tblMesin tbody').on('click', 'tr', function() {

            $('#tblMesin tbody tr').removeClass('selected');

            $(this).addClass('selected');

        });

        function formatNumberOEE(data) {
            if (data === null || data === undefined || data === '') {
                return '0';
            }

            var num = parseFloat(data);

            if (isNaN(num)) {
                return data;
            }

            // maksimal 2 angka desimal, buang nol belakang
            return num.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

    });
</script>