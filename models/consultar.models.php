<?php

require_once "connection.php";

class ModelsConsultar {
    static public function ConsultarLogs($colunas) {
        if (empty($colunas)) {
            return [];
        }

        $colunasSQL = implode(',', array_map(function ($coluna) {
            return "`$coluna`";
        }, $colunas));

        try {
            $stmt = Connection::conectar()->prepare("SELECT $colunasSQL FROM slow_logs WHERE insert_id > 0");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log de erro para depuração
            error_log("Erro na consulta SQL: " . $e->getMessage());
            return ["error" => $e->getMessage()];
        }
	}	

}