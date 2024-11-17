<?php
require_once "controllers/importar.controller.php";
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Importar
      <small>Logs</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Importar</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Configurações de importação</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-6">
            <form class="form-horizontal" id="formImportar" enctype="multipart/form-data">            
              <label for="fileInput">Arquivo</label>
              <div class="input-group">
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="fileInput" name="fileInput">
                  <label class="custom-file-label" for="fileInput">Escolher arquivo</label>
                </div>
                <div class="input-group-append">
                  <span class="input-group-text">Upload</span>
                </div>
              </div>
              <div class="input-group">
                <label>Operações</label>
                <div class="scrollable-checkboxes border p-2">
                <?php
                $controller = new ControllerImportar();
                $controller->gerarOpcoesFormulario();
                ?>
                </div>
              </div>
              <div class="card-footer">
                  <button type="button" id="btImportar" class="btn btn-primary">Enviar</button>
              </div>              
            </form>
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>

    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Logs de importação</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <textarea id="logList" class="form-control" rows="10" readonly style="resize: none;"></textarea>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>    

  </section>
  <!-- /.content -->
</div>
<script src="views/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script>
  $(function () {
    bsCustomFileInput.init();
  });

  $('#btImportar').on('click', function (event) {
    event.preventDefault();
    var formData = new FormData($('#formImportar')[0]);
    
    $.ajax({
      url: 'controllers/importar.controller.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        if (response.error) {
          $('#logList').val(response.error);
        } else {
          $('#logList').val("JSON com dados importados:\n" + JSON.stringify(response.data, null, 2)); // Formata JSON com indentação
        }
      },
      error: function(xhr, status, error) {
        $('#logList').val("Erro ao importar: " + error);
        
      }
    });
  });
</script>