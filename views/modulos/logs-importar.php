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
            <form role="form" id="formImportar" enctype="multipart/form-data">            
              <div class="box-body">
              <div class="form-group">
                  <label for="arquivolog">Arquivo</label>
                  <input type="file" name="arquivolog" id="arquivolog">
                  <p class="help-block">Informe um arquivo com extensão .log</p>
                </div>
                <div class="form-group">
                  <label>Operações</label>
                  <div id="listaOperacoesImp" class="scrollable-checkboxes border p-1">
                    <button type="button" id="selOperacaoes" class="btn btn-primary btn-xs" data-all-checked="false">Marcar todos</button>
                    <script>
                      document.getElementById('selOperacaoes').addEventListener('click', function() {
                        var allChecked = this.dataset.allChecked === 'true';
                        document.querySelectorAll('#listaOperacoesImp input[type="checkbox"]').forEach(function(checkbox) {
                        checkbox.checked = !allChecked;
                        checkbox.dispatchEvent(new Event('change'));
                        });
                        this.dataset.allChecked = !allChecked;
                        this.textContent = allChecked ? 'Marcar todos' : 'Desmarcar todos';
                      });
                    </script>                     
                  <?php
                    $controller = new ControllerImportar();
                    $controller->gerarOpcoesFormulario();
                  ?>
                  </div>
                </div>
              </div>  
              <div class="box-footer">
                  <button type="button" id="btImportar" class="btn btn-primary">Importar</button>
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
        <button type="button" class="btn btn-primary btn-xs pull-right" onclick="copyToClipboard('logList')">Copiar</button>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <textarea id="logList" class="form-control" rows="10" readonly></textarea>
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
          $('#modal-warning .modal-title').text('Erro ao validar dados');
          $('#modal-warning .modal-body').text(response.error);
          $('#modal-warning').modal('show');
        } else {
          $('#logList').val("JSON com dados importados:\n" + JSON.stringify(response.data, null, 2)); // Formata JSON com indentação
        }
      },
      error: function(xhr, status, error) {
        $('#modal-warning .modal-title').text('Erro');
        $('#modal-warning .modal-body').text('Erro ao importar logs: ' + error);
        $('#modal-warning').modal('show');
      }
    });
  });
</script>