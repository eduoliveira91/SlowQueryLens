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

    static public function ConsultarLogs($colunas, $datahoraInicio, $datahoraFim, $filtroSQL, $filtroMainTable) {
        if (empty($colunas)) {
            return [];
        }
    
        $colunasSQL = implode(',', array_map(function ($coluna) {
            return "`$coluna`";
        }, $colunas));
    
        // Montagem do WHERE
        $where = [];
        $params = []; // Parâmetros da consulta
    
        if (!empty($filtroSQL)) {
            $where[] = $filtroSQL;
        } else {
            if (!empty($datahoraInicio) && !empty($datahoraFim)) {
                $where[] = " (time BETWEEN :inicio AND :fim) ";
                $params[':inicio'] = $datahoraInicio;
                $params[':fim'] = $datahoraFim;
            }
    
            if (!empty($filtroMainTable)) {
                // Verifica se o item 'sem_informacao' está presente
                $includeNullAndEmpty = in_array('sem_informacao', $filtroMainTable, true);
    
                // Limpa os valores de filtroMainTable
                $filtroMainTable = array_map(function($item) {
                    return $item === 'sem_informacao' ? '' : $item;
                }, $filtroMainTable);
    
                // Cria placeholders para os valores
                $placeholders = [];
                foreach ($filtroMainTable as $index => $value) {
                    if ($value !== '') { // Evita adicionar string vazia duas vezes
                        $key = ":mainTable" . $index;
                        $placeholders[] = $key;
                        $params[$key] = $value;
                    }
                }
    
                // Adiciona a cláusula IN
                $clause = "main_table IN (" . implode(',', $placeholders) . ")";
    
                // Adiciona condições para NULL e string vazia, se necessário
                if ($includeNullAndEmpty) {
                    $clause .= " OR main_table IS NULL OR main_table = ''";
                }
    
                $where[] = "($clause)";
            }
        }
    
        try {
            // Monta a query final
            $query = "SELECT $colunasSQL FROM slow_logs " . (!empty($where) ? "WHERE " . implode(' AND ', $where) : '');
            $stmt = Connection::conectar()->prepare($query);
    
            // Associa os valores dos parâmetros
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
    
            $stmt->execute();
    
            // Retorna o SQL gerado e os dados
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