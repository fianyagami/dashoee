<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    $(document).ready(function() {
        var page = $('#page-dashoeekk');
        if (page.length === 0) return;

        // DROPDOWN MESIN
        $('#mesin').select2({
            placeholder: 'Pilih Mesin',
            ajax: {
                url: '<?= site_url("Dashoeekk/getMesin") ?>',
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
                url: '<?= site_url("Dashoeekk/getKK") ?>',
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

        // KLIK BUTTON BROWSE
        $('#btnBrowse').click(function() {
            loadDashboard();
        });

        function loadDashboard() {
            let mesinData = $('#mesin').select2('data')[0];
            let kkData = $('#nomor_kk').select2('data')[0];

            if (!mesinData || !kkData) {
                alert('Mesin dan Nomor KK wajib dipilih.');
                return;
            }

            console.log('tahun:', $('#tahun').val());
            console.log('bulan:', $('#bulan').val());
            console.log('kdmesin:', mesinData.kdmesin);
            console.log('nomor_kk:', kkData.nomor_kk);
            console.log('tanggal_kk:', kkData.tanggal_kk);

            $.ajax({
                url: '<?= site_url("Dashoeekk/getDashboard") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    tahun: $('#tahun').val(),
                    bulan: $('#bulan').val(),
                    kdmesin: mesinData.kdmesin,
                    nomor_kk: kkData.nomor_kk,
                    tanggal_kk: kkData.tanggal_kk
                },
                beforeSend: function() {
                    page.find('#loadingDashboardKK').show();

                    page.find('#btnBrowseMonprod').prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Loading...'
                    );

                    // tblDetail.clear().draw();
                },
                success: function(res) {
                    let s = res.summary;

                    $('#dashboardTitle').html(
                        '<div class="dashboard-title-main">Dashboard OEE - ' + mesinData.mesin + '</div>' +
                        '<div class="dashboard-title-sub">' + kkData.nomor_kk + ' - ' + kkData.nama_barang + '</div>'
                    );

                    renderGauge('chartAR', 'Availability', parseFloat(s.AR), parseFloat(s.TARGET_AR));
                    renderGauge('chartPR', 'Performance', parseFloat(s.PR), parseFloat(s.TARGET_PR));
                    renderGauge('chartQR', 'Quality', parseFloat(s.QR), parseFloat(s.TARGET_QR));

                    renderBarHorizontal('chartDowntime', 'Top 5 Downtime', res.downtime, 'KEGIATAN', 'PERSEN', '%');
                    renderBarHorizontal('chartDefect', 'Top 5 Defect', res.defect, 'KEGIATAN', 'JUMLAH', '');
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

                    page.find('#btnBrowseMonprod').prop('disabled', false).html(
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
            let chart = echarts.init(document.getElementById(id));

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
                        fontSize: 24
                    },
                    data: [{
                        value: value || 0,
                        name: 'Target ' + target + '%'
                    }]
                }]
            });

            window.addEventListener('resize', function() {
                chart.resize();
            });
        }

        function renderBarHorizontal(id, title, data, labelField, valueField, suffix) {
            let chart = echarts.init(document.getElementById(id));

            let labels = [];
            let values = [];

            data.forEach(function(row) {
                labels.push(row[labelField]);
                values.push(parseFloat(row[valueField] || 0));
            });

            labels = labels.reverse();
            values = values.reverse();

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
                    data: labels
                },
                series: [{
                    type: 'bar',
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

            chart.resize();
        }

        function renderActualTarget(data) {
            let chart = echarts.init(document.getElementById('chartActualTarget'));

            chart.setOption({
                title: {
                    text: 'Actual / Target',
                    left: 'center',
                    top: 5
                },
                tooltip: {},
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
                            value: parseFloat(data.ACTUAL_OUTPUT || 0),
                            itemStyle: {
                                color: '#2ECC71'
                            }
                        },
                        {
                            value: parseFloat(data.TARGET_OUTPUT || 0),
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

            window.addEventListener('resize', function() {
                chart.resize();
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

    });
</script>