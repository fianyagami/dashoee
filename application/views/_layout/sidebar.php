 <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
     <div class="menu_section">
         <h3>Program TSS</h3>
         <ul class="nav side-menu">
             <li><a><i class="fa fa-dashboard"></i> Dashboard <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
                     <li><a href="<?= base_url(); ?>core/error">-</a></li>
                     <!-- <li><a href="<?= base_url(); ?>core/error">Dashboard Utama</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Dashboard Realtime</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Dashboard Shift</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Dashboard Mesin</a></li> -->
                 </ul>
             </li>
             <li><a><i class="fa fa-line-chart"></i> Monitoring OEE <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
                     <li><a href="<?= base_url(); ?>dashoeeunit" onclick="logModule('OEE per Unit')">OEE per Unit</a></li>
                     <li><a href="<?= base_url(); ?>dashoeedept" onclick="logModule('OEE per Dept')">OEE per Dept</a></li>
                     <li><a href="<?= base_url(); ?>dashoeeweek" onclick="logModule('OEE per Week')">OEE per Week</a></li>
                     <li><a href="<?= base_url(); ?>dashoeekk" onclick="logModule('OEE per KK')">OEE per KK</a></li>
                     <li><a href="<?= base_url(); ?>dashoeemesinkk" onclick="logModule('OEE per Mesin & KK')">OEE per Mesin & KK</a></li>
                     <li><a href="<?= base_url(); ?>core/error">-</a></li>
                     <!-- <li><a href="<?= base_url(); ?>core/error">Availability</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Performance</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Quality</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Analisa OEE</a></li> -->
                 </ul>
             </li>
             <li><a><i class="fa fa-paper-plane"></i> PPIC<span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
                     <li><a href="<?= base_url(); ?>daftarkk" onclick="logModule('Daftar KK')">Daftar KK</a></li>
                     <li><a href="<?= base_url(); ?>kompbapobkk" onclick="logModule('Komparasi BAPOB & KK')">Komparasi BAPOB & KK</a></li>
                     <li><a href="<?= base_url(); ?>kompkklhp" onclick="logModule('Komparasi KK & LHP')">Komparasi KK & LHP</a></li>
                     <li><a href="<?= base_url(); ?>core/error">-</a></li>
                 </ul>
             </li>
             <li><a><i class="fa fa-industry"></i> Produksi <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
                     <li><a href="<?= base_url(); ?>monprod" onclick="logModule('Monitoring Produksi')">Monitoring Produksi</a></li>
                     <li><a href="<?= base_url(); ?>core/error">-</a></li>
                     <!-- <li><a href="<?= base_url(); ?>core/error">Progress KK</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Produktivitas</a></li> -->
                 </ul>
             </li>
             <li><a><i class="fa fa-check-circle"></i> Quality & Waste <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
                     <li><a href="<?= base_url(); ?>core/error">-</a></li>
                     <!-- <li><a href="<?= base_url(); ?>core/error">Monitoring Reject</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Analisa Waste</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Pareto Reject</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Quality Trend</a></li> -->
                 </ul>
             </li>
             <li><a><i class="fa fa-cogs"></i> Teknik <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
                     <li><a href="<?= base_url(); ?>dashplpteknik" onclick="logModule('Dashboard PLP Teknik')">Dashboard PLP Teknik</a></li>
                     <li><a href="<?= base_url(); ?>monteknik" onclick="logModule('Monitoring PLP Teknik')">Monitoring PLP Teknik</a></li>
                     <li><a href="<?= base_url(); ?>komplplhp" onclick="logModule('Komparasi PLP & LHP')">Komparasi PLP & LHP</a></li>
                     <!-- <li><a href="<?= base_url(); ?>core/error">Performance Mesin</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Utilization Mesin</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Histori Mesin</a></li> -->
                 </ul>
             </li>
             <li><a><i class="fa fa-database"></i> Master Data <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
                     <li><a href="<?= base_url(); ?>masterlimplan" onclick="logModule('Master Limit Plan')">Master Limit Plan</a></li>
                     <li><a href="<?= base_url(); ?>core/error">-</a></li>
                     <!-- <li><a href="<?= base_url(); ?>core/error">Produk</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Proses</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Waste</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Kegiatan</a></li>
                     <li><a href="<?= base_url(); ?>core/error">Operator</a></li> -->
                 </ul>
             </li>
         </ul>
     </div>

 </div>
 <script>
     function logModule(namaModule) {
         fetch('<?= base_url("core/log_module") ?>', {
             method: 'POST',
             keepalive: true,
             headers: {
                 'Content-Type': 'application/x-www-form-urlencoded'
             },
             body: 'nama_module=' + encodeURIComponent(namaModule)
         });
     }
 </script>