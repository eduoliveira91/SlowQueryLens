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
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="columnFilter1">Filtrar Colunas (1-15):</label>
          <div id="columnFilter1" class="form-control" style="height: 200px; overflow-y: scroll;">
            <label><input type="checkbox" class="column-toggle" data-column="1" checked> user_host</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="2" checked> query_time</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="3" checked> lock_time</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="4" checked> rows_sent</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="5" checked> rows_examined</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="6" checked> db</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="7" checked> server_id</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="8" checked> sql_text</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="9" checked> thread_id</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="10" checked> errno</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="11" checked> killed</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="12" checked> bytes_received</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="13" checked> bytes_sent</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="14" checked> read_first</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="15" checked> read_last</label><br>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="columnFilter2">Filtrar Colunas (16-29):</label>
          <div id="columnFilter2" class="form-control" style="height: 200px; overflow-y: scroll;">
            <label><input type="checkbox" class="column-toggle" data-column="16" checked> read_key</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="17" checked> read_next</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="18" checked> read_prev</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="19" checked> read_rnd</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="20" checked> read_rnd_next</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="21" checked> sort_merge_passes</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="22" checked> sort_range_count</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="23" checked> sort_rows</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="24" checked> sort_scan_count</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="25" checked> created_tmp_disk_tables</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="26" checked> created_tmp_tables</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="27" checked> start</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="28" checked> end</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="29" checked> main_table</label><br>
          </div>
        </div>
      </div>
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
  </div>

  <div class="box-body">
  <table class="table table-bordered table-striped dt-responsive tables" width="100%">
  <thead>
  <tr>
  <th style="width:10px">ID</th>
  <th>user_host</th>
  <th>query_time</th>
  <th>lock_time</th>
  <th>rows_sent</th>
  <th>rows_examined</th>
  <th>db</th>
  <th>server_id</th>
  <th>sql_text</th>
  <th>thread_id</th>
  <th>errno</th>
  <th>killed</th>
  <th>bytes_received</th>
  <th>bytes_sent</th>
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
  <th>main_table</th>
  <th>Ações</th>
  </tr>
  </thead>
  <tbody>
  <?php
  $logs = ModelsConsultar::ListaLogs();

  foreach ($logs as $log) {
    if (is_array($log)) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($log["last_insert_id"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["user_host"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["query_time"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["lock_time"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["rows_sent"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["rows_examined"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["db"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["server_id"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["sql_text"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["thread_id"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["errno"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["killed"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["bytes_received"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["bytes_sent"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["read_first"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["read_last"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["read_key"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["read_next"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["read_prev"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["read_rnd"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["read_rnd_next"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["sort_merge_passes"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["sort_range_count"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["sort_rows"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["sort_scan_count"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["created_tmp_disk_tables"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["created_tmp_tables"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["start"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["end"]) . '</td>';
    echo '<td>' . htmlspecialchars($log["main_table"]) . '</td>';
    echo '<td>
    <div>
    <button class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></button>
    <button class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button>
    </div>
    </td>';
    echo '</tr>';
    }
  }
  ?>
  </tbody>
  </table>
  </div>

  </div>
  </section>
</div>