<?php
require_once "models/consultar.models.php";
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
      <h3 class="box-title">Filtrar Colunas</h3>
    </div>
    <div class="box-body">
      <form id="columnFilterForm">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="columnFilter1">Filtrar Colunas (1-15):</label>
              <div id="columnFilter1" class="form-control" style="height: 200px; overflow-y: scroll;">
                <label><input type="checkbox" class="column-toggle" data-column="time" checked> time</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="user_host" checked> user_host</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="thread_id" checked> thread_id</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="name_schema" checked> name_schema</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="query_time" checked> query_time</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="lock_time" checked> lock_time</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="rows_sent" checked> rows_sent</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="rows_examined" checked> rows_examined</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="rows_affected" checked> rows_affected</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="bytes_sent" checked> bytes_sent</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="sql_text" checked> sql_text</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="main_table" checked> main_table</label><br>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="columnFilter2">Filtrar Colunas (16-30):</label>
              <div id="columnFilter2" class="form-control" style="height: 200px; overflow-y: scroll;">
                <label><input type="checkbox" class="column-toggle" data-column="errno" checked> errno</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="killed" checked> killed</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="bytes_received" checked> bytes_received</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="read_first" checked> read_first</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="read_last" checked> read_last</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="read_key" checked> read_key</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="read_next" checked> read_next</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="read_prev" checked> read_prev</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="read_rnd" checked> read_rnd</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="read_rnd_next" checked> read_rnd_next</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="sort_merge_passes" checked> sort_merge_passes</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="sort_range_count" checked> sort_range_count</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="sort_rows" checked> sort_rows</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="sort_scan_count" checked> sort_scan_count</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="created_tmp_disk_tables" checked> created_tmp_disk_tables</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="created_tmp_tables" checked> created_tmp_tables</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="start" checked> start</label><br>
                <label><input type="checkbox" class="column-toggle" data-column="end" checked> end</label><br>
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <button id="btConsultar" class="btn btn-primary">Consultar</button>
        </div>
      </form>
    </div>
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
  </script>
  

  <div class="box-body" id="logsTableContainer">
  <table class="table table-bordered table-striped dt-responsive tables" width="100%">
  <thead>
  <tr>
  <th style="width:10px">ID</th>
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
  <th>Ações</th>
  </tr>
  </thead>
  <tbody>
  
  </tbody>
  </table>
  </div>

  </div>
  </section>
</div>

<script>
  $('#btConsultar').on('click', function (event) {
    event.preventDefault();

    // Coleta as colunas selecionadas
    let colunasSelecionadas = [];
    $('#columnFilterForm input[type="checkbox"]:checked').each(function () {
      colunasSelecionadas.push($(this).data('column'));
    });

    // Faz o envio via AJAX
    $.ajax({
      url: 'controllers/consultar.controller.php',
      type: 'POST',
      data: { action: 'listarLogs', colunas: colunasSelecionadas },
      success: function (response) {
        if (response.error) {
          // Exibe a mensagem de erro no modal
          $("#modal-warning .modal-title").text("Erro");
          $("#modal-warning .modal-body p").text(response.error);
          $("#modal-warning").modal('show');
        } else {
          // Insere a tabela pronta no container
          $('#logsTableContainer').html(response.table);

          // Re-inicializa o DataTables
          $('#logsTable').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
              url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Portuguese-Brasil.json" // Altere conforme necessário
            }
          });          
        }
      },
      error: function (xhr, status, error) {
        // Exibe a mensagem de erro no modal
        $("#modal-warning .modal-title").text("Erro ao consultar");
        $("#modal-warning .modal-body p").text("Erro ao consultar: " + error);
        $("#modal-warning").modal('show');
      }
    });
  });
</script>