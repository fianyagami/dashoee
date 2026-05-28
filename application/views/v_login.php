<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    if (isset($_SESSION['dashoee-USERNAME'])) {
        redirect(base_url('core'));
    }
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url() ?>assets/images/pura_logo.ico" type="image/ico" />
    <title><?= $GLOBALS['nama_project'] ?> </title>

    <!-- Bootstrap -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="<?= $GLOBALS['assets'] ?>vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?= $GLOBALS['assets'] ?>css/custom.min.css" rel="stylesheet">
</head>

<body class="login">
    <div>
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <form action="<?= base_url('login/log_in'); ?>" method="post">
                        <h1>Login Form</h1>
                        <div>
                            <input type="text" class="form-control" placeholder="Username" required="" name="username" style="text-transform: lowercase;" />
                        </div>
                        <div>
                            <input type="password" id="showPass" class="form-control" placeholder="Password" required="" name="password" style="text-transform: lowercase;" />
                        </div>
                        <div>
                            <label class="pull-left">
                                <input type="checkbox" onclick="FungtionshowPass()">
                                <span class="lbl"> Show Password</span>
                            </label>
                        </div>
                        <br>
                        <div class="clearfix"></div>
                        <div>
                            <button type="submit" value="Log in" class="btn btn-md btn-success btn-block">
                                <span class="bigger-110">&nbsp &nbsp Login</span>
                            </button>
                        </div>

                        <div class="clearfix"></div>

                        <div class="separator">
                            <div class="clearfix"></div>

                            <div>
                                <!-- <h1><i class="fa fa-paw"></i> Gentelella Alela!</h1> -->
                                <h1><i class="fa fa-pie-chart"></i>&nbsp &nbsp<span><?= $GLOBALS['nama_project'] ?> </span></h1>
                                <p>©2026 All Rights Reserved.<br /> <a href="http://192.168.37.65/aplikasi" target="_blank">PT Pura Barutama - TSS 01</p>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>


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



</body>

</html>