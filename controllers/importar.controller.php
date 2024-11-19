<?php
require_once __DIR__ . "/../models/importar.models.php";

class ControllerImportar{
    private $operacoesPossiveis = ['SELECT', 'SHOW', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP'];
    private $operacoesSQL = [
        'SELECT' => '/FROM\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'SHOW' => '/SHOW\s+TABLES\s+FROM\s+`?([a-zA-Z0-9_]+)`?/i',
        'INSERT' => '/INSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'UPDATE' => '/UPDATE\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'DELETE' => '/DELETE\s+FROM\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'CREATE' => '/CREATE\s+TABLE\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'ALTER' => '/ALTER\s+TABLE\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'DROP' => '/DROP\s+TABLE\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i'
    ];

    public function gerarOpcoesFormulario() {
        echo '<div class="form-check">';
        echo '<input class="form-check-input" type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)">';
        echo '<label class="form-check-label" for="checkAll">Marcar todos</label>';
        echo '</div>';
        echo '<script>
            function toggleCheckboxes(source) {
            checkboxes = document.querySelectorAll(".form-check-input");
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i] != source) {
                checkboxes[i].checked = source.checked;
                }
            }
            }
        </script>';

        foreach ($this->operacoesPossiveis as $operacao) {
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="checkbox" name="' . strtolower($operacao) . 'Check" id="' . strtolower($operacao) . 'Check">';
            echo '<label class="form-check-label" for="' . strtolower($operacao) . 'Check">' . $operacao . '</label>';
            echo '</div>';
        }

    }

    private function obterTabelaPrincipal($sql) {
        $sql = preg_replace('/\(([^()]|(?R))*\)/', '', $sql); // Remove subconsultas

        foreach ($this->operacoesSQL as $pattern) {
            if (preg_match($pattern, $sql, $matches)) {
                return isset($matches[2]) ? $matches[2] : $matches[1];
            }
        }

        error_log("SQL: " . $sql);
        error_log("Matches: " . print_r($matches, true));

        return null;
    }

    private function minerarDadosDoLog($caminhoArquivo, $operacoesSelecionadas) {
        $dadosLog = [];
        $arquivo = fopen($caminhoArquivo, "r");

        if ($arquivo) {
            while (($linha = fgets($arquivo)) !== false) {
                // Formato 1: # Time: 2024-10-21T01:00:00.853389Z
                if (preg_match('/# Time: (\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+Z)/', $linha, $matches)) {
                    $entradaLog = $this->inicializarEntradaLog();
                    $entradaLog['time'] = $matches[1];

                // Formato 2: # Time: 240821  0:01:01
                } elseif (preg_match('/# Time: (\d{6})\s+(\d{1,2}:\d{2}:\d{2})/', $linha, $matches)) {
                    $dataFormatada = DateTime::createFromFormat('ymd H:i:s', $matches[1] . ' ' . $matches[2]);
                    $entradaLog = $this->inicializarEntradaLog();
                    $entradaLog['time'] = $dataFormatada->format('Y-m-d H:i:s');

                } elseif (preg_match('/# User@Host: (\w+)\[(\w+)\] @ (\w+) \[(.*?)\]/', $linha, $matches) || 
                          preg_match('/# User@Host: (\w+)\[(\w+)\] @ (\w+) \[(.*?)\] Id: \d+/', $linha, $matches)) {
                    $entradaLog['user'] = $matches[1];
                    $entradaLog['host'] = $matches[3];

                } elseif (preg_match('/# Query_time: ([\d.]+)\s+Lock_time: ([\d.]+)\s+Rows_sent: (\d+)\s+Rows_examined: (\d+)/', $linha, $matches)) {
                    $entradaLog['query_time'] = (float)$matches[1];
                    $entradaLog['lock_time'] = (float)$matches[2];
                    $entradaLog['rows_sent'] = (int)$matches[3];
                    $entradaLog['rows_examined'] = (int)$matches[4];

                } elseif (preg_match('/# Thread_id: (\d+)  Schema: (\w+)  QC_hit: \w+/', $linha, $matches)) {
                    $entradaLog['thread_id'] = (int)$matches[1];
                    $entradaLog['db'] = $matches[2];

                } elseif (preg_match('/# Rows_affected: (\d+)  Bytes_sent: (\d+)/', $linha, $matches)) {
                    $entradaLog['rows_affected'] = (int)$matches[1];
                    $entradaLog['bytes_sent'] = (int)$matches[2];

                } elseif (preg_match('/Bytes_received: (\d+) Bytes_sent: (\d+) Read_first: (\d+) Read_last: (\d+) Read_key: (\d+) Read_next: (\d+) Read_prev: (\d+) Read_rnd: (\d+) Read_rnd_next: (\d+) Sort_merge_passes: (\d+) Sort_range_count: (\d+) Sort_rows: (\d+) Sort_scan_count: (\d+) Created_tmp_disk_tables: (\d+) Created_tmp_tables: (\d+)/', $linha, $matches)) {
                    $entradaLog['bytes_received'] = (int)$matches[1];
                    $entradaLog['bytes_sent'] = (int)$matches[2];
                    $entradaLog['read_first'] = (int)$matches[3];
                    $entradaLog['read_last'] = (int)$matches[4];
                    $entradaLog['read_key'] = (int)$matches[5];
                    $entradaLog['read_next'] = (int)$matches[6];
                    $entradaLog['read_prev'] = (int)$matches[7];
                    $entradaLog['read_rnd'] = (int)$matches[8];
                    $entradaLog['read_rnd_next'] = (int)$matches[9];
                    $entradaLog['sort_merge_passes'] = (int)$matches[10];
                    $entradaLog['sort_range_count'] = (int)$matches[11];
                    $entradaLog['sort_rows'] = (int)$matches[12];
                    $entradaLog['sort_scan_count'] = (int)$matches[13];
                    $entradaLog['created_tmp_disk_tables'] = (int)$matches[14];
                    $entradaLog['created_tmp_tables'] = (int)$matches[15];

                } elseif (preg_match('/SET timestamp=(\d+);/', $linha, $matches)) {
                    $entradaLog['timestamp'] = (int)$matches[1];

                } elseif (preg_match('/Start: (\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+Z)/', $linha, $matches)) {
                    $entradaLog['start'] = $matches[1];

                } elseif (preg_match('/End: (\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+Z)/', $linha, $matches)) {
                    $entradaLog['end'] = $matches[1];

                } elseif (preg_match('/(select .*|set .*|show .*)/i', $linha, $matches)) {
                    $entradaLog['query'] = trim($matches[1]);
                    $entradaLog['main_table'] = $this->obterTabelaPrincipal($entradaLog['query']);
                    
                    // Filtra as operações selecionadas
                    foreach ($operacoesSelecionadas as $operacao) {
                        if (stripos($entradaLog['query'], $operacao) === 0) {
                            $dadosLog[] = $entradaLog;
                            break;
                        }
                    }
                }
            }
            fclose($arquivo);
        }

        return $dadosLog;
    }

    private function inicializarEntradaLog() {
        return [
            'time' => '',
            'user' => '',
            'host' => '',
            'query_time' => 0,
            'lock_time' => 0,
            'rows_sent' => 0,
            'rows_examined' => 0,
            'timestamp' => 0,
            'query' => '',
            'main_table' => '',
            'bytes_received' => 0,
            'bytes_sent' => 0,
            'read_first' => 0,
            'read_last' => 0,
            'read_key' => 0,
            'read_next' => 0,
            'read_prev' => 0,
            'read_rnd' => 0,
            'read_rnd_next' => 0,
            'sort_merge_passes' => 0,
            'sort_range_count' => 0,
            'sort_rows' => 0,
            'sort_scan_count' => 0,
            'created_tmp_disk_tables' => 0,
            'created_tmp_tables' => 0,
            'start' => '',
            'end' => '',
            'db' => '',
            'server_id' => 0,
            'thread_id' => 0,
            'rows_affected' => 0,
            'errno' => 0,
            'killed' => 0
        ];
    }

    public function processarFormulario() {
        $resultado = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_FILES['fileInput']['name'])) {
                $resultado['success'] = false;
                $resultado['error'] = 'O arquivo de logs deve ser informado!';
            } elseif (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
                $caminhoArquivo = $_FILES['fileInput']['tmp_name'];
                $extensaoArquivo = pathinfo($_FILES['fileInput']['name'], PATHINFO_EXTENSION);

                if ($extensaoArquivo !== 'log') {
                    $resultado['success'] = false;
                    $resultado['error'] = 'Apenas arquivos com extensão .log são permitidos.';
                } else {
                    $operacoesSelecionadas = [];

                    foreach ($this->operacoesPossiveis as $operacao) {
                        if (isset($_POST[strtolower($operacao) . 'Check'])) {
                            $operacoesSelecionadas[] = $operacao;
                        }
                    }                

                    if (empty($operacoesSelecionadas)) {
                        $resultado['success'] = false;
                        $resultado['error'] = 'Pelo menos uma operação deve ser selecionada.';
                    } else {
                        $dadosLog = $this->minerarDadosDoLog($caminhoArquivo, $operacoesSelecionadas);

                        $pdo = Connection::conectar();
                        $importar = new ModelsImportar();
                        $importar->gravaLogs($pdo, $dadosLog);

                        $resultado['success'] = true;
                        $resultado['data'] = $dadosLog;
                    }
                }
            } else {
                $resultado['success'] = false;
                $resultado['error'] = 'Erro ao enviar o arquivo: ' . ($_FILES['fileInput']['error'] ?? 'Erro desconhecido');
            }
        } else {
            $resultado['success'] = false;
            $resultado['error'] = 'Método de requisição inválido.';
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerImportar();
    $controller->processarFormulario();
}
?>