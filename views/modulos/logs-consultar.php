<?php
require_once "controllers/consultar.controller.php";
?>
<div class="content-wrapper">
   
  <section class="content-header">
  <h1>
  Consultar
  <small>Logs</small>
  </h1>
  <ol class="breadcrumb">
  <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
  <li class="active">Consultar</li>
  </ol>
  </section>

  <section class="content">
 
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Filtros</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
  </div>
    <div class="box-body">
      <form id="columnFilterForm">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Período:</label>
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-clock-o"></i>
                </div>
                <input type="text" class="form-control pull-right" name="periodo" id="periodo">
              </div>
            </div>
            <div class="form-group">
              <div class="checkbox"><label for="sqlCommand"><input type="checkbox" id="usaFiltroSQL"><b>Filtro SQL:</b></label></div>
              <textarea disabled name="filtroSQL" id="filtroSQL" class="form-control" rows="6" placeholder="Exemplo: WHERE sql_text LIKE 'SELECT%'..." style="resize: none;"></textarea>
            </div>
            <script>
              document.getElementById('usaFiltroSQL').addEventListener('change', function() {
                document.getElementById('filtroSQL').disabled = !this.checked;
              });
            </script>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="lstMainTable">Main table:</label>
              <button type="button" id="selMainTable" class="btn btn-primary btn-xs pull-right" data-all-checked="false">Marcar todos</button>
          
              <div id="filtroMainTable" class="form-control" style="height: 280px; overflow-y: scroll;">
                <?php
                $controller = new ControllerConsultar();
                $controller->gerarOpcoesMainTable();
                ?>
              </div>
            </div>
            <script>
                document.getElementById('selMainTable').addEventListener('click', function() {
                  var allChecked = this.dataset.allChecked === 'true';
                  document.querySelectorAll('#filtroMainTable input[type="checkbox"]').forEach(function(checkbox) {
                  checkbox.checked = !allChecked;
                  checkbox.dispatchEvent(new Event('change'));
                  });
                  this.dataset.allChecked = !allChecked;
                  this.textContent = allChecked ? 'Marcar todos' : 'Desmarcar todos';
                });
              </script>              
          </div>
          <div class="col-md-2" id="selColunas">
            <div class="form-group">
              <label for="columnFilter1">Colunas padrão:</label>
              <button type="button" id="selColsPadrao" class="btn btn-primary btn-xs pull-right" data-all-checked="true">Desmarcar todos</button>
              <script>
                document.getElementById('selColsPadrao').addEventListener('click', function() {
                  var allChecked = this.dataset.allChecked === 'true';
                  document.querySelectorAll('#columnFilter1 input[type="checkbox"]').forEach(function(checkbox) {
                  checkbox.checked = !allChecked;
                  checkbox.dispatchEvent(new Event('change'));
                  });
                  this.dataset.allChecked = !allChecked;
                  this.textContent = allChecked ? 'Marcar todos' : 'Desmarcar todos';
                });
              </script>
              <div id="columnFilter1" class="form-control" style="height: 120px; overflow-y: scroll;">
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="insert_id" checked> insert_id</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="time" checked> time</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="user_host" checked> user_host</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="thread_id" checked> thread_id</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="name_schema" checked> name_schema</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="query_time" checked> query_time</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="lock_time" checked> lock_time</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="rows_sent" checked> rows_sent</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="rows_examined" checked> rows_examined</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="rows_affected" checked> rows_affected</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="bytes_sent" checked> bytes_sent</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="sql_text" checked> sql_text</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="main_table" checked> main_table</label></div>
              </div>
            </div>

            <div class="form-group">
              <label for="columnFilter2">Colunas extra:</label>
              <button type="button" id="selColsExtra" class="btn btn-primary btn-xs pull-right" data-all-checked="true">Desmarcar todos</button>
              <script>
                document.getElementById('selColsExtra').addEventListener('click', function() {
                  var allChecked = this.dataset.allChecked === 'true';
                  document.querySelectorAll('#columnFilter2 input[type="checkbox"]').forEach(function(checkbox) {
                  checkbox.checked = !allChecked;
                  checkbox.dispatchEvent(new Event('change'));
                  });
                  this.dataset.allChecked = !allChecked;
                  this.textContent = allChecked ? 'Marcar todos' : 'Desmarcar todos';
                });
              </script>              
              <div id="columnFilter2" class="form-control" style="height: 120px; overflow-y: scroll;">
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="errno" checked> errno</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="killed" checked> killed</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="bytes_received" checked> bytes_received</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="read_first" checked> read_first</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="read_last" checked> read_last</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="read_key" checked> read_key</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="read_next" checked> read_next</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="read_prev" checked> read_prev</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="read_rnd" checked> read_rnd</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="read_rnd_next" checked> read_rnd_next</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="sort_merge_passes" checked> sort_merge_passes</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="sort_range_count" checked> sort_range_count</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="sort_rows" checked> sort_rows</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="sort_scan_count" checked> sort_scan_count</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="created_tmp_disk_tables" checked> created_tmp_disk_tables</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="created_tmp_tables" checked> created_tmp_tables</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="start" checked> start</label></div>
                <div class="checkbox"><label><input type="checkbox" class="column-toggle" data-column="end" checked> end</label></div>
              </div>
            </div>            
          </div>
          <div class="col-md-2">

          </div>
        </div>
        <div class="box-footer">
          <button id="btConsultar" class="btn btn-primary">Consultar</button>
        </div>
      </form>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <!-- Existing column filter form -->
    </div>
  </div>
  <div class="box">
  <div class="box-header with-border" id="logsTableContainer">
  <table class="table table-bordered table-striped dt-responsive tables" width="100%">
  <thead>
  <tr>
  <th style="width:10px">insert_id</th>
  <th>time</th>
  <th>user_host</th>
  <th>thread_id</th>
  <th>name_schema</th>
  <th>query_time</th>
  <th>lock_time</th>
  <th>rows_sent</th>
  <th>rows_examined</th>
  <th>rows_affected</th>
  <th>bytes_sent</th>
  <th>sql_text</th>
  <th>main_table</th>
  <th>errno</th>
  <th>killed</th>
  <th>bytes_received</th>
  <th>read_first</th>
  <th>read_last</th>
  <th>read_key</th>
  <th>read_next</th>
  <th>read_prev</th>
  <th>read_rnd</th>
  <th>read_rnd_next</th>
  <th>sort_merge_passes</th>
  <th>sort_range_count</th>
  <th>sort_rows</th>
  <th>sort_scan_count</th>
  <th>created_tmp_disk_tables</th>
  <th>created_tmp_tables</th>
  <th>start</th>
  <th>end</th>
  </tr>
  </thead>
  <tbody>
  
  </tbody>
  </table>
  </div>
  </div>

  </div>
  </section>
