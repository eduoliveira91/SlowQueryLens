<?php
 /* session_start();  */
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>SlowQueryLens</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Icone da aplicação -->
  <link rel="icon" href="views/img/template/favicon.png">
   
  <!--=====================================
  PLUGINS DE CSS
  ======================================-->

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="views/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="views/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="views/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="views/dist/css/adminlte.css">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="views/dist/css/skins/_all-skins.min.css">
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
   <!-- DataTables -->
  <link rel="stylesheet" href="views/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="views/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="views/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
   <!-- Daterange picker -->
  <link rel="stylesheet" href="views/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="views/bower_components/morris.js/morris.css">

  <!--=====================================
  PLUGINS DE JAVASCRIPT
  ======================================-->

  <!-- jQuery 3 -->
  <script src="views/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
  <script src="views/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- FastClick -->
  <script src="views/bower_components/fastclick/lib/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="views/dist/js/adminlte.min.js"></script>
  <!-- DataTables -->
  <script src="views/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="views/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="views/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
  <script src="views/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>
  <!-- SweetAlert 2 -->
  <script src="views/plugins/sweetalert2/sweetalert2.all.js"></script>
   <!-- By default SweetAlert2 doesn't support IE. To enable IE 11 support, include Promise polyfill:-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
  <!-- InputMask -->
  <script src="views/plugins/inputmask/inputmask.js"></script>
  <!-- bs-custom-file-input -->
  <script src="views/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
  <!-- daterangepicker http://www.daterangepicker.com/-->
  <script src="views/bower_components/moment/min/moment.min.js"></script>
  <script src="views/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- Morris.js charts http://morrisjs.github.io/morris.js/-->
  <script src="views/bower_components/raphael/raphael.min.js"></script>
  <script src="views/bower_components/morris.js/morris.min.js"></script>
  <!-- ChartJS http://www.chartjs.org/-->
  <script src="views/bower_components/Chart.js/Chart.js"></script>
</head>

<body class="hold-trasition skin-blue sidebar-mini login-page">
  <!-- sidebar-collapse fica recolhido, padrão aberto -->

<div id="ajaxLoadingModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body text-center">
        <i class="fa fa-refresh fa-spin fa-3x"></i>
        <p>
        <?php
        if (isset($_GET["rota"])) {
          if ($_GET["rota"] == "logs-importar") {
            echo "Importando logs...";
          } elseif ($_GET["rota"] == "logs-consultar") {
            echo "Consultando registros...";
          }
        }
      ?>
      </p>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal-warning">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Warning Modal</h4>
      </div>
      <div class="modal-body" style="word-wrap: break-word !important;">
        <p> </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<script>
  $(document).ajaxStart(function() {
    $("#ajaxLoadingModal").modal('show');
  }).ajaxStop(function() {
    $("#ajaxLoadingModal").modal('hide');
  });

  function copyToClipboard(elementId) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    document.execCommand("copy");
  }
</script>

 

<?php

//if(isset($_SESSION["startSession"]) && $_SESSION["startSession"] == "ok"){
if (true) {

  /*Site wrapper */
  //echo '<div class="wrapper">';

  /*====================================================
  Cabeçario
  ====================================================*/
    include "modulos/cabecario.php";
  /*====================================================
  Menu
  ====================================================*/
    include "modulos/menu.php";
  /*====================================================
  Conteudo
  ====================================================*/

  if(isset($_GET["rota"])){

    if($_GET["rota"] == "inicio" ||
      $_GET["rota"] == "logs-importar" ||
      $_GET["rota"] == "logs-consultar" ||
      $_GET["rota"] == "usuarios" ||
      $_GET["rota"] == "sair"){

      include "modulos/".$_GET["rota"].".php";
    }else{
      include "modulos/404.php";
    }

  }else{
    include "modulos/inicio.php";
  }

  /*====================================================
  Roda pé
  ====================================================*/
  include "modulos/footer.php";

 // echo '</div>';

}else{
  //include "modulos/login.php";
  include "modulos/inicio.php";
}
?>

<script src="views/js/template.js"></script>
</body>
</html>
