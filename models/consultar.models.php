<?php

require_once "connection.php";

class ModelsConsultar
{
    static public function RetornaOpcoesMainTable()
    {
        try {
            $stmt = Connection::conectar()->prepare("SELECT DISTINCT main_table FROM slow_logs WHERE insert_id > 0 and main_table IS NOT NULL and main_table <> ''");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro na consulta SQL: " . $e->getMessage());
            return ["error" => $e->getMessage()];
        }
    }

    public static function RetornaIndices()
    {
        try {
            $stmt = Connection::conectar()->prepare("
                SHOW INDEX FROM slow_logs
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    static public function consultarLogs($colunas, $dataHoraInicio, $dataHoraFim, $filtroSQL, $filtroTabelaPrincipal)
    {
        if (empty($colunas)) {
            return [];
        }
    
        $colunasSQL = implode(',', array_map(function ($coluna) {
            return "`$coluna`";
        }, $colunas));
    
        $condicoes = [];
        $parametros = [];
    
        if (!empty($filtroSQL)) {
            $condicoes[] = $filtroSQL;
        } else {
            if (!empty($dataHoraInicio) && !empty($dataHoraFim)) {
                $condicoes[] = " (time BETWEEN :inicio AND :fim) ";
                $parametros[':inicio'] = $dataHoraInicio;
                $parametros[':fim'] = $dataHoraFim;
            }
    
            if (!empty($filtroTabelaPrincipal)) {
                $incluirNuloVazio = in_array('sem_informacao', $filtroTabelaPrincipal, true);
    
                $filtroTabelaPrincipal = array_map(function ($item) {
                    return $item === 'sem_informacao' ? '' : $item;
                }, $filtroTabelaPrincipal);
    
                $marcadores = [];
                foreach ($filtroTabelaPrincipal as $indice => $valor) {
                    if ($valor !== '') {
                        $chave = ":tabela" . $indice;
                        $marcadores[] = $chave;
                        $parametros[$chave] = $valor;
                    }
                }
    
                $partesClausula = [];
                if (!empty($marcadores)) {
                    $partesClausula[] = "main_table IN (" . implode(',', $marcadores) . ")";
                }
    
                if ($incluirNuloVazio) {
                    $partesClausula[] = "main_table IS NULL OR main_table = ''";
                }
    
                if (!empty($partesClausula)) {
                    $condicoes[] = "(" . implode(' OR ', $partesClausula) . ")";
                }
            }
        }
    
        try {
            $consulta = "SELECT $colunasSQL FROM slow_logs " . (!empty($condicoes) ? "WHERE " . implode(' AND ', $condicoes) : '');
            $stmt = Connection::conectar()->prepare($consulta);
    
            foreach ($parametros as $chave => $valor) {
                $stmt->bindValue($chave, $valor);
            }
    
            $stmt->execute();
    
            return [
                "sql" => $stmt->queryString,
                "dados" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Erro na consulta SQL: " . $e->getMessage());
            return ["error" => $e->getMessage()];
        }
    }
    
    


}