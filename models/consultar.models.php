<?php

require_once "connection.php";

class ModelsConsultar {
    static public function RetornaOpcoesMainTable() {
        try {
            $stmt = Connection::conectar()->prepare("SELECT DISTINCT main_table FROM slow_logs WHERE insert_id > 0 and main_table IS NOT NULL and main_table <> ''");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro na consulta SQL: " . $e->getMessage());
            return ["error" => $e->getMessage()];
        }
    }

    static public function ConsultarLogs($colunas,  $datahoraInicio, $datahoraFim, $filtroSQL, $filtroMainTable) {
        if (empty($colunas)) {
            return [];
        }

        $colunasSQL = implode(',', array_map(function ($coluna) {
            return "`$coluna`";
        }, $colunas));

        // se o filtro por SQL aplica uma cláusula WHERE
        $where = [];
        if (!empty($filtroSQL)) {
            $where[] = $filtroSQL;
        } else {
            if (!empty($datahoraInicio) && !empty($datahoraFim)) {
                $where[] = " (time BETWEEN :inicio AND :fim) ";
            }

            if (!empty($filtroMainTable)) {
                $where[] = "main_table in (:filtroMainTable)";
            }
        }

        try {
            $stmt = Connection::conectar()->prepare("SELECT $colunasSQL FROM slow_logs " . (!empty($where) ? "WHERE " . implode(' AND ', $where) : ''));
            if (empty($filtroSQL)) {
                if (!empty($datahoraInicio) && !empty($datahoraFim)) {
                    $stmt->bindValue(':inicio', $datahoraInicio);
                    $stmt->bindValue(':fim', $datahoraFim);
                }

                if (!empty($filtroMainTable)) {
                    $filtroMainTable = array_map(function($item) {
                        return $item === 'sem_informacao' ? '' : $item;
                    }, $filtroMainTable);
                    $stmt->bindValue(':filtroMainTable', implode(",", array_map(function($item) {
                        return "'" . $item . "'";
                    }, $filtroMainTable)));
                }
            }
            $stmt->execute();

            // o retorno deve trazer o sql executado com as variáveis substituídas e os dados
            return [
                "sql" => $stmt->queryString,
                "dados" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (PDOException $e) {
            // Log de erro para depuração
            error_log("Erro na consulta SQL: " . $e->getMessage());
            return ["error" => $e->getMessage()];
        }
	}	

}