<script>
    $(document).ready(function() {

        const page = $('#page-daftarkk');

        let tblKKhead = null;
        let tblKKdetail = null;

        function formatNumberNolkoma(data) {
            if (data === null || data === undefined || data === '') return '0';

            var num = parseFloat(data);
            if (isNaN(num)) return data;

            return num.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        initTable();
        initColumnFilter(tblKKhead, '#tblKKhead');
        initColumnFilter(tblKKdetail, '#tblKKdetail');

        // BUTTON BROWSE KLIK
        page.on('click', '#btnBrowseDaftarkk', function() {
            loadHeadKK();
        });

        // KLIK TABEL KK HEADER
        page.on('click', '#tblKKhead tbody tr', function() {
            const data = tblKKhead.row(this).data();

            if (!data) {
                return;
            }

            $('#tblKKhead tbody tr').removeClass('row-selected');
            $(this).addClass('row-selected');

            const thn = $('#filter_thn').val();
            const nokk = data.NOMOR_KK;
            const nmbarang = data.NAMA_BARANG;
            const tgl_kk = data.TANGGAL_KK_PARAM;

            page.find('#infoKK').html(
                'KK :(<b>' + nokk + '</b>) | <b>' + nmbarang + '</b>'
            );

            loadDetailKK(thn, nokk, tgl_kk);
        });

        // INISIALISASI TABEL
        function initTable() {
            tblKKhead = $('#tblKKhead').DataTable({
                data: [],
                destroy: true,
                processing: true,

                paging: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],

                ordering: true,
                order: [
                    [4, 'desc']
                ],
                orderCellsTop: true,
                responsive: false,
                autoWidth: false,
                scrollX: true,

                dom: "<'row'<'col-sm-6'l><'col-sm-6 text-right'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                buttons: [{
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Dafar KK'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf-o"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Dafar KK',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        text: '<i class="fa fa-refresh"></i> Refresh',
                        className: 'btn btn-primary btn-sm',
                        action: function() {
                            loadHeadKK();
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        className: 'btn btn-dark btn-sm'
                    }
                ],
                columns: [{
                        data: 'NOMOR_KK'
                    },
                    {
                        data: 'NO_BAPOB'
                    },
                    {
                        data: 'NOMER_PO_CUSTOMER',
                        className: 'wordwrap'
                    },
                    {
                        data: 'STAT_KK'
                    },
                    // {
                    //     data: 'TANGGAL_KK',
                    //     render: function(data) {
                    //         return formatDateDisplay(data);
                    //     }
                    // },
                    {
                        data: 'TANGGAL_KK',
                        className: 'wordwrap'
                    },
                    {
                        data: 'CUSTOMER',
                        className: 'wordwrap'
                    },
                    {
                        data: 'NAMA_BARANG',
                        className: 'wordwrap'
                    },
                    {
                        data: 'NAMA_KATEGORI',
                        className: 'wordwrap'
                    },
                    {
                        data: 'OPLAAG_PO',
                        className: 'text-right',
                        render: $.fn.dataTable.render.number('.', ',', 0, '')
                    },
                    {
                        data: 'SATUAN_OPLAAG'
                    },
                    {
                        data: 'ARSIP_STATUS'
                    },
                    {
                        data: 'NAMA',
                        className: 'wordwrap'
                    },
                    {
                        data: 'NAMA2',
                        className: 'wordwrap'
                    }
                ]
            });

            tblKKdetail = $('#tblKKdetail').DataTable({
                data: [],
                destroy: true,
                processing: true,

                paging: true,
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],

                ordering: true,
                order: [
                    [0, 'asc']
                ],
                orderCellsTop: true,
                responsive: false,
                autoWidth: false,
                scrollX: true,

                dom: "<'row'<'col-sm-6'l><'col-sm-6 text-right'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                buttons: [{
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Daftar KK Produksi Detail'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf-o"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Daftar KK Produksi Detail',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    // {
                    //     text: '<i class="fa fa-refresh"></i> Refresh',
                    //     className: 'btn btn-primary btn-sm',
                    //     action: function() {
                    //         loadData();
                    //     }
                    // },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        className: 'btn btn-dark btn-sm'
                    }
                ],
                columns: [
                    // {
                    //     data: 'URUT',
                    //     className: 'text-center'
                    // },
                    {
                        data: 'URUT_FLOW',
                        className: 'text-center'
                    },
                    {
                        data: 'NAMA_PROSES',
                        className: 'wordwrap'
                    },
                    {
                        data: 'NAMA_MESIN',
                        className: 'wordwrap'
                    },
                    {
                        data: 'WASTE_PROSES',
                        className: 'text-right',
                        render: formatNumberNolkoma
                    },
                    {
                        data: 'TARGET',
                        className: 'text-right',
                        render: $.fn.dataTable.render.number('.', ',', 0, '')
                    },
                    {
                        data: 'SAT_TARGET'
                    },
                    {
                        data: 'JENIS_BAHAN',
                        className: 'wordwrap kol-bahan'
                    },
                    {
                        data: 'NAMA_BARANG',
                        className: 'wordwrap kol-bahan'
                    },
                    {
                        data: 'JUMLAH_OR_UKURAN',
                        className: 'wordwrap kol-bahan'
                    },
                    {
                        data: 'NAMA_HASIL',
                        className: 'wordwrap'
                    }
                ]
            });
        }

        function initColumnFilter(table, tableId) {
            $(tableId).closest('.dataTables_wrapper')
                .on('click', 'thead tr.filter-row input', function(e) {
                    e.stopPropagation();
                });

            $(tableId).closest('.dataTables_wrapper')
                .on('keyup change', 'thead tr.filter-row input', function(e) {
                    e.stopPropagation();

                    let colIndex = $(this).closest('th').index();

                    table
                        .column(colIndex)
                        .search(this.value)
                        .draw();
                });
        }

        function loadHeadKK() {
            const thn = $('#filter_thn').val();

            showLoading();

            tblKKhead.clear().draw();
            tblKKdetail.clear().draw();

            $.ajax({
                url: "<?= base_url('daftarkk/get_daftarkk_head') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    thn: thn
                },
                success: function(res) {
                    tblKKhead.clear().rows.add(res).draw();

                    setTimeout(function() {
                        tblKKhead.columns.adjust().draw(false);
                    }, 200);
                },
                error: function(xhr, status, error) {
                    alert('Gagal mengambil data KK Header: ' + error);
                    console.log(xhr.responseText);
                },
                complete: function() {
                    hideLoading();
                }
            });
        }

        function loadDetailKK(thn, nokk, tgl_kk) {
            showLoading();

            tblKKdetail.clear().draw();

            $.ajax({
                url: "<?= base_url('daftarkk/get_daftarkk_detail') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    thn: thn,
                    nokk: nokk,
                    tgl_kk: formatDateParam(tgl_kk)
                },
                success: function(res) {
                    tblKKdetail.clear().rows.add(res).draw();
                },
                error: function(xhr, status, error) {
                    alert('Gagal mengambil data KK Detail: ' + error);
                    console.log(xhr.responseText);
                },
                complete: function() {
                    hideLoading();
                }
            });
        }

        function showLoading() {
            $('#panel_loading').show();
            $('#btnBrowseDaftarkk').prop('disabled', true);
        }

        function hideLoading() {
            $('#panel_loading').hide();
            $('#btnBrowseDaftarkk').prop('disabled', false);
        }

        function formatDateDisplay(value) {
            if (!value) {
                return '';
            }

            const dt = new Date(value);

            const dd = String(dt.getDate()).padStart(2, '0');
            const mm = String(dt.getMonth() + 1).padStart(2, '0');
            const yyyy = dt.getFullYear();

            return dd + '/' + mm + '/' + yyyy;
        }

        function formatDateParam(value) {
            if (!value) {
                return '';
            }

            let dateStr = value;

            if (typeof value === 'object' && value.date) {
                dateStr = value.date;
            }

            return String(dateStr).substring(0, 10);
        }

    });
</script>