<?php
function obterTabelaPrincipal($sql) {
    // Remover subconsultas
    $sql = preg_replace('/\(([^()]|(?R))*\)/', '', $sql);

    // Procurar a tabela principal após a cláusula FROM ou em consultas SHOW
    if (preg_match('/FROM\s+`?([a-zA-Z0-9_]+)`?\.`?([a-zA-Z0-9_]+)`?/i', $sql, $matches) || preg_match('/FROM\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $matches)) {
        return isset($matches[2]) ? $matches[2] : $matches[1];
    }

    // depuração
    error_log("SQL: " . $sql);
    error_log("Matches: " . print_r($matches, true));

    return null;
}

function minerarDadosDoLog($caminhoArquivo) {
    $dadosLog = [];
    $arquivo = fopen($caminhoArquivo, "r");

    if ($arquivo) {
        while (($linha = fgets($arquivo)) !== false) {
            // Formato 1: # Time: 2024-10-21T01:00:00.853389Z
            if (preg_match('/# Time: (\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+Z)/', $linha, $matches)) {
                $entradaLog = inicializarEntradaLog();
                $entradaLog['time'] = $matches[1];

            // Formato 2: # Time: 240821  0:01:01
            } elseif (preg_match('/# Time: (\d{6})\s+(\d{1,2}:\d{2}:\d{2})/', $linha, $matches)) {
                $dataFormatada = DateTime::createFromFormat('ymd H:i:s', $matches[1] . ' ' . $matches[2]);
                $entradaLog = inicializarEntradaLog();
                $entradaLog['time'] = $dataFormatada->format('Y-m-d H:i:s');

            } elseif (preg_match('/# User@Host: (\w+)\[(\w+)\] @ (\w+) \[(.*?)\]/', $linha, $matches) || 
                      preg_match('/# User@Host: (\w+)\[(\w+)\] @ (\w+) \[(.*?)\] Id: \d+/', $linha, $matches)) {
                $entradaLog['user'] = $matches[1];
                $entradaLog['host'] = $matches[3];

            } elseif (preg_match('/# Query_time: ([\d.]+)  Lock_time: ([\d.]+)  Rows_sent: (\d+)  Rows_examined: (\d+)/', $linha, $matches) || 
                      preg_match('/# Query_time: ([\d.]+) Lock_time: ([\d.]+) Rows_sent: (\d+) Rows_examined: (\d+)/', $linha, $matches)) {
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
                $entradaLog['main_table'] = obterTabelaPrincipal($entradaLog['query']);
                $dadosLog[] = $entradaLog; // Adiciona a entrada de log ao array
            }
        }
        fclose($arquivo);
    }

    return $dadosLog;
}

// Função para inicializar uma entrada de log
function inicializarEntradaLog() {
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

function imprimeLogsMinerados($dadosMinerados) {
    foreach ($dadosMinerados as $reg) {
        $timeFormat = isset($reg['time']) ? (DateTime::createFromFormat('ymd H:i:s', $reg['time']) ?: DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $reg['time'])) : null;
        if ($timeFormat) {
            $timeFormat = $timeFormat->format('Y-m-d H:i:s');
        } else {
            $timeFormat = '--';
        }

        $StartFormat = isset($reg['start']) ? $reg['start'] : null;
        $EndFormat = isset($reg['end']) ? $reg['end'] : null;

        echo "Time: " . $timeFormat . "<br>\n";
        echo "User: " . (isset($reg['user']) ? $reg['user'] : '--') . "<br>\n";
        echo "Host: " . (isset($reg['host']) ? $reg['host'] : '--') . "<br>\n";
        echo "Query Time: " . (isset($reg['query_time']) ? $reg['query_time'] : '--') . "<br>\n";
        echo "Lock Time: " . (isset($reg['lock_time']) ? $reg['lock_time'] : '--') . "<br>\n";
        echo "Rows Sent: " . (isset($reg['rows_sent']) ? $reg['rows_sent'] : '--') . "<br>\n";
        echo "Rows Examined: " . (isset($reg['rows_examined']) ? $reg['rows_examined'] : '--') . "<br>\n";
        echo "Timestamp: " . (isset($reg['timestamp']) ? date('Y-m-d H:i:s', $reg['timestamp']) : '--') . "<br>\n";
        echo "Bytes Received: " . (isset($reg['bytes_received']) ? $reg['bytes_received'] : '--') . "<br>\n";
        echo "Bytes Sent: " . (isset($reg['bytes_sent']) ? $reg['bytes_sent'] : '--') . "<br>\n";
        echo "Read First: " . (isset($reg['read_first']) ? $reg['read_first'] : '--') . "<br>\n";
        echo "Read Last: " . (isset($reg['read_last']) ? $reg['read_last'] : '--') . "<br>\n";
        echo "Read Key: " . (isset($reg['read_key']) ? $reg['read_key'] : '--') . "<br>\n";
        echo "Read Next: " . (isset($reg['read_next']) ? $reg['read_next'] : '--') . "<br>\n";
        echo "Read Prev: " . (isset($reg['read_prev']) ? $reg['read_prev'] : '--') . "<br>\n";
        echo "Read Rnd: " . (isset($reg['read_rnd']) ? $reg['read_rnd'] : '--') . "<br>\n";
        echo "Read Rnd Next: " . (isset($reg['read_rnd_next']) ? $reg['read_rnd_next'] : '--') . "<br>\n";
        echo "Sort Merge Passes: " . (isset($reg['sort_merge_passes']) ? $reg['sort_merge_passes'] : '--') . "<br>\n";
        echo "Sort Range Count: " . (isset($reg['sort_range_count']) ? $reg['sort_range_count'] : '--') . "<br>\n";
        echo "Sort Rows: " . (isset($reg['sort_rows']) ? $reg['sort_rows'] : '--') . "<br>\n";
        echo "Sort Scan Count: " . (isset($reg['sort_scan_count']) ? $reg['sort_scan_count'] : '--') . "<br>\n";
        echo "Created Tmp Disk Tables: " . (isset($reg['created_tmp_disk_tables']) ? $reg['created_tmp_disk_tables'] : '--') . "<br>\n";
        echo "Created Tmp Tables: " . (isset($reg['created_tmp_tables']) ? $reg['created_tmp_tables'] : '--') . "<br>\n";
        echo "Start: " . ($StartFormat ? $StartFormat->format('Y-m-d H:i:s') : '--') . "<br>\n";
        echo "End: " . ($EndFormat ? $EndFormat->format('Y-m-d H:i:s') : '--') . "<br>\n";
        echo "Main Table: " . (isset($reg['main_table']) ? $reg['main_table'] : '--') . "<br>\n";
        echo "Query: " . (isset($reg['query']) ? $reg['query'] : '--') . "<br>\n";
        echo "<br>-------------------------<br><br>\n";
    }
}

function createTable($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS slow_logs (
        insert_id INT AUTO_INCREMENT PRIMARY KEY, 
        last_insert_id INT NOT NULL,
        start_time TIMESTAMP(6) NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
        user_host MEDIUMTEXT NOT NULL,
        query_time TIME(6) NOT NULL,
        lock_time TIME(6) NOT NULL,
        rows_sent INT NOT NULL,
        rows_examined INT NOT NULL,
        db VARCHAR(512) NOT NULL,
        server_id INT UNSIGNED NOT NULL,
        sql_text MEDIUMBLOB NOT NULL,
        thread_id BIGINT UNSIGNED NOT NULL,
        errno INT DEFAULT 0, 
        killed TINYINT DEFAULT 0,
        bytes_received INT DEFAULT 0,
        bytes_sent INT DEFAULT 0,
        read_first INT DEFAULT 0,
        read_last INT DEFAULT 0,
        read_key INT DEFAULT 0,
        read_next INT DEFAULT 0,
        read_prev INT DEFAULT 0,
        read_rnd INT DEFAULT 0,
        read_rnd_next INT DEFAULT 0,
        sort_merge_passes INT DEFAULT 0,
        sort_range_count INT DEFAULT 0,
        sort_rows INT DEFAULT 0,
        sort_scan_count INT DEFAULT 0,
        created_tmp_disk_tables INT DEFAULT 0,
        created_tmp_tables INT DEFAULT 0,
        start TIMESTAMP,
        end TIMESTAMP,
        main_table VARCHAR(100)
    )";
    $pdo->exec($sql);

    $pdo->exec("CREATE INDEX idx_slow_logs_start_time ON slow_logs(start_time)");
    $pdo->exec("CREATE INDEX idx_slow_logs_user_host ON slow_logs(user_host(255))");
    $pdo->exec("CREATE INDEX idx_slow_logs_query_time ON slow_logs(query_time)");
    $pdo->exec("CREATE INDEX idx_slow_logs_lock_time ON slow_logs(lock_time)");
    $pdo->exec("CREATE INDEX idx_slow_logs_rows_sent ON slow_logs(rows_sent)");
    $pdo->exec("CREATE INDEX idx_slow_logs_rows_examined ON slow_logs(rows_examined)");
    $pdo->exec("CREATE INDEX idx_slow_logs_db ON slow_logs(db(255))");
    $pdo->exec("CREATE INDEX idx_slow_logs_server_id ON slow_logs(server_id)");
    $pdo->exec("CREATE INDEX idx_slow_logs_sql_text ON slow_logs(sql_text(255))");
    $pdo->exec("CREATE INDEX idx_slow_logs_start ON slow_logs(start)");
    $pdo->exec("CREATE INDEX idx_slow_logs_end ON slow_logs(end)");
    $pdo->exec("CREATE INDEX idx_slow_logs_main_table ON slow_logs(main_table)");
}

function gravaLogs($pdo, $dadosMinerados) {
    $stmt = $pdo->prepare("
        INSERT INTO slow_logs (
            start_time, user_host, query_time, lock_time, rows_sent, rows_examined, db, last_insert_id, server_id, sql_text, thread_id, errno, killed, bytes_received, bytes_sent, read_first, read_last, read_key, read_next, read_prev, read_rnd, read_rnd_next, sort_merge_passes, sort_range_count, sort_rows, sort_scan_count, created_tmp_disk_tables, created_tmp_tables, start, end, main_table
        ) VALUES (
            :start_time, :user_host, :query_time, :lock_time, :rows_sent, :rows_examined, :db, LAST_INSERT_ID(), :server_id, :sql_text, :thread_id, :errno, :killed, :bytes_received, :bytes_sent, :read_first, :read_last, :read_key, :read_next, :read_prev, :read_rnd, :read_rnd_next, :sort_merge_passes, :sort_range_count, :sort_rows, :sort_scan_count, :created_tmp_disk_tables, :created_tmp_tables, :start, :end, :main_table
        )
    ");
    
    foreach ($dadosMinerados as $reg) {
        // ignorar time não informado
        if (!isset($reg['time'])) {
            continue;
        }

        $stmt->execute([
            ':start_time' => isset($reg['time']) 
                ? (
                    ($date = DateTime::createFromFormat('ymd H:i:s', $reg['time']) ?: DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $reg['time'])) 
                    ? $date->format('Y-m-d H:i:s') 
                    : '1970-01-01 00:00:01'
                ) 
                : '1970-01-01 00:00:01',
            ':user_host' => isset($reg['user']) && isset($reg['host']) ? $reg['user'] . '@' . $reg['host'] : null,
            ':query_time' => $reg['query_time'] ?? null,
            ':lock_time' => $reg['lock_time'] ?? null,
            ':rows_sent' => $reg['rows_sent'] ?? null,
            ':rows_examined' => $reg['rows_examined'] ?? null,
            ':db' => $reg['db'] ?? null,
            ':server_id' => $reg['server_id'] ?? null,
            ':sql_text' => $reg['query'] ?? null,
            ':thread_id' => $reg['thread_id'] ?? null,
            ':errno' => $reg['errno'] ?? 0,
            ':killed' => $reg['killed'] ?? 0,
            ':bytes_received' => $reg['bytes_received'] ?? 0,
            ':bytes_sent' => $reg['bytes_sent'] ?? 0,
            ':read_first' => $reg['read_first'] ?? 0,
            ':read_last' => $reg['read_last'] ?? 0,
            ':read_key' => $reg['read_key'] ?? 0,
            ':read_next' => $reg['read_next'] ?? 0,
            ':read_prev' => $reg['read_prev'] ?? 0,
            ':read_rnd' => $reg['read_rnd'] ?? 0,
            ':read_rnd_next' => $reg['read_rnd_next'] ?? 0,
            ':sort_merge_passes' => $reg['sort_merge_passes'] ?? 0,
            ':sort_range_count' => $reg['sort_range_count'] ?? 0,
            ':sort_rows' => $reg['sort_rows'] ?? 0,
            ':sort_scan_count' => $reg['sort_scan_count'] ?? 0,
            ':created_tmp_disk_tables' => $reg['created_tmp_disk_tables'] ?? 0,
            ':created_tmp_tables' => $reg['created_tmp_tables'] ?? 0,
            ':start' => isset($reg['start']) ? (
                ($startDate = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $reg['start'])) 
                ? $startDate->format('Y-m-d H:i:s') 
                : null
            ) : null,
            ':end' => isset($reg['end']) ? (
                ($endDate = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $reg['end'])) 
                ? $endDate->format('Y-m-d H:i:s') 
                : null
            ) : null,
            ':main_table' => $reg['main_table'] ?? null
        ]);
        
    }
}

$caminhoLogs = 'slow_query.log';
//$caminhoLogs = 'extra.log';
$dadosMinerados = minerarDadosDoLog($caminhoLogs);

//imprimeLogsMinerados($dadosMinerados);

$dsn = 'mysql:host=localhost;charset=utf8';
$username = 'root';
$password = 'root';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Sempre excluir/criar o banco de dados para recriar
    $pdo->exec("DROP DATABASE IF EXISTS slow_logs_db");
    $pdo->exec("CREATE DATABASE IF NOT EXISTS slow_logs_db");
    $pdo->exec("USE slow_logs_db");

    createTable($pdo);

    gravaLogs($pdo, $dadosMinerados);

    echo "Dados inseridos no banco de dados.\n";
} catch (PDOException $e) {
    echo 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
}
?>