</div>

<script>
  document.querySelectorAll('.column-toggle').forEach(function(checkbox) {
  checkbox.addEventListener('change', function() {
  var columnTitle = this.parentElement.textContent.trim();
  var table = document.querySelector('.tables');
  var headers = table.querySelectorAll('th');
  var columnIndex = -1;

  headers.forEach(function(header, index) {
    if (header.textContent.trim() === columnTitle) {
    columnIndex = index + 1;
    }
  });

  if (columnIndex !== -1) {
    var cells = table.querySelectorAll('td:nth-child(' + columnIndex + '), th:nth-child(' + columnIndex + ')');
    cells.forEach(function(cell) {
    cell.style.display = checkbox.checked ? '' : 'none';
    });
  }
  });
  });
    
  // ao carregar todo o html chama o clic do botão selColsExtra para escolher as colunas extras
  $(document).ready(function(){
    $('#selColsExtra').click();
  });

  $('#periodo').daterangepicker({
     startDate: '01/01/1901', endDate: '31/12/2199', 
     timePicker: true, timePickerSeconds: true, timePicker24Hour: true,
     timePickerIncrement: 30, 
     locale: {
        format: 'DD/MM/YYYY hh:mm A' 
      }
    }
  );

  $('#btConsultar').on('click', function (event) {
    event.preventDefault();

    // Validação
    let periodo = $('#periodo').val().split(' - ');
    let datahoraInicio = periodo[0].split(' ');
    let datahoraFim = periodo[1].split(' ');
    // testar o formato da data e hora 01/01/1901 05:00 AM - 31/12/2199 01:00 AM
    if (datahoraInicio.length !== 3 || datahoraFim.length !== 3) {
      $("#modal-warning .modal-title").text("Erro");
      $("#modal-warning .modal-body p").text("Formato de data e hora inválido.");
      $("#modal-warning").modal('show');
      return;
    }
     

    // ver se a data inicial é menor que a final
    if (new Date(datahoraInicio[0]) > new Date(datahoraFim[0])) {
      $("#modal-warning .modal-title").text("Erro");
      $("#modal-warning .modal-body p").text("Data inicial maior que a final.");
      $("#modal-warning").modal('show');
      return;
    }

    // alertar o período selecionado
    //alert('Período selecionado: ' + JSON.stringify(periodo));
    if ($('#usaFiltroSQL').is(':checked') && $('#filtroSQL').val().trim() === '') {
      $("#modal-warning .modal-title").text("Erro");
      $("#modal-warning .modal-body p").text("Filtro SQL habilitado, mas não preenchido.");
      $("#modal-warning").modal('show');
      return;
    }    

    // Colunas selecionadas
    let colunasSelecionadas = [];
    $('#selColunas input[type="checkbox"]:checked').each(function () {
      colunasSelecionadas.push($(this).data('column'));
    });

    if (colunasSelecionadas.length === 0) {
      $("#modal-warning .modal-title").text("Erro");
      $("#modal-warning .modal-body p").text("Nenhuma coluna foi selecionada.");
      $("#modal-warning").modal('show');
      return;
    }    

    if ($('#usaFiltroSQL').is(':checked')) {
      var filtroSQL = $('#filtroSQL').val().trim();
    } else {
      var filtroSQL = '';
    }
    
    // Filtro main_table
    let mainTable = [];
    $('#filtroMainTable input[type="checkbox"]:checked').each(function () {
      mainTable.push($(this).data('column'));
    });

    // Faz o envio via AJAX
    $.ajax({
      url: 'controllers/consultar.controller.php',
      type: 'POST',
      data: { action: 'listarLogs', datahoraInicio: datahoraInicio, datahoraFim: datahoraFim, filtroSQL: filtroSQL, filtroMainTable: mainTable, colunas: colunasSelecionadas},
      success: function (response) {
        if (response.error) {
          $("#modal-warning .modal-title").text("Erro");
          $("#modal-warning .modal-body p").text(response.error);
          $("#modal-warning").modal('show');
        } else {
          $('#logsTableContainer').html(response.table);

          // Re-inicializa o DataTable
          $('#logsTable').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
              sProcessing: "Processando...",
              sLengthMenu: "Mostrar _MENU_ registros",
              sZeroRecords: "Nenhum resultado encontrado",
              sEmptyTable: "Não há dados disponíveis nesta tabela",
              sInfo: "Mostrando registros de _START_ a _END_ de um total de _TOTAL_",
              sInfoEmpty: "Mostrando registros de 0 a 0 de um total de 0",
              sInfoFiltered: "(filtrando um total de registros _MAX_)",
              sSearch: "Pesquisar:",
              oPaginate: {
                sFirst: "Primeiro",
                sLast: "Último",
                sNext: "Próximo",
                sPrevious: "Anterior"
              },
              oAria: {
                sSortAscending: ": Ativar para ordenar a coluna crescente",
                sSortDescending: ": Ativar para ordenar a coluna decrescente"
              }
            }            
          });          
        }
      },
      error: function (xhr, status, error) {
        $("#modal-warning .modal-title").text("Erro ao consultar");
        $("#modal-warning .modal-body p").text("Erro ao consultar: " + error);
        $("#modal-warning").modal('show');
      }
    });
  });
</script>