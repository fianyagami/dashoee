<!doctype html>
<html lang="en">

<head>
    <?php
    if (isset($_SESSION[$GLOBALS['project'] . '-USERNAME'])) {
        redirect(base_url('core'));
    }
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- <title>Sign in · 2026 Redesign Preview</title> -->
    <link rel="icon" href="<?= base_url() ?>assets/images/pura_logo.ico" type="image/ico" />
    <title><?= $GLOBALS['nama_project'] ?> </title>
    <link href="<?= $GLOBALS['assets'] ?>css/fontku.css" rel="stylesheet">
    <link href="<?= $GLOBALS['assets'] ?>vendors/adminator/style.css" rel="stylesheet">
    <style>
        .auth-aside {
            background: linear-gradient(135deg, #817c78, #060505);
            color: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 40px 48px;
            position: relative;
        }

        /* === Custom override login page === */
        .field-label {
            font-size: 14px;
        }

        .auth-submit {
            justify-content: center;
            font-size: 16px;
        }
    </style>
</head>

<body class="login">
    <div class="auth-shell">
        <aside class="auth-aside">
            <div class="auth-brand">
                <div class="name"></div>
            </div>
            <div class="auth-aside-body"><span class="auth-aside-eyebrow"></span>
                <h1>TSS-01 Dashboard OEE</h1>
                <p>Sistem monitoring OEE (AR, PR, QR) per KK/Produk, mesin, dan mingguan, dilengkapi analisis downtime, defect, serta laporan produksi dan perbaikan mesin harian.</p>
            </div>
            <div class="auth-aside-footer"></div>
        </aside>
        <main class="auth-main">

            <div class="auth-card">
                <h2>Welcome back</h2>
                <!-- <p class="sub">Sign in to your Adminator workspace to pick up where you left off.</p> -->
                <!-- <form class="auth-form" onsubmit='event.preventDefault(),window.location="index.html"'> -->
                <form class="auth-form" id="loginForm" action="<?= base_url('login/log_in'); ?>" method="post">
                    <div>
                        <div class="field-row">
                            <label class="field-label">Username</label>
                        </div>
                        <input id="email" name="username" class="input" type="text" placeholder="Username" style="text-transform: lowercase;" required>
                    </div>
                    <div>
                        <div class="field-row">
                            <label class="field-label">Password</label>
                        </div>
                        <input id="showPass" name="password" class="input" type="password" placeholder="••••••••" style="text-transform: lowercase;" required>
                    </div><label class="check"><input type="checkbox" onclick="FungtionshowPass()"> <span class="box"></span> Show Password</label>
                    <button class="btn btn--md btn--success btn--block auth-submit" type="submit">
                        <span class="bigger-110">Login</span>
                    </button>
                </form>

            </div>
            <div class="auth-main-bottom">
                <p>©2026 All Rights Reserved.<br /> <a href="http://192.168.37.65/aplikasi" target="_blank">PT Pura Barutama - TSS 01</a></p>
            </div>
        </main>
    </div>


    <script>
        ! function() {
            try {
                var t = localStorage.getItem("dash26-theme"),
                    e = window.matchMedia("(prefers-color-scheme: light)").matches;
                document.documentElement.setAttribute("data-theme", t || (e ? "dark" : "light"))
            } catch (t) {
                document.documentElement.setAttribute("data-theme", "light")
            }
        }()
    </script>
    <script type="text/javascript">
        function FungtionshowPass() {
            var x = document.getElementById("showPass");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
    <script defer="defer" src="<?= $GLOBALS['assets'] ?>vendors/adminator/runtime.js"></script>
    <script defer="defer" src="<?= $GLOBALS['assets'] ?>vendors/adminator/vendor-fullcalendar.js"></script>
    <script defer="defer" src="<?= $GLOBALS['assets'] ?>vendors/adminator/vendor-chartjs.js"></script>
    <script defer="defer" src="<?= $GLOBALS['assets'] ?>vendors/adminator/vendors.js"></script>
    <script defer="defer" src="<?= $GLOBALS['assets'] ?>vendors/adminator/2026.js"></script>

    <link href="<?= $GLOBALS['assets'] ?>css/sweetalert2.min.css" rel="stylesheet">
    <script src="<?= $GLOBALS['assets'] ?>js/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Berhasil',
                            text: 'Selamat datang kembali!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = data.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: 'Username atau Password anda tidak sesuai.'
                        }).then(function() {
                            form.reset();
                        });
                    }
                })
                .catch(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Tidak dapat menghubungi server. Silakan coba lagi.'
                    });
                });
        });
    </script>
</body>

</html>