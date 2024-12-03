<!DOCTYPE html>
<html lang="es">
<?php session_start(); ?>
<?php
if (!isset($_SESSION['login_id']))
    header('location:login.php');
include 'db_connect.php';
/** @var mysqli $conn */
ob_start();
if (!isset($_SESSION['system'])) {
    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach ($system as $k => $v) {
        $_SESSION['system'][$k] = $v;
    }
}
ob_end_flush();
include 'header.php';
/** @var string $title */
?>
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($page_title) ? $page_title : 'Mi Aplicación'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include 'topbar.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body text-white"></div>
        </div>
        <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?php echo $title; ?></h1>
                    </div>
                </div>
                <hr class="border-primary">
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'home';
                if (!file_exists($page . ".php")) {
                    include '404.html';
                } else {
                    include $page . '.php';
                }
                ?>
            </div>
        </section>

        <!-- Modal Ejemplo -->
        <div class="modal fade" id="confirm_modal" role='dialog'>
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmación</h5>
                    </div>
                    <div class="modal-body">
                        <div id="delete_content"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continuar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="uni_modal" role='dialog'>
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle del Paquete</h5>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="uni_modal_right" role='dialog'>
            <div class="modal-dialog modal-full-height  modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Información...</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span class="fa fa-arrow-right"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="viewer_modal" role='dialog'>
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
                    <img src="" alt="">
                </div>
            </div>
        </div>
    </div>

    </div>

    <?php include 'footer.php'; ?>
    <footer class="main-footer">

        <div class="float-right d-none d-sm-inline-block">
            <b>Grupo 09</b>
        </div>
    </footer>
</div>
</body>
</html>

