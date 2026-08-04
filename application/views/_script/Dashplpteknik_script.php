<script>
    var base_url = '<?= base_url(); ?>';
</script>

<script>
    $(function() {

        var page = $('#page-dashplpteknik');
        if (page.length === 0) return;

        var chartTopMesin = null;
        var chartJenisPekerjaan = null;
        var chartTrenHarian = null;
        var chartWaktuProses = null;

        function disposeCharts() {
            [chartTopMesin, chartJenisPekerjaan, chartTrenHarian, chartWaktuProses].forEach(function(c) {
                if (c) {
                    c.dispose();
                }
            });

            chartTopMesin = null;
            chartJenisPekerjaan = null;
            chartTrenHarian = null;
            chartWaktuProses = null;
        }

        function renderTopMesin(data) {
            var dom = page.find('#chartTopMesin')[0];
            chartTopMesin = echarts.init(dom);

            chartTopMesin.setOption({
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '18%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: data.kategori,
                    axisLabel: {
                        interval: 0,
                        rotate: 30
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Jumlah PLP'
                },
                series: [{
                    type: 'bar',
                    data: data.nilai,
                    itemStyle: {
                        color: '#1ABB9C'
                    },
                    label: {
                        show: true,
                        position: 'top'
                    }
                }]
            });
        }

        function renderJenisPekerjaan(data) {
            var dom = page.find('#chartJenisPekerjaan')[0];
            chartJenisPekerjaan = echarts.init(dom);

            chartJenisPekerjaan.setOption({
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}: {c} ({d}%)'
                },
                legend: {
                    bottom: 0,
                    type: 'scroll'
                },
                series: [{
                    type: 'pie',
                    radius: ['35%', '65%'],
                    data: data,
                    label: {
                        formatter: '{b}\n{d}%'
                    }
                }]
            });
        }

        function renderTrenHarian(data, thn, bln) {
            var dom = page.find('#chartTrenHarian')[0];
            chartTrenHarian = echarts.init(dom);

            var blnPad = String(bln).padStart(2, '0');

            chartTrenHarian.setOption({
                tooltip: {
                    trigger: 'axis',
                    formatter: function(params) {
                        var p = params[0];
                        var tgl = String(p.axisValue).padStart(2, '0');

                        return 'Tgl ' + tgl + '/' + blnPad + '/' + thn +
                            '<br/>Jml PLP : ' + p.data;
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '10%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: data.kategori,
                    name: 'Tanggal'
                },
                yAxis: {
                    type: 'value',
                    name: 'Jumlah PLP'
                },
                series: [{
                    type: 'line',
                    data: data.nilai,
                    smooth: true,
                    areaStyle: {},
                    itemStyle: {
                        color: '#3498db'
                    }
                }]
            });
        }

        function renderWaktuProses(data) {
            var dom = page.find('#chartWaktuProses')[0];
            chartWaktuProses = echarts.init(dom);

            chartWaktuProses.setOption({
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '10%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value',
                    name: 'Jam'
                },
                yAxis: {
                    type: 'category',
                    data: data.kategori,
                    inverse: true
                },
                series: [{
                    type: 'bar',
                    data: data.nilai,
                    itemStyle: {
                        color: '#e74c3c'
                    },
                    label: {
                        show: true,
                        position: 'right',
                        formatter: '{c} jam'
                    }
                }]
            });
        }

        function loadDashboard() {
            var thn = page.find('#thn').val();
            var bln = page.find('#bln').val();

            if (!thn || !bln) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Tahun dan Bulan wajib dipilih.'
                });
                return;
            }

            $.ajax({
                url: base_url + 'Dashplpteknik/get_dashboard',
                type: 'POST',
                dataType: 'json',
                data: {
                    thn: thn,
                    bln: bln
                },
                beforeSend: function() {
                    page.find('#loadingDashboard').show();

                    page.find('#btnBrowseDashplpteknik').prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Loading...'
                    );
                },
                success: function(res) {
                    if (res.error) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Belum Lengkap',
                            text: res.error
                        });
                        return;
                    }

                    disposeCharts();

                    renderTopMesin(res.top_mesin);
                    renderJenisPekerjaan(res.jenis_pekerjaan);
                    renderTrenHarian(res.tren_harian, thn, bln);
                    renderWaktuProses(res.top_waktu_proses);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Dashboard',
                        text: 'Terjadi kesalahan saat mengambil data dashboard.'
                    });
                    console.log(xhr.responseText);
                },
                complete: function() {
                    page.find('#loadingDashboard').hide();

                    page.find('#btnBrowseDashplpteknik').prop('disabled', false).html(
                        '<i class="fa fa-search"></i> Browse'
                    );
                }
            });
        }

        $(window).on('resize', function() {
            [chartTopMesin, chartJenisPekerjaan, chartTrenHarian, chartWaktuProses].forEach(function(c) {
                if (c) c.resize();
            });
        });

        page.find('#btnBrowseDashplpteknik').on('click', function() {
            loadDashboard();
        });

        // Load pertama kali saat halaman dibuka
        loadDashboard();

    });
</script>