<?php
require_once __DIR__ . "/../models/importar.models.php";

class ControllerImportar
{
    private $operacoesPossiveis = ['SELECT', 'SHOW', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP'];
    private $operacoesSQL = [
        'SELECT' => '/FROM\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'SHOW' => '/SHOW\s+TABLES\s+FROM\s+`?([a-zA-Z0-9_]+)`?/i',
        'INSERT' => '/INSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'UPDATE' => '/UPDATE\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'DELETE' => '/DELETE\s+FROM\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'CREATE' => '/CREATE\s+(?:IF\s+NOT\s+EXISTS\s+)?TABLE\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'ALTER' => '/ALTER\s+TABLE\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i',
        'DROP' => '/DROP\s+(?:IF\s+EXISTS\s+)?TABLE\s+`?([a-zA-Z0-9_]+)`?(?:\.`?([a-zA-Z0-9_]+)`?)?/i'
    ];

    public function gerarOpcoesFormulario()
    {
        foreach ($this->operacoesPossiveis as $operacao) {
            echo '<div class="checkbox">';
            echo '<label class="form-check-label" for="' . strtolower($operacao) . 'Check">';
            echo '<input class="form-check-input" type="checkbox" name="' . strtolower($operacao) . 'Check" id="' . strtolower($operacao) . 'Check"> ' . $operacao;
            echo '</label>';
            echo '</div>';
        }

    }

    private function obterTabelaPrincipal($sql)
    {
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

    private function minerarDadosLog($caminhoArquivo, $operacoesSelecionadas)
    {
        $dadosLog = [];
        $arquivo = fopen($caminhoArquivo, "r");
        $entradaLog = null;
    
        if ($arquivo) {
            while (($linha = fgets($arquivo)) !== false) {
                if (preg_match('/# Time: (\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+Z)|# Time: (\d{6})\s+(\d{2}:\d{2}:\d{2})/', $linha, $matches)) {
                    if ($entradaLog) {
                        $dadosLog[] = $entradaLog;
                    }
    
                    $entradaLog = $this->inicializarEntradaLog();
    
                    if (!empty($matches[1])) {
                        $entradaLog['time'] = $matches[1];
                    } elseif (!empty($matches[2]) && !empty($matches[3])) {
                        $dataFormatada = DateTime::createFromFormat('ymd H:i:s', $matches[2] . ' ' . $matches[3]);
                        $entradaLog['time'] = $dataFormatada->format('Y-m-d H:i:s');
                    }
                }
                elseif (preg_match('/# User@Host: (\w+)\[(\w+)\] @ (\S+) \[(.*?)\](?:\s+Id:\s+(\d+))?/', $linha, $matches)) {
                    $entradaLog['user'] = $matches[1];
                    $entradaLog['host'] = $matches[3];
                    if (!empty($matches[5])) {
                        $entradaLog['thread_id'] = (int) $matches[5];
                    }
                }
                elseif (preg_match('/# Query_time: ([\d.]+)\s+Lock_time: ([\d.]+)\s+Rows_sent: (\d+)\s+Rows_examined: (\d+)/', $linha, $matches)) {
                    $entradaLog['query_time'] = number_format((float) $matches[1], 6, '.', '');
                    $entradaLog['lock_time'] = number_format((float) $matches[2], 6, '.', '');
                    $entradaLog['rows_sent'] = (int) $matches[3];
                    $entradaLog['rows_examined'] = (int) $matches[4];
                }
                elseif (preg_match('/# Thread_id: (\d+)\s+Schema: (\w+)?\s+QC_hit: (\w+)/', $linha, $matches)) {
                    $entradaLog['thread_id'] = (int) $matches[1];
                    $entradaLog['name_schema'] = $matches[2] ?? '';
                }
                elseif (preg_match('/# Rows_affected: (\d+)\s+Bytes_sent: (\d+)/', $linha, $matches)) {
                    $entradaLog['rows_affected'] = (int) $matches[1];
                    $entradaLog['bytes_sent'] = (int) $matches[2];
                }
                elseif (preg_match('/SET timestamp=(\d+);/', $linha, $matches)) {
                    $entradaLog['timestamp'] = (int) $matches[1];
                }
                elseif (preg_match('/(SELECT .*|SHOW .*|INSERT .*|UPDATE .*|DELETE .*|CREATE .*|ALTER .*|DROP .*)/i', $linha, $matches)) {
                    $query = $matches[1];
    
                    while (!preg_match('/;\s*$/', $linha)) {
                        $linha = fgets($arquivo);
                        $query .= ' ' . trim($linha);
                    }
    
                    $entradaLog['query'] = trim($query);
                    $entradaLog['main_table'] = $this->obterTabelaPrincipal($entradaLog['query']);
    
                    foreach ($operacoesSelecionadas as $operacao) {
                        if (stripos($entradaLog['query'], $operacao) === 0) {
                            $dadosLog[] = $entradaLog;
                            break;
                        }
                    }
    
                    $entradaLog = null;
                }
            }
    
            if ($entradaLog) {
                $dadosLog[] = $entradaLog;
            }
    
            fclose($arquivo);
        }
    
        return $dadosLog;
    }
    
    

    private function inicializarEntradaLog()
    {
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
            'name_schema' => '',
            'thread_id' => 0,
            'rows_affected' => 0,
            'errno' => 0,
            'killed' => 0
        ];
    }

    public function processarFormulario()
    {
        $resultado = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_FILES['arquivolog']['name'])) {
                $resultado['success'] = false;
                $resultado['error'] = 'O arquivo de logs deve ser informado!';
            } elseif (isset($_FILES['arquivolog']) && $_FILES['arquivolog']['error'] === UPLOAD_ERR_OK) {
                $caminhoArquivo = $_FILES['arquivolog']['tmp_name'];
                $extensaoArquivo = pathinfo($_FILES['arquivolog']['name'], PATHINFO_EXTENSION);

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
                        $dadosLog = $this->minerarDadosLog($caminhoArquivo, $operacoesSelecionadas);

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