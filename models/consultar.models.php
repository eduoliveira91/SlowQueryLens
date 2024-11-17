<?php

require_once "connection.php";

class ModelsConsultar {
	
	static public function MostrarLogs($tabela, $item, $valor) {
		$stmt = Connection::conectar()->prepare("SELECT * FROM $tabela WHERE $item = :$item");
		$stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetch();
		$stmt->close();
		$stmt = null;
	}

	static public function ListaLogs() {
		$stmt = Connection::conectar()->prepare("SELECT * FROM slow_logs WHERE insert_id > 0");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->close();
		$stmt = null;
	}

}