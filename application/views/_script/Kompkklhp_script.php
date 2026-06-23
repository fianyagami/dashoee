<script>
    $(document).ready(function() {

        const page = $('#page-kompkklhp');

        let tblKK = null;
        let tblLHP = null;

        initSelect2KK();
        initTableKK();
        initTableLHP();

        $('#thn_kk').on('change', function() {
            $('#select_kk').val(null).trigger('change');
            $('#info_kk_barang').text('PRODUK: -');
            $('#info_kk_customer').text('CUSTOMER: -');
            tblKK.clear().draw();
            resetPanelLHP();
        });

        $('#select_kk').on('select2:select', function(e) {
            let data = e.params.data;

            $('#info_kk_barang').text('PRODUK: ' + safeText(data.NAMA_BARANG));
            $('#info_kk_customer').text('CUSTOMER: ' + safeText(data.CUSTOMER));

            loadProsesKK(data);
            loadProsesLHP(data);
        });

        $('#select_kk').on('select2:clear', function() {
            $('#info_kk_barang').text('PRODUK: -');
            $('#info_kk_customer').text('CUSTOMER: -');
            tblKK.clear().draw();
            resetPanelLHP();
        });

        // -------------------------------------------------------
        // SELECT2 KK
        // -------------------------------------------------------
        function initSelect2KK() {
            $('#select_kk').select2({
                placeholder: 'Pilih KK',
                allowClear: true,
                ajax: {
                    url: "<?= base_url('Kompkklhp/get_header_kk') ?>",
                    type: "GET",
                    dataType: "json",
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term,
                            thn: $('#thn_kk').val()
                        };
                    },
                    processResults: function(data) {
                        return data;
                    }
                },
                templateResult: function(data) {
                    if (data.loading) return data.text;

                    return $(
                        '<div>' +
                        '<strong>' + safeText(data.NOMOR_KK) + '</strong><br>' +
                        '<small>' + safeText(data.NAMA_BARANG) + '</small>' +
                        '</div>'
                    );
                },
                templateSelection: function(data) {
                    return data.text || data.NOMOR_KK;
                }
            });
        }

        // -------------------------------------------------------
        // TABLE KK
        // -------------------------------------------------------
        function initTableKK() {
            tblKK = $('#tblkk').DataTable({
                data: [],
                destroy: true,
                processing: true,
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                scrollX: false,
                columns: [{
                        data: 'URUT',
                        className: 'text-center'
                    },
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
                        render: function(data) {
                            return formatNumber(data);
                        }
                    },
                    {
                        data: 'TARGET',
                        className: 'text-right',
                        render: function(data) {
                            return formatNumber(data);
                        }
                    },
                    {
                        data: 'SAT_TARGET',
                        className: 'text-center'
                    }
                ],
                order: [
                    [0, 'asc'],
                    [1, 'asc']
                ],
                columnDefs: [{
                        targets: 0,
                        width: "20px",
                        className: "text-center"
                    },
                    {
                        targets: 1,
                        width: "20px",
                        className: "text-center"
                    },
                    {
                        targets: 5,
                        width: "50px",
                        createdCell: function(td) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#e01414'
                            });
                        }
                    }
                ]
            });
        }

        // -------------------------------------------------------
        // TABLE LHP
        // -------------------------------------------------------
        function initTableLHP() {
            tblLHP = $('#tbllhp').DataTable({
                data: [],
                destroy: true,
                processing: true,
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                scrollX: false,
                columns: [{
                        data: 'URUT_PROSES',
                        className: 'text-center'
                    },
                    {
                        data: 'PROSES',
                        className: 'wordwrap'
                    },
                    {
                        data: 'TOT_WAKTU',
                        className: 'text-right',
                        render: function(data) {
                            return formatNumber(data);
                        }
                    },
                    {
                        data: 'TOT_BAIK',
                        className: 'text-right',
                        render: function(data) {
                            return formatNumber(data);
                        }
                    },
                    {
                        data: 'TOT_RUSAK',
                        className: 'text-right',
                        render: function(data) {
                            return formatNumber(data);
                        }
                    }
                ],
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                        targets: 0,
                        width: "30px",
                        className: "text-center"
                    },
                    // {
                    //     targets: [2, 3, 4],
                    //     width: "60px",
                    //     createdCell: function(td, cellData, rowData, row, col) {
                    //         if (col === 4 && parseFloat(cellData) > 0) {
                    //             $(td).css({
                    //                 'font-weight': 'bold',
                    //                 'color': '#e01414'
                    //             });
                    //         }
                    //     }
                    // },
                    {
                        targets: 2, //TOT_WAKTU//
                        width: "60px",
                        createdCell: function(td) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#1118df'
                            });
                        }
                    },
                    {
                        targets: 3, //TOT_BAIK//
                        width: "60px",
                        createdCell: function(td) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#048220'
                            });
                        }
                    },
                    {
                        targets: 4, //TOT_RUSAK//
                        width: "60px",
                        createdCell: function(td) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#e01414'
                            });
                        }
                    }
                ]
            });
        }

        // -------------------------------------------------------
        // LOAD PROSES KK
        // -------------------------------------------------------
        function loadProsesKK(dataKK) {
            $.ajax({
                url: "<?= base_url('Kompkklhp/get_proses_kk') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    nomor_kk: dataKK.NOMOR_KK,
                    tahun: $('#thn_kk').val(),
                    tanggal_kk: dataKK.TANGGAL_KK
                },
                beforeSend: function() {
                    tblKK.clear().draw();
                },
                success: function(res) {
                    tblKK.clear().rows.add(res.data || []).draw();
                    tblKK.columns.adjust();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Gagal mengambil proses KK');
                }
            });
        }

        // -------------------------------------------------------
        // LOAD PROSES LHP (otomatis dari pilihan KK)
        // -------------------------------------------------------
        function loadProsesLHP(dataKK) {
            // Update info panel LHP
            $('#info_lhp_nomor').text('NOMOR KK: ' + safeText(dataKK.NOMOR_KK) +
                ' | TGL: ' + safeText(dataKK.TANGGAL_KK));
            $('#info_lhp_barang').text('PRODUK: ' + safeText(dataKK.NAMA_BARANG));
            $('#info_lhp_status').text(
                'STATUS: ' + safeText(dataKK.STATUS_KK) +
                ' | REVISI KE: ' + safeText(dataKK.REVISI_KE)
            );

            $.ajax({
                url: "<?= base_url('Kompkklhp/get_proses_lhp') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    nomor_kk: dataKK.NOMOR_KK,
                    tanggal_kk: dataKK.TANGGAL_KK,
                    revisi_ke: dataKK.REVISI_KE,
                    status_kk: dataKK.STATUS_KK
                },
                beforeSend: function() {
                    tblLHP.clear().draw();
                    page.find('#loadingDetail').show();
                },
                success: function(res) {
                    tblLHP.clear().rows.add(res.data || []).draw();
                    tblLHP.columns.adjust();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Gagal mengambil data LHP');
                },
                complete: function() {
                    page.find('#loadingDetail').hide();
                }
            });
        }

        // -------------------------------------------------------
        // HELPERS
        // -------------------------------------------------------
        function resetPanelLHP() {
            tblLHP.clear().draw();
            $('#info_lhp_nomor').text('NOMOR KK: -');
            $('#info_lhp_barang').text('PRODUK: -');
            $('#info_lhp_status').text('STATUS: -');
        }

        function safeText(value) {
            if (value === null || value === undefined) return '-';
            return String(value);
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

    });
</script>