<script>
    $(document).ready(function() {

        const page = $('#page-kompbapobkk');

        let tblKK = null;
        let tblBapob = null;

        initSelect2KK();
        initSelect2BAPOB();
        initTableKK();
        initTableBAPOB();

        $('#thn_kk').on('change', function() {
            $('#select_kk').val(null).trigger('change');
            $('#info_kk_bapob').text('NO BAPOB: -');
            $('#info_kk_barang').text('BARANG: -');
            tblKK.clear().draw();
        });

        $('#thn_bapob').on('change', function() {
            $('#select_bapob').val(null).trigger('change');
            $('#select_bapob').empty();

            $('#info_bapob_produk').text('PRODUK: -');
            $('#info_bapob_dibuat').text('DIBUAT: -');

            tblBapob.clear().draw();
        });

        // User mulai mengetik No BAPOB secara manual -> sembunyikan notifikasi "tidak ditemukan"
        $('#select_bapob').on('select2:opening select2:select', function() {
            page.find('#bapobNotFoundAlert').hide();
        });

        $('#select_kk').on('select2:select', function(e) {
            let data = e.params.data;

            $('#info_kk_bapob').text(
                'NO BAPOB: ' + safeText(data.NO_BAPOB) +
                ' | TGL BAPOB: ' + safeText(data.TANGGAL_BAPOB)
            );

            $('#info_kk_barang').text('BARANG: ' + safeText(data.NAMA_BARANG));

            loadProsesKK(data);

            autoLoadBAPOBFromKK(data);
        });

        $('#select_bapob').on('select2:select', function(e) {
            let data = e.params.data;

            $('#info_bapob_produk').text('PRODUK: ' + safeText(data.PRODUK));
            $('#info_bapob_dibuat').text(
                'DIBUAT: ' + safeText(data.DIBUAT) +
                ' | TGL BAPOB: ' + safeText(data.TANGGAL_BAPOB)
            );

            loadProsesBAPOB(data);
        });

        function initSelect2KK() {
            $('#select_kk').select2({
                placeholder: 'Pilih KK',
                allowClear: true,
                ajax: {
                    url: "<?= base_url('Kompbapobkk/get_header_kk') ?>",
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


        function initSelect2BAPOB() {
            $('#select_bapob').select2({
                placeholder: 'Ketik minimal 3 karakter No BAPOB / Produk',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: "<?= base_url('Kompbapobkk/get_header_bapob') ?>",
                    type: "GET",
                    dataType: "json",
                    delay: 500,
                    cache: true,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            thn: $('#thn_bapob').val()
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || []
                        };
                    },
                    beforeSend: function() {
                        $('#info_bapob_produk').text('PRODUK: loading...');
                        $('#info_bapob_dibuat').text('DIBUAT: loading...');
                    }
                },
                language: {
                    inputTooShort: function() {
                        return 'Ketik minimal 3 karakter';
                    },
                    searching: function() {
                        return 'Mencari data BAPOB...';
                    },
                    noResults: function() {
                        return 'Data tidak ditemukan';
                    }
                },
                templateResult: function(data) {
                    if (data.loading) return data.text;

                    return $(
                        '<div>' +
                        '<strong>' + safeText(data.NO_BAPOB) + '</strong><br>' +
                        '<small>' + safeText(data.PRODUK) + '</small>' +
                        '</div>'
                    );
                },
                templateSelection: function(data) {
                    return data.text || data.NO_BAPOB;
                }
            });
        }

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
                        targets: 0, // URUT
                        width: "20px",
                        className: "text-center"
                    },
                    {
                        targets: 1, // URUT_FLOW
                        width: "20px",
                        className: "text-center"
                    },
                    {
                        targets: 5, // TARGET
                        width: "50px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#e01414'
                            });
                        }
                    }
                ]
            });
        }

        function initTableBAPOB() {
            tblBapob = $('#tblbapob').DataTable({
                data: [],
                destroy: true,
                processing: true,
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                scrollX: true,
                columns: [{
                        data: 'URUT_SUB',
                        visible: false
                    },
                    {
                        data: 'NAMA_SUB',
                        className: 'wordwrap'
                    },
                    {
                        data: 'URUTAN_PROSES',
                        className: 'text-center'
                    },
                    {
                        data: 'NAMA_PROSES',
                        className: 'wordwrap'
                    },
                    {
                        data: 'NAMA_MESIN_S',
                        className: 'wordwrap'
                    },
                    {
                        data: 'TARGET_SPEED_S',
                        className: 'wordwrap text-right'
                    }
                ],
                order: [
                    [0, 'asc'],
                    [2, 'asc']
                ],
                columnDefs: [{
                        targets: 2, // URUTAN_PROSES
                        width: "20px",
                        className: "text-center"
                    },
                    {
                        targets: 5, // TARGET_SPEED_S
                        width: "50px",
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'font-weight': 'bold',
                                'color': '#e01414'
                            });
                        }
                    }
                ]
            });
        }

        function loadProsesKK(dataKK) {
            $.ajax({
                url: "<?= base_url('Kompbapobkk/get_proses_kk') ?>",
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

        function loadProsesBAPOB(dataBAPOB) {
            $.ajax({
                url: "<?= base_url('Kompbapobkk/get_proses_bapob') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    no_bapob: dataBAPOB.NO_BAPOB,
                    dibuat: dataBAPOB.DIBUAT,
                    kode_transaksi: dataBAPOB.KODE_TRANSAKSI,
                    tanggal_bapob: dataBAPOB.TANGGAL_BAPOB
                },
                beforeSend: function() {
                    tblBapob.clear().draw();
                },
                success: function(res) {
                    tblBapob.clear().rows.add(res.data || []).draw();
                    tblBapob.columns.adjust();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Gagal mengambil proses BAPOB');
                }
            });
        }

        function autoLoadBAPOBFromKK(dataKK) {
            if (!dataKK.NO_BAPOB || !dataKK.TAHUN_BAPOB) {
                tblBapob.clear().draw();

                $('#info_bapob_produk').text('PRODUK: -');
                $('#info_bapob_dibuat').text('DIBUAT: -');

                $('#select_bapob').val(null).trigger('change');
                return;
            }

            $('#thn_bapob').val(dataKK.TAHUN_BAPOB).trigger('change');

            $.ajax({
                url: "<?= base_url('Kompbapobkk/get_header_bapob') ?>",
                type: "GET",
                dataType: "json",
                data: {
                    thn: dataKK.TAHUN_BAPOB,
                    tgl_bapob: dataKK.TANGGAL_BAPOB,
                    q: dataKK.NO_BAPOB,
                    customer: dataKK.CUSTOMER

                },
                beforeSend: function() {
                    page.find('#loadingDetail').show();
                    page.find('#bapobNotFoundAlert').hide();
                },
                success: function(res) {
                    let results = res.results || [];

                    if (results.length === 0) {
                        tblBapob.clear().draw();

                        $('#select_bapob').empty().val(null).trigger('change');
                        $('#info_bapob_produk').text('PRODUK: tidak ditemukan');
                        $('#info_bapob_dibuat').text('DIBUAT: -');

                        // Tahun BAPOB tetap diisi dari KK (jika ada) supaya user
                        // tinggal mengetik No BAPOB / Produk secara manual di dropdown.
                        if (dataKK.TAHUN_BAPOB) {
                            $('#thn_bapob').val(dataKK.TAHUN_BAPOB);
                        }

                        page.find('#bapobNotFoundAlert').show();
                        $('#select_bapob').select2('open');

                        return;
                    }

                    // let bapob = results.find(function(item) {
                    //     return item.NO_BAPOB == dataKK.NO_BAPOB &&
                    //         item.TANGGAL_BAPOB == dataKK.TANGGAL_BAPOB;
                    // });

                    // if (!bapob) {
                    //     bapob = results[0];
                    // }

                    let bapob = null;

                    let kkCustomer = normalizeCustomer(dataKK.CUSTOMER);

                    let kandidat = results.filter(function(item) {
                        return item.NO_BAPOB == dataKK.NO_BAPOB &&
                            item.TANGGAL_BAPOB == dataKK.TANGGAL_BAPOB;
                    });

                    if (kandidat.length === 1) {

                        bapob = kandidat[0];

                    } else if (kandidat.length > 1) {

                        let matchByCustomer = kandidat.find(function(item) {

                            let bapobCustomer = normalizeCustomer(item.CUSTOMER);

                            return bapobCustomer === kkCustomer ||
                                bapobCustomer.includes(kkCustomer) ||
                                kkCustomer.includes(bapobCustomer);

                        });

                        if (matchByCustomer) {

                            bapob = matchByCustomer;

                        } else {

                            bapob = kandidat[0];

                            console.warn(
                                'Terdapat lebih dari satu BAPOB dengan NO dan TANGGAL yang sama'
                            );
                        }

                    } else {

                        bapob = results[0];

                    }

                    bapob.TANGGAL_BAPOB = dataKK.TANGGAL_BAPOB;

                    page.find('#bapobNotFoundAlert').hide();

                    let optionText = bapob.NO_BAPOB + ' - ' + bapob.PRODUK;
                    let newOption = new Option(optionText, bapob.NO_BAPOB, true, true);

                    $('#select_bapob').empty().append(newOption).trigger('change');

                    $('#select_bapob').trigger({
                        type: 'select2:select',
                        params: {
                            data: bapob
                        }
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Gagal mengambil data BAPOB dari KK');
                },
                complete: function() {
                    page.find('#loadingDetail').hide();
                }
            });
        }

        function safeText(value) {
            if (value === null || value === undefined) {
                return '-';
            }

            return String(value);
        }

        function normalizeCustomer(value) {
            if (!value) return '';

            return String(value)
                .toUpperCase()
                .replace(/\./g, ' ')
                .replace(/,/g, ' ')
                .replace(/\bCV\b/g, '')
                .replace(/\bPT\b/g, '')
                .replace(/\bUD\b/g, '')
                .replace(/\bPD\b/g, '')
                .replace(/\bTBK\b/g, '')
                .replace(/\s+/g, ' ')
                .trim();
